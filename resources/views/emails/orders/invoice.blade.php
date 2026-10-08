<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1e293b; background: #f8fafc; padding: 24px; margin: 0; }
        .container { max-width: 580px; margin: 0 auto; background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; padding: 32px; }
        .header { border-bottom: 1px solid #f1f5f9; padding-bottom: 20px; margin-bottom: 24px; }
        .logo { font-size: 18px; font-weight: bold; color: #0f172a; }
        .badge { display: inline-block; background: #ecfdf5; color: #047857; font-size: 12px; font-weight: 600; padding: 4px 10px; border-radius: 9999px; }
        .totals-card { background: #f8fafc; border-radius: 8px; padding: 16px; margin: 20px 0; border: 1px solid #e2e8f0; }
        .total-row { display: flex; justify-content: space-between; margin-bottom: 6px; font-size: 13px; }
        .grand-total { font-size: 16px; font-weight: bold; color: #0f172a; border-top: 1px solid #cbd5e1; padding-top: 8px; margin-top: 8px; }
        .footer { margin-top: 32px; font-size: 12px; color: #64748b; border-top: 1px solid #f1f5f9; padding-top: 16px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">ALO IT Consultants</div>
            <p style="margin: 4px 0 0; font-size: 13px; color: #64748b;">Mini POS &amp; Invoicing System</p>
        </div>

        <p><span class="badge">Payment Confirmed</span></p>
        <h2 style="margin: 12px 0; color: #0f172a;">Your Invoice is Ready</h2>
        <p>Dear {{ $customer->name }},</p>
        <p>Your sales order <strong>#{{ $order->id }}</strong> has been completed successfully and posted to our accounting system.</p>

        <div class="totals-card">
            <div class="total-row"><span>Order Reference:</span> <strong>#{{ $order->id }}</strong></div>
            <div class="total-row"><span>Date:</span> <strong>{{ $order->order_date->format('d M Y') }}</strong></div>
            <div class="total-row"><span>Subtotal:</span> <span>৳ {{ number_format((float) $order->subtotal, 2) }}</span></div>
            <div class="total-row"><span>Tax (5%):</span> <span>৳ {{ number_format((float) $order->tax, 2) }}</span></div>
            <div class="total-row grand-total"><span>Grand Total Paid:</span> <span>৳ {{ number_format((float) $order->grand_total, 2) }}</span></div>
        </div>

        <p>Your official tax invoice is attached to this email as a PDF document for your tax and bookkeeping records.</p>

        <div class="footer">
            <p style="margin: 0;">ALO IT Consultants · Dhaka, Bangladesh</p>
            <p style="margin: 4px 0 0;">This is an automated system email. For questions, contact sales@aloit.test</p>
        </div>
    </div>
</body>
</html>

