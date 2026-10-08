<?php

namespace App\Listeners;

use App\Events\OrderCompleted;
use App\Services\AccountingService;

class PostLedgerEntries
{
    public function __construct(private AccountingService $accountingService) {}

    public function handle(OrderCompleted $event): void
    {
        $this->accountingService->postSalesOrderToLedger($event->order);
    }
}
