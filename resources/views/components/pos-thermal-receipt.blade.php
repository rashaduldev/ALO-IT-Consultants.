@props(['order', 'invoiceNumber'])

<div id="thermal-receipt" class="mx-auto w-[80mm] max-w-[80mm] rounded-sm bg-white p-4 font-mono text-[11px] leading-tight text-slate-900 shadow-md">
    {{-- Header --}}
    <div class="text-center">
        <h2 class="text-sm font-bold uppercase tracking-wider text-slate-950">ALO IT CONSULTANTS</h2>
        <p class="text-[10px] text-slate-600">Mini POS & Retail System</p>
        <p class="text-[10px] text-slate-600">Dhaka, Bangladesh</p>
        <p class="mt-1 text-[10px]">TEL: +880 1700-000000</p>
    </div>

    <div class="my-2 border-b border-dashed border-slate-400"></div>

    {{-- Order Metadata --}}
    <div class="space-y-0.5 text-[10px]">
        <div class="flex justify-between">
            <span>Rcpt: {{ $invoiceNumber }}</span>
            <span>Ord: #{{ $order->id }}</span>
        </div>
        <div class="flex justify-between">
            <span>Date: {{ $order->order_date->format('d/m/Y') }}</span>
            <span>Time: {{ $order->created_at?->format('H:i') ?? now()->format('H:i') }}</span>
        </div>
        <div>Cust: {{ Str::limit($order->customer->name, 26) }}</div>
    </div>

    <div class="my-2 border-b border-dashed border-slate-400"></div>

    {{-- Line Items Table --}}
    <table class="w-full text-left text-[11px]">
        <thead>
            <tr class="border-b border-slate-300">
                <th class="py-1">Item</th>
                <th class="py-1 text-center">Qty</th>
                <th class="py-1 text-right">Price</th>
                <th class="py-1 text-right">Total</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-dashed divide-slate-200">
            @foreach ($order->items as $item)
                <tr>
                    <td class="py-1 pr-1 font-medium">{{ Str::limit($item->product_name, 16) }}</td>
                    <td class="py-1 text-center">{{ $item->quantity }}</td>
                    <td class="py-1 text-right">{{ number_format((float) $item->unit_price, 2) }}</td>
                    <td class="py-1 text-right font-bold">{{ number_format((float) $item->line_total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="my-2 border-b border-dashed border-slate-400"></div>

    {{-- Summary Totals --}}
    <div class="space-y-1 text-[11px]">
        <div class="flex justify-between">
            <span>Subtotal:</span>
            <span>৳ {{ number_format((float) $order->subtotal, 2) }}</span>
        </div>
        @if ((float) $order->discount > 0)
            <div class="flex justify-between text-emerald-700">
                <span>Discount:</span>
                <span>− ৳ {{ number_format((float) $order->discount, 2) }}</span>
            </div>
        @endif
        <div class="flex justify-between">
            <span>VAT (5%):</span>
            <span>৳ {{ number_format((float) $order->tax, 2) }}</span>
        </div>
        <div class="flex justify-between border-t border-slate-400 pt-1 text-xs font-black">
            <span>TOTAL:</span>
            <span>৳ {{ number_format((float) $order->grand_total, 2) }}</span>
        </div>
    </div>

    <div class="my-3 border-b border-dashed border-slate-400"></div>

    {{-- Barcode Simulation & Footer --}}
    <div class="text-center text-[10px]">
        <p class="font-bold tracking-widest text-emerald-700">*** PAID ***</p>
        <p class="mt-0.5 text-slate-500">Journal: {{ $order->journalEntry?->reference ?? 'POSTED' }}</p>
        <div class="my-2 flex justify-center">
            {{-- Code 128 / Barcode aesthetic placeholder --}}
            <div class="h-7 w-44 bg-slate-900 [mask-image:repeating-linear-gradient(90deg,#000_0px,#000_2px,transparent_2px,transparent_4px)]"></div>
        </div>
        <p class="text-[9px] text-slate-500">Thank you for your business!</p>
        <p class="text-[8px] text-slate-400">Software: ALO Enterprise POS</p>
    </div>
</div>

