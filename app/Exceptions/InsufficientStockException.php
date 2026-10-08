<?php

namespace App\Exceptions;

class InsufficientStockException extends OrderCompletionException
{
    /**
     * @param  array<int, string>  $unavailableProducts
     */
    public function __construct(array $unavailableProducts)
    {
        parent::__construct(
            'Order cannot be completed. Insufficient stock for: '.implode(', ', $unavailableProducts).'.',
        );
    }
}
