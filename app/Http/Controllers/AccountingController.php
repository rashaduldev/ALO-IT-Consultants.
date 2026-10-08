<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\Order;
use Illuminate\Contracts\View\View;

class AccountingController extends Controller
{
    public function index(): View
    {
        $accounts = Account::query()
            ->with('latestLedgerEntry')
            ->withSum('ledgerEntries as total_debit', 'debit')
            ->withSum('ledgerEntries as total_credit', 'credit')
            ->orderBy('code')
            ->get();

        $keyAccounts = $accounts->keyBy('code');
        $completedOrders = Order::query()->where('status', OrderStatus::Completed->value);

        $recentJournalEntries = JournalEntry::query()
            ->with(['order.customer', 'lines.account'])
            ->orderByDesc('entry_date')
            ->orderByDesc('id')
            ->limit(8)
            ->get();

        $journalTotals = \App\Models\JournalEntryLine::query()
            ->selectRaw('COALESCE(SUM(debit), 0) as total_debit, COALESCE(SUM(credit), 0) as total_credit')
            ->first();

        $auditTotalDebit = (float) ($journalTotals?->total_debit ?? 0);
        $auditTotalCredit = (float) ($journalTotals?->total_credit ?? 0);
        $auditTotalDebitCents = (int) round($auditTotalDebit * 100);
        $auditTotalCreditCents = (int) round($auditTotalCredit * 100);
        $discrepancyCents = abs($auditTotalDebitCents - $auditTotalCreditCents);
        $isLedgerBalanced = $discrepancyCents === 0;

        return view('accounting.index', [
            'accounts' => $accounts,
            'totalRevenue' => $keyAccounts->get('4000')?->latestLedgerEntry?->running_balance ?? '0.00',
            'totalReceivable' => $keyAccounts->get('1100')?->latestLedgerEntry?->running_balance ?? '0.00',
            'totalTaxPayable' => $keyAccounts->get('2100')?->latestLedgerEntry?->running_balance ?? '0.00',
            'completedOrderCount' => (clone $completedOrders)->count(),
            'pendingOrderCount' => Order::query()->where('status', OrderStatus::Pending->value)->count(),
            'todaySales' => (clone $completedOrders)->whereDate('order_date', today())->sum('grand_total'),
            'monthSales' => (clone $completedOrders)
                ->whereBetween('order_date', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()])
                ->sum('grand_total'),
            'recentJournalEntries' => $recentJournalEntries,
            'auditTotalDebit' => number_format($auditTotalDebit, 2),
            'auditTotalCredit' => number_format($auditTotalCredit, 2),
            'auditDiscrepancy' => number_format($discrepancyCents / 100, 2),
            'isLedgerBalanced' => $isLedgerBalanced,
        ]);
    }

    public function showAccount(Account $account): View
    {
        $ledgerEntries = $account->ledgerEntries()
            ->with(['journalEntry.order'])
            ->orderBy('journal_entry_id')
            ->orderBy('id')
            ->paginate(20);

        return view('accounting.ledger', [
            'account' => $account,
            'ledgerEntries' => $ledgerEntries,
        ]);
    }
}
