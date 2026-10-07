<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Account;
use App\Models\Customer;
use App\Models\JournalEntry;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class OrderWorkflowTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_order_creation_rejects_an_empty_payload(): void
    {
        $response = $this->post(route('orders.store'));

        $response->assertRedirect();
        $response->assertSessionHasErrors(['customer_id', 'order_date', 'items']);
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_order_creation_rejects_combined_quantity_that_exceeds_stock(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create(['stock_quantity' => 3]);

        $response = $this->from(route('orders.create'))->post(route('orders.store'), [
            'customer_id' => $customer->id,
            'order_date' => '2026-10-07',
            'discount' => '0.00',
            'items' => [
                ['product_id' => $product->id, 'quantity' => 2],
                ['product_id' => $product->id, 'quantity' => 2],
            ],
        ]);

        $response->assertRedirect(route('orders.create'));
        $response->assertSessionHasErrors([
            'items' => "Insufficient stock for: {$product->name} (requested: 4, available: 3).",
        ]);
        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock_quantity' => 3]);
    }

    public function test_order_creation_calculates_totals_and_preserves_stock(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create(['price' => '100.00', 'stock_quantity' => 10]);

        $response = $this->post(route('orders.store'), [
            'customer_id' => $customer->id,
            'order_date' => '2026-10-07',
            'discount' => '10.00',
            'items' => [
                ['product_id' => $product->id, 'quantity' => 2],
            ],
        ]);

        $response->assertRedirect(route('orders.index'));
        $response->assertSessionHas('success', 'Order #1 was created as pending.');
        $this->assertDatabaseHas('orders', [
            'customer_id' => $customer->id,
            'status' => OrderStatus::Pending->value,
            'subtotal' => '200.00',
            'discount' => '10.00',
            'tax' => '9.50',
            'grand_total' => '199.50',
        ]);
        $this->assertDatabaseHas('order_items', [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 2,
            'unit_price' => '100.00',
            'line_total' => '200.00',
        ]);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock_quantity' => 10]);
    }

    public function test_order_completion_deducts_stock_and_creates_a_balanced_journal(): void
    {
        $this->createRequiredAccounts();
        $product = Product::factory()->create(['price' => '100.00', 'stock_quantity' => 5]);
        $order = $this->createPendingOrder($product, 2, '10.00');

        $response = $this->post(route('orders.complete', $order));

        $response->assertRedirect(route('orders.show', $order));
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => OrderStatus::Completed->value]);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock_quantity' => 3]);
        $this->assertDatabaseCount('journal_entries', 1);
        $this->assertDatabaseCount('journal_entry_lines', 3);
        $this->assertDatabaseCount('ledger_entries', 3);

        $journalEntry = JournalEntry::query()->with('lines')->firstOrFail();

        $this->assertSame('199.50', number_format((float) $journalEntry->lines->sum('debit'), 2, '.', ''));
        $this->assertSame('199.50', number_format((float) $journalEntry->lines->sum('credit'), 2, '.', ''));
        $this->assertDatabaseHas('ledger_entries', ['account_id' => Account::query()->where('code', '1100')->value('id'), 'running_balance' => '199.50']);
        $this->assertDatabaseHas('ledger_entries', ['account_id' => Account::query()->where('code', '4000')->value('id'), 'running_balance' => '190.00']);
        $this->assertDatabaseHas('ledger_entries', ['account_id' => Account::query()->where('code', '2100')->value('id'), 'running_balance' => '9.50']);
    }

    public function test_order_completion_does_not_change_anything_when_stock_is_insufficient(): void
    {
        $this->createRequiredAccounts();
        $product = Product::factory()->create(['price' => '100.00', 'stock_quantity' => 1]);
        $order = $this->createPendingOrder($product, 2, '0.00');

        $response = $this->from(route('orders.show', $order))->post(route('orders.complete', $order));

        $response->assertRedirect(route('orders.show', $order));
        $response->assertSessionHas('error', "Order cannot be completed. Insufficient stock for: {$product->name} (requested: 2, available: 1).");
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => OrderStatus::Pending->value]);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock_quantity' => 1]);
        $this->assertDatabaseCount('journal_entries', 0);
        $this->assertDatabaseCount('ledger_entries', 0);
    }

    public function test_order_completion_is_blocked_after_the_first_successful_completion(): void
    {
        $this->createRequiredAccounts();
        $product = Product::factory()->create(['price' => '100.00', 'stock_quantity' => 5]);
        $order = $this->createPendingOrder($product, 2, '0.00');

        $this->post(route('orders.complete', $order))->assertRedirect(route('orders.show', $order));
        $response = $this->from(route('orders.show', $order))->post(route('orders.complete', $order));

        $response->assertRedirect(route('orders.show', $order));
        $response->assertSessionHas('error', "Order #{$order->id} has already been completed.");
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock_quantity' => 3]);
        $this->assertDatabaseCount('journal_entries', 1);
        $this->assertDatabaseCount('journal_entry_lines', 3);
        $this->assertDatabaseCount('ledger_entries', 3);
    }

    private function createRequiredAccounts(): void
    {
        Account::factory()->create(['code' => '1100', 'name' => 'Accounts Receivable', 'type' => 'asset']);
        Account::factory()->create(['code' => '2100', 'name' => 'Tax Payable', 'type' => 'liability']);
        Account::factory()->create(['code' => '4000', 'name' => 'Sales Revenue', 'type' => 'revenue']);
    }

    private function createPendingOrder(Product $product, int $quantity, string $discount): Order
    {
        $subtotal = $quantity * 100;
        $taxableAmount = $subtotal - (int) $discount;
        $tax = $taxableAmount * 0.05;
        $order = Order::factory()->for(Customer::factory())->create([
            'order_date' => '2026-10-07',
            'status' => OrderStatus::Pending,
            'subtotal' => number_format($subtotal, 2, '.', ''),
            'discount' => $discount,
            'tax' => number_format($tax, 2, '.', ''),
            'grand_total' => number_format($taxableAmount + $tax, 2, '.', ''),
        ]);

        $order->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => $quantity,
            'unit_price' => '100.00',
            'line_total' => number_format($subtotal, 2, '.', ''),
        ]);

        return $order;
    }
}
