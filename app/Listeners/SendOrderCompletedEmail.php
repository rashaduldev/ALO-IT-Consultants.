<?php

namespace App\Listeners;

use App\Events\OrderCompleted;
use App\Mail\OrderCompletedInvoice;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendOrderCompletedEmail implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(OrderCompleted $event): void
    {
        $order = $event->order;
        $order->loadMissing(['customer', 'items', 'journalEntry']);

        if (! empty($order->customer?->email)) {
            Mail::to($order->customer->email)->queue(new OrderCompletedInvoice($order));
        }
    }
}

