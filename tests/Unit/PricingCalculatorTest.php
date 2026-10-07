<?php

namespace Tests\Unit;

use App\Services\PricingCalculator;
use PHPUnit\Framework\TestCase;

class PricingCalculatorTest extends TestCase
{
    public function test_tax_is_rounded_to_two_decimal_places(): void
    {
        $totals = (new PricingCalculator)->calculate([
            ['quantity' => 1, 'unit_price' => '0.10'],
        ]);

        $this->assertSame('0.10', $totals['subtotal']);
        $this->assertSame('0.01', $totals['tax']);
        $this->assertSame('0.11', $totals['grand_total']);
    }
}
