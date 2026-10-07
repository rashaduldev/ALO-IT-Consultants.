<?php

namespace App\Http\Requests;

use App\Models\Product;
use App\Services\PricingCalculator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;
use Illuminate\Validation\Validator;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'order_date' => ['required', 'date'],
            'discount' => ['nullable', 'decimal:0,2', 'min:0'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $requestedQuantities = collect($this->input('items'))
                    ->groupBy('product_id')
                    ->map(fn (Collection $items): int => $items->sum('quantity'));

                $products = Product::query()
                    ->whereKey($requestedQuantities->keys())
                    ->get(['id', 'name', 'price', 'stock_quantity'])
                    ->keyBy('id');

                $unavailableProducts = $requestedQuantities
                    ->filter(fn (int $quantity, int|string $productId): bool => $quantity > $products->get($productId)->stock_quantity)
                    ->map(fn (int $quantity, int|string $productId): string => sprintf(
                        '%s (requested: %d, available: %d)',
                        $products->get($productId)->name,
                        $quantity,
                        $products->get($productId)->stock_quantity,
                    ));

                if ($unavailableProducts->isNotEmpty()) {
                    $validator->errors()->add('items', 'Insufficient stock for: '.$unavailableProducts->implode(', ').'.');

                    return;
                }

                try {
                    (new PricingCalculator)->calculate(
                        $requestedQuantities
                            ->map(fn (int $quantity, int|string $productId): array => [
                                'quantity' => $quantity,
                                'unit_price' => $products->get($productId)->price,
                            ])
                            ->values()
                            ->all(),
                        $this->input('discount', 0),
                    );
                } catch (\InvalidArgumentException $exception) {
                    $validator->errors()->add('discount', $exception->getMessage());
                }
            },
        ];
    }
}
