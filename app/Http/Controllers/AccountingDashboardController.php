<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\LedgerEntry;
use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AccountingDashboardController extends Controller
{
    /**
     * Display the Executive Accounting Overview Dashboard with Chart.js analytics.
     */
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

        // Global Journal Audit Parity Check
        $journalTotals = JournalEntryLine::query()
            ->selectRaw('COALESCE(SUM(debit), 0) as total_debit, COALESCE(SUM(credit), 0) as total_credit')
            ->first();

        $auditTotalDebit = (float) ($journalTotals?->total_debit ?? 0);
        $auditTotalCredit = (float) ($journalTotals?->total_credit ?? 0);
        $auditTotalDebitCents = (int) round($auditTotalDebit * 100);
        $auditTotalCreditCents = (int) round($auditTotalCredit * 100);
        $discrepancyCents = abs($auditTotalDebitCents - $auditTotalCreditCents);
        $isLedgerBalanced = $discrepancyCents === 0;

        // KPI Metric Values
        $totalRevenue = (float) ($keyAccounts->get('4000')?->latestLedgerEntry?->running_balance ?? 0);
        $totalReceivable = (float) ($keyAccounts->get('1100')?->latestLedgerEntry?->running_balance ?? 0);
        $totalTaxPayable = (float) ($keyAccounts->get('2100')?->latestLedgerEntry?->running_balance ?? 0);
        $netOperatingSurplus = max(0, $totalRevenue - $totalTaxPayable);

        // Chart Data: Monthly Revenue vs Tax (Past 6 Months)
        $monthlyLabels = [];
        $monthlyRevenue = [];
        $monthlyTax = [];

        for ($i = 5; $i >= 0; $i--) {
            $monthDate = now()->subMonths($i);
            $monthKey = $monthDate->format('M Y');
            $monthlyLabels[] = $monthKey;

            $monthOrders = Order::query()
                ->where('status', OrderStatus::Completed->value)
                ->whereYear('order_date', $monthDate->year)
                ->whereMonth('order_date', $monthDate->month);

            $monthlyRevenue[] = (float) (clone $monthOrders)->sum('subtotal');
            $monthlyTax[] = (float) (clone $monthOrders)->sum('tax');
        }

        // Chart Data: Account Balance Breakdown by Type
        $assetBalance = (float) $accounts->where('type', 'asset')->sum(fn (Account $a) => (float) ($a->latestLedgerEntry?->running_balance ?? 0));
        $liabilityBalance = (float) $accounts->where('type', 'liability')->sum(fn (Account $a) => (float) ($a->latestLedgerEntry?->running_balance ?? 0));
        $revenueBalance = (float) $accounts->where('type', 'revenue')->sum(fn (Account $a) => (float) ($a->latestLedgerEntry?->running_balance ?? 0));
        $equityBalance = (float) $accounts->where('type', 'equity')->sum(fn (Account $a) => (float) ($a->latestLedgerEntry?->running_balance ?? 0));

        // Recent Journal Postings
        $recentJournalEntries = JournalEntry::query()
            ->with(['order.customer', 'lines.account'])
            ->orderByDesc('entry_date')
            ->orderByDesc('id')
            ->limit(7)
            ->get();

        return view('accounting.index', [
            'accounts' => $accounts,
            'totalRevenue' => number_format($totalRevenue, 2),
            'totalReceivable' => number_format($totalReceivable, 2),
            'totalTaxPayable' => number_format($totalTaxPayable, 2),
            'netOperatingSurplus' => number_format($netOperatingSurplus, 2),
            'completedOrderCount' => (clone $completedOrders)->count(),
            'pendingOrderCount' => Order::query()->where('status', OrderStatus::Pending->value)->count(),
            'monthlyLabels' => $monthlyLabels,
            'monthlyRevenue' => $monthlyRevenue,
            'monthlyTax' => $monthlyTax,
            'assetBalance' => $assetBalance,
            'liabilityBalance' => $liabilityBalance,
            'revenueBalance' => $revenueBalance,
            'equityBalance' => $equityBalance,
            'recentJournalEntries' => $recentJournalEntries,
            'auditTotalDebit' => number_format($auditTotalDebit, 2),
            'auditTotalCredit' => number_format($auditTotalCredit, 2),
            'auditDiscrepancy' => number_format($discrepancyCents / 100, 2),
            'isLedgerBalanced' => $isLedgerBalanced,
        ]);
    }

    /**
     * Display the Complete General Ledger Table with debits/credits.
     */
    public function ledger(Request $request): View
    {
        $selectedAccountCode = $request->string('account')->toString();

        $accounts = Account::query()->orderBy('code')->get();

        $ledgerEntriesQuery = LedgerEntry::query()
            ->with(['account', 'journalEntry.order.customer'])
            ->when($selectedAccountCode, function ($query, $code): void {
                $query->whereHas('account', fn ($q) => $q->where('code', $code));
            })
            ->orderByDesc('id');

        $ledgerEntries = $ledgerEntriesQuery->paginate(25)->withQueryString();

        $totalDebits = (float) LedgerEntry::query()
            ->when($selectedAccountCode, fn ($q) => $q->whereHas('account', fn ($acc) => $acc->where('code', $selectedAccountCode)))
            ->sum('debit');

        $totalCredits = (float) LedgerEntry::query()
            ->when($selectedAccountCode, fn ($q) => $q->whereHas('account', fn ($acc) => $acc->where('code', $selectedAccountCode)))
            ->sum('credit');

        return view('accounting.ledger', [
            'accounts' => $accounts,
            'selectedAccountCode' => $selectedAccountCode,
            'ledgerEntries' => $ledgerEntries,
            'totalDebits' => number_format($totalDebits, 2),
            'totalCredits' => number_format($totalCredits, 2),
        ]);
    }

    /**
     * Display Sales Orders & Accounting Transaction List.
     */
    public function orders(Request $request): View
    {
        $status = $request->string('status')->toString();

        $orders = Order::query()
            ->with(['customer', 'items', 'journalEntry'])
            ->when($status, fn ($query) => $query->where('status', $status))
            ->orderByDesc('order_date')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $totalSales = (float) Order::query()
            ->where('status', OrderStatus::Completed->value)
            ->sum('grand_total');

        return view('accounting.orders', [
            'orders' => $orders,
            'selectedStatus' => $status,
            'totalSales' => number_format($totalSales, 2),
        ]);
    }

    /**
     * Display Tax & Revenue Financial Reports.
     */
    public function reports(): View
    {
        $completedOrders = Order::query()->where('status', OrderStatus::Completed->value);

        $grossSales = (float) (clone $completedOrders)->sum('subtotal');
        $totalDiscounts = (float) (clone $completedOrders)->sum('discount');
        $taxableSales = max(0, $grossSales - $totalDiscounts);
        $taxCollected = (float) (clone $completedOrders)->sum('tax');
        $netGrandTotal = (float) (clone $completedOrders)->sum('grand_total');

        // Monthly Tax Breakdown for current year
        $ordersThisYear = Order::query()
            ->where('status', OrderStatus::Completed->value)
            ->whereYear('order_date', now()->year)
            ->get();

        $monthlyReport = $ordersThisYear
            ->groupBy(fn (Order $order) => (int) $order->order_date->format('n'))
            ->sortKeys()
            ->map(fn ($orders, $month) => [
                'month_name' => Carbon::create(now()->year, $month, 1)->format('F Y'),
                'subtotal' => number_format((float) $orders->sum('subtotal'), 2),
                'discount' => number_format((float) $orders->sum('discount'), 2),
                'tax' => number_format((float) $orders->sum('tax'), 2),
                'grand_total' => number_format((float) $orders->sum('grand_total'), 2),
                'count' => $orders->count(),
            ])
            ->values();

        return view('accounting.reports', [
            'grossSales' => number_format($grossSales, 2),
            'totalDiscounts' => number_format($totalDiscounts, 2),
            'taxableSales' => number_format($taxableSales, 2),
            'taxCollected' => number_format($taxCollected, 2),
            'netGrandTotal' => number_format($netGrandTotal, 2),
            'monthlyReport' => $monthlyReport,
        ]);
    }

    /**
     * Drill down into an individual account's ledger entries.
     */
    public function showAccount(Account $account): View
    {
        $ledgerEntries = $account->ledgerEntries()
            ->with(['journalEntry.order'])
            ->orderBy('journal_entry_id')
            ->orderBy('id')
            ->paginate(25);

        return view('accounting.account-show', [
            'account' => $account,
            'ledgerEntries' => $ledgerEntries,
        ]);
    }
}
