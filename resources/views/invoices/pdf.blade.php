<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $invoiceNumber }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 11px; line-height: 1.4; color: #1e293b; margin: 0; padding: 20px; }
        .table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        .table th { background: #f8fafc; padding: 8px; font-size: 10px; font-weight: bold; text-transform: uppercase; color: #475569; border-bottom: 2px solid #cbd5e1; }
        .table td { padding: 8px; border-bottom: 1px solid #e2e8f0; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .badge { display: inline-block; background: #ecfdf5; color: #047857; font-size: 10px; font-weight: bold; padding: 2px 8px; border-radius: 4px; }
        .totals-table { width: 45%; margin-left: 55%; margin-top: 16px; border-collapse: collapse; }
        .totals-table td { padding: 4px 8px; }
        .totals-table .grand-total { font-size: 13px; font-weight: bold; color: #0f172a; border-top: 2px solid #0f172a; }
        .footer { margin-top: 32px; border-top: 1px solid #e2e8f0; padding-top: 12px; font-size: 10px; color: #64748b; }
    </style>
</head>
<body>
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="vertical-align: top;">
                <h1 style="margin: 0; font-size: 20px; color: #0f172a;">ALO IT Consultants</h1>
                <p style="margin: 2px 0 0; color: #64748b;">Mini POS &amp; Invoicing System</p>
                <p style="margin: 4px 0 0; color: #64748b;">Dhaka, Bangladesh · sales@aloit.test</p>
            </td>
            <td class="text-right" style="vertical-align: top;">
                <span class="badge">TAX INVOICE</span>
                <h2 style="margin: 4px 0 0; font-size: 18px; color: #0f172a;">{{ $invoiceNumber }}</h2>
                <p style="margin: 2px 0 0; color: #64748b;">Date: {{ $order->order_date->format('d M Y') }}</p>
                <p style="margin: 2px 0 0; color: #64748b;">Ref: #{{ $order->id }}</p>
            </td>
        </tr>
    </table>

    <div style="margin-top: 24px; padding: 12px; background: #f8fafc; border-radius: 6px; border: 1px solid #e2e8f0;">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="width: 50%; vertical-align: top;">
                    <p style="margin: 0; font-size: 9px; text-transform: uppercase; font-weight: bold; color: #64748b;">Bill To:</p>
                    <p style="margin: 4px 0 0; font-size: 12px; font-weight: bold; color: #0f172a;">{{ $order->customer->name }}</p>
                    @if ($order->customer->email)<p style="margin: 2px 0 0; color: #475569;">{{ $order->customer->email }}</p>@endif
                    @if ($order->customer->phone)<p style="margin: 2px 0 0; color: #475569;">{{ $order->customer->phone }}</p>@endif
                    @if ($order->customer->address)<p style="margin: 2px 0 0; color: #475569;">{{ $order->customer->address }}</p>@endif
                </td>
                <td class="text-right" style="vertical-align: top;">
                    <p style="margin: 0; font-size: 9px; text-transform: uppercase; font-weight: bold; color: #64748b;">Accounting Status:</p>
                    <p style="margin: 4px 0 0; font-weight: bold; color: #0f172a;">Double-Entry Ledger Posted</p>
                    <p style="margin: 2px 0 0; color: #475569;">Journal Ref: {{ $order->journalEntry?->reference ?? 'POSTED' }}</p>
                </td>
            </tr>
        </table>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Item Description</th>
                <th class="text-right">Unit Price</th>
                <th class="text-center" style="width: 60px;">Qty</th>
                <th class="text-right" style="width: 100px;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
                <tr>
                    <td style="font-weight: 500;">{{ $item->product_name }}</td>
                    <td class="text-right">৳ {{ number_format((float) $item->unit_price, 2) }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right" style="font-weight: bold;">৳ {{ number_format((float) $item->line_total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals-table">
        <tr>
            <td style="color: #64748b;">Subtotal:</td>
            <td class="text-right">৳ {{ number_format((float) $order->subtotal, 2) }}</td>
        </tr>
        @if ((float) $order->discount > 0)
            <tr>
                <td style="color: #047857;">Discount:</td>
                <td class="text-right" style="color: #047857;">− ৳ {{ number_format((float) $order->discount, 2) }}</td>
            </tr>
        @endif
        <tr>
            <td style="color: #64748b;">Tax (5% VAT):</td>
            <td class="text-right">৳ {{ number_format((float) $order->tax, 2) }}</td>
        </tr>
        <tr class="grand-total">
            <td>Grand Total:</td>
            <td class="text-right">৳ {{ number_format((float) $order->grand_total, 2) }}</td>
        </tr>
    </table>

    <div class="footer">
        <p style="margin: 0;">Computer generated tax invoice issued by ALO IT Consultants.</p>
        <p style="margin: 2px 0 0;">Thank you for your business!</p>
    </div>
</body>
</html>

