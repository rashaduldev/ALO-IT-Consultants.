<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderCompletedInvoice extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Number of retry attempts on queue failure.
     */
    public int $tries = 3;

    public function __construct(
        public readonly Order $order,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Tax Invoice #INV-{$this->order->id} - ALO IT Consultants",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.orders.invoice',
            with: [
                'order' => $this->order,
                'customer' => $this->order->customer,
            ],
        );
    }

    /**
     * Compile PDF in-memory and attach to the email payload.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        $this->order->loadMissing(['customer', 'items', 'journalEntry']);
        $invoiceNumber = sprintf('INV-%s-%04d', $this->order->order_date->format('Ymd'), $this->order->id);

        /** @phpstan-ignore-next-line */
        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            /** @var \Barryvdh\DomPDF\PDF $pdf */
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('invoices.pdf', [
                'order' => $this->order,
                'invoiceNumber' => $invoiceNumber,
            ])->setPaper('a4', 'portrait');

            return [
                Attachment::fromData(fn () => $pdf->output(), "{$invoiceNumber}.pdf")
                    ->withMime('application/pdf'),
            ];
        }

        // Resilient fallback when DomPDF package is not yet pulled in offline environment
        $htmlContent = view('invoices.pdf', [
            'order' => $this->order,
            'invoiceNumber' => $invoiceNumber,
        ])->render();

        return [
            Attachment::fromData(fn () => $htmlContent, "{$invoiceNumber}.html")
                ->withMime('text/html'),
        ];
    }
}

