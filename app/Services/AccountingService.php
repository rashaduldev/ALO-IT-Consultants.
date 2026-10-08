<?php

namespace App\Services;

use App\Exceptions\UnbalancedJournalEntryException;
use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\LedgerEntry;
use App\Models\Order;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AccountingService
{
    public function postSalesOrderToLedger(Order $order): JournalEntry
    {
        return DB::transaction(function () use ($order): JournalEntry {
            $accounts = Account::query()
                ->whereIn('code', ['1100', '2100', '4000'])
                ->orderBy('code')
                ->lockForUpdate()
                ->get()
                ->keyBy('code');

            $this->ensureRequiredAccountsExist($accounts);

            $journalLines = [
                ['account' => $accounts->get('1100'), 'debit' => $order->grand_total, 'credit' => '0.00'],
                ['account' => $accounts->get('4000'), 'debit' => '0.00', 'credit' => $this->subtract($order->subtotal, $order->discount)],
                ['account' => $accounts->get('2100'), 'debit' => '0.00', 'credit' => $order->tax],
            ];

            $this->assertJournalIsBalanced($journalLines);

            $journalEntry = JournalEntry::query()->create([
                'order_id' => $order->id,
                'entry_date' => $order->order_date,
                'description' => "Sales order #{$order->id}",
                'reference' => "ORDER-{$order->id}",
            ]);

            foreach ($journalLines as $journalLine) {
                $line = $journalEntry->lines()->create([
                    'account_id' => $journalLine['account']->id,
                    'debit' => $journalLine['debit'],
                    'credit' => $journalLine['credit'],
                ]);

                LedgerEntry::query()->create([
                    'account_id' => $journalLine['account']->id,
                    'journal_entry_id' => $journalEntry->id,
                    'journal_entry_line_id' => $line->id,
                    'debit' => $journalLine['debit'],
                    'credit' => $journalLine['credit'],
                    'running_balance' => $this->nextRunningBalance(
                        $journalLine['account'],
                        $journalLine['debit'],
                        $journalLine['credit'],
                    ),
                ]);
            }

            return $journalEntry->load(['lines.account', 'ledgerEntries']);
        });
    }

    /**
     * @param  Collection<string, Account>  $accounts
     */
    private function ensureRequiredAccountsExist(Collection $accounts): void
    {
        $missingCodes = collect(['1100', '2100', '4000'])->diff($accounts->keys());

        if ($missingCodes->isNotEmpty()) {
            throw new UnbalancedJournalEntryException(
                'Required chart-of-account codes are missing: '.$missingCodes->implode(', ').'.',
            );
        }
    }

    /**
     * @param  array<int, array{account: Account, debit: string, credit: string}>  $journalLines
     */
    private function assertJournalIsBalanced(array $journalLines): void
    {
        $totalDebitCents = collect($journalLines)->sum(fn (array $line): int => $this->toCents($line['debit']));
        $totalCreditCents = collect($journalLines)->sum(fn (array $line): int => $this->toCents($line['credit']));

        if ($totalDebitCents !== $totalCreditCents) {
            throw new UnbalancedJournalEntryException(
                "Journal entry is unbalanced: debit {$this->fromCents($totalDebitCents)} does not equal credit {$this->fromCents($totalCreditCents)}.",
            );
        }
    }

    private function nextRunningBalance(Account $account, string $debit, string $credit): string
    {
        $previousBalanceCents = $this->toCents(
            LedgerEntry::query()
                ->whereBelongsTo($account)
                ->latest('id')
                ->value('running_balance') ?? '0.00',
        );

        $debitCents = $this->toCents($debit);
        $creditCents = $this->toCents($credit);
        $changeCents = in_array($account->type, ['asset', 'expense'], true)
            ? $debitCents - $creditCents
            : $creditCents - $debitCents;

        return $this->fromCents($previousBalanceCents + $changeCents);
    }

    private function subtract(string $minuend, string $subtrahend): string
    {
        return $this->fromCents($this->toCents($minuend) - $this->toCents($subtrahend));
    }

    private function toCents(string $amount): int
    {
        $normalizedAmount = number_format((float) $amount, 2, '.', '');
        [$whole, $fraction] = explode('.', $normalizedAmount);

        return ((int) $whole * 100) + ((int) $fraction * ($whole[0] === '-' ? -1 : 1));
    }

    private function fromCents(int $amount): string
    {
        return number_format($amount / 100, 2, '.', '');
    }
}
