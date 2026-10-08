<?php

namespace App\Listeners;

use App\Events\OrderCompleted;
use App\Services\AccountingService;

class PostLedgerEntries
{
    public function __construct(private AccountingService $accountingService) {}

    public function handle(OrderCompleted $event): void
    {
        $journalEntry = $this->accountingService->postSalesOrderToLedger($event->order);

        \App\Services\AuditLogger::log($event->order, 'ledger_posted', "Balanced double-entry journal {$journalEntry->reference} posted.", [
            'journal_id' => $journalEntry->id,
            'reference' => $journalEntry->reference,
        ]);
    }
}
