<?php

namespace App\Services;

use InvalidArgumentException;

class PricingCalculator
{
    /**
     * @param  array<int, array{quantity: int, unit_price: int|float|string}>  $items
     * @return array{subtotal: string, discount: string, taxable: string, tax: string, grand_total: string, line_totals: array<int, string>}
     */
    public function calculate(array $items, int|float|string|null $discount = 0): array
    {
        $subtotalCents = 0;
        $lineTotals = [];

        foreach ($items as $item) {
            if ($item['quantity'] < 1) {
                throw new InvalidArgumentException('Each item quantity must be at least one.');
            }

            $unitPriceCents = $this->toCents($item['unit_price']);

            if ($unitPriceCents < 0) {
                throw new InvalidArgumentException('Unit prices cannot be negative.');
            }

            $lineTotalCents = $item['quantity'] * $unitPriceCents;
            $subtotalCents += $lineTotalCents;
            $lineTotals[] = $this->fromCents($lineTotalCents);
        }

        $discountCents = $this->toCents($discount ?? 0);

        if ($discountCents < 0 || $discountCents > $subtotalCents) {
            throw new InvalidArgumentException('Discount must be between zero and the order subtotal.');
        }

        $taxableCents = $subtotalCents - $discountCents;
        $taxCents = intdiv(($taxableCents * 5) + 50, 100);

        return [
            'subtotal' => $this->fromCents($subtotalCents),
            'discount' => $this->fromCents($discountCents),
            'taxable' => $this->fromCents($taxableCents),
            'tax' => $this->fromCents($taxCents),
            'grand_total' => $this->fromCents($taxableCents + $taxCents),
            'line_totals' => $lineTotals,
        ];
    }

    private function toCents(int|float|string $amount): int
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
