<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use Database\Seeders\AccountSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountingDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AccountSeeder::class);
    }

    public function test_overview_dashboard_loads_successfully_with_audit_metrics(): void
    {
        $response = $this->get(route('accounting.index'));

        $response->assertOk();
        $response->assertSee('Accounting Overview');
        $response->assertSee('General Ledger Integrity Audit');
        $response->assertSee('Ledger Audit Status: BALANCED');
        $response->assertSee('monthlyRevenueChart');
        $response->assertSee('accountBreakdownChart');
    }

    public function test_general_ledger_page_loads_and_filters_by_account(): void
    {
        $response = $this->get(route('accounting.ledger'));

        $response->assertOk();
        $response->assertSee('General Ledger Postings');
        $response->assertSee('Total Filtered Debits');

        $arAccount = Account::where('code', '1100')->firstOrFail();
        $filteredResponse = $this->get(route('accounting.ledger', ['account' => $arAccount->code]));

        $filteredResponse->assertOk();
        $filteredResponse->assertSee($arAccount->name);
    }

    public function test_sales_orders_page_loads_and_filters_by_status(): void
    {
        $response = $this->get(route('accounting.orders'));

        $response->assertOk();
        $response->assertSee('Sales Orders & Invoices');
        $response->assertSee('Commercial Audit');

        $pendingResponse = $this->get(route('accounting.orders', ['status' => 'pending']));
        $pendingResponse->assertOk();
    }

    public function test_tax_and_revenue_reports_page_loads(): void
    {
        $response = $this->get(route('accounting.reports'));

        $response->assertOk();
        $response->assertSee('Tax & Revenue Compliance Report');
        $response->assertSee('5% VAT Collected');
        $response->assertSee('Monthly Statutory Filing Schedule');
    }

    public function test_single_account_drilldown_statement_loads(): void
    {
        $account = Account::where('code', '4000')->firstOrFail();

        $response = $this->get(route('accounting.accounts.show', $account));

        $response->assertOk();
        $response->assertSee($account->name);
        $response->assertSee('Ledger Activity Register');
        $response->assertSee('Normal Balance Nature');
    }

    public function test_completed_order_updates_accounting_dashboard_metrics(): void
    {
        $customer = Customer::create([
            'name' => 'Enterprise Corp',
            'email' => 'corp@enterprise.test',
            'phone' => '+8801700000000',
        ]);

        $product = Product::create([
            'name' => 'Premium Server Rack',
            'sku' => 'SR-001',
            'price' => 1000.00,
            'stock_quantity' => 10,
        ]);

        $orderService = app(OrderService::class);
        $order = $orderService->createOrder([
            'customer_id' => $customer->id,
            'order_date' => now()->toDateString(),
            'discount' => 100.00,
            'items' => [
                ['product_id' => $product->id, 'quantity' => 2],
            ],
        ]);

        // Complete the order
        $orderService->completeOrder($order);

        $response = $this->get(route('accounting.index'));

        $response->assertOk();
        // Subtotal = 2000, Discount = 100, Taxable = 1900, Tax = 95, Grand Total = 1995
        $response->assertSee('1,900.00'); // Revenue
        $response->assertSee('95.00'); // Tax
        $response->assertSee('1,995.00'); // Accounts Receivable
        $response->assertSee('Ledger Audit Status: BALANCED');
    }
}
