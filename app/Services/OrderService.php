<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function __construct(private PricingCalculator $pricingCalculator) {}

    /**
     * @param  array{customer_id: int, order_date: string, discount?: int|float|string|null, items: array<int, array{product_id: int, quantity: int}>}  $validatedOrder
     */
    public function createOrder(array $validatedOrder): Order
    {
        return DB::transaction(function () use ($validatedOrder): Order {
            $requestedQuantities = collect($validatedOrder['items'])
                ->groupBy('product_id')
                ->map(fn (Collection $items): int => $items->sum('quantity'));

            $products = Product::query()
                ->whereKey($requestedQuantities->keys())
                ->get()
                ->keyBy('id');

            $this->ensureStockIsAvailable($products, $requestedQuantities);

            $orderItems = collect($validatedOrder['items'])->map(
                fn (array $item): array => [
                    'product' => $products->get($item['product_id']),
                    'quantity' => (int) $item['quantity'],
                ],
            );

            $pricing = $this->pricingCalculator->calculate(
                $orderItems
                    ->map(fn (array $item): array => [
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['product']->price,
                    ])
                    ->all(),
                $validatedOrder['discount'] ?? 0,
            );

            $order = Order::query()->create([
                'customer_id' => $validatedOrder['customer_id'],
                'order_date' => $validatedOrder['order_date'],
                'status' => OrderStatus::Pending,
                'subtotal' => $pricing['subtotal'],
                'discount' => $pricing['discount'],
                'tax' => $pricing['tax'],
                'grand_total' => $pricing['grand_total'],
            ]);

            foreach ($orderItems as $index => $item) {
                $order->items()->create([
                    'product_id' => $item['product']->id,
                    'product_name' => $item['product']->name,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['product']->price,
                    'line_total' => $pricing['line_totals'][$index],
                ]);
            }

            return $order->load(['customer', 'items.product']);
        });
    }

    /**
     * @param  Collection<int, Product>  $products
     * @param  Collection<int, int>  $requestedQuantities
     */
    private function ensureStockIsAvailable(Collection $products, Collection $requestedQuantities): void
    {
        $unavailableProducts = $requestedQuantities
            ->filter(fn (int $quantity, int|string $productId): bool => ! $products->has($productId) || $quantity > $products->get($productId)->stock_quantity)
            ->map(function (int $quantity, int|string $productId) use ($products): string {
                $product = $products->get($productId);

                return sprintf(
                    '%s (requested: %d, available: %d)',
                    $product?->name ?? "Product #{$productId}",
                    $quantity,
                    $product?->stock_quantity ?? 0,
                );
            });

        if ($unavailableProducts->isNotEmpty()) {
            throw ValidationException::withMessages([
                'items' => 'Insufficient stock for: '.$unavailableProducts->implode(', ').'.',
            ]);
        }
    }
}
