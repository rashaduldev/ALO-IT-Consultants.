<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Contracts\View\View;

class InvoiceController extends Controller
{
    public function show(Order $order): View
    {
        $order->load([
            'customer',
            'items.product',
            'journalEntry.lines.account',
        ]);

        return view('invoices.show', [
            'order' => $order,
            'isCompleted' => $order->status === OrderStatus::Completed,
            'invoiceNumber' => 'INV-'.str_pad((string) $order->id, 6, '0', STR_PAD_LEFT),
        ]);
    }
}
