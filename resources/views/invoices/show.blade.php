<x-layouts.app title="{{ $invoiceNumber }} · ALO POS">
    <style>
        @media print {
            header,
            .print-hidden {
                display: none !important;
            }

            body {
                background: #ffffff !important;
            }

            main {
                max-width: none !important;
                padding: 0 !important;
            }

            .invoice-sheet {
                border: 0 !important;
                box-shadow: none !important;
            }
        }
    </style>

    @if (! $isCompleted)
        <section class="mx-auto max-w-2xl rounded-2xl border border-amber-200 bg-amber-50 p-6 text-center shadow-sm">
            <span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800">Pending order</span>
            <h1 class="mt-4 text-2xl font-bold tracking-tight text-amber-950">Invoice unavailable until completion</h1>
            <p class="mt-2 text-sm leading-6 text-amber-800">Order #{{ $order->id }} must be completed before an invoice and its accounting entry can be issued.</p>
            <a href="{{ route('orders.show', $order) }}" class="print-hidden mt-6 inline-flex rounded-lg bg-amber-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-amber-800">Return to order</a>
        </section>
    @else
        <div class="print-hidden mb-6 flex justify-end gap-3">
            <a href="{{ route('orders.show', $order) }}" class="rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Back to order</a>
            <button type="button" onclick="window.print()" class="rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700">Print invoice</button>
        </div>

        <article class="invoice-sheet mx-auto max-w-4xl rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-10">
            <header class="flex flex-col gap-6 border-b border-slate-200 pb-8 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <div class="flex items-center gap-3">
                        <span class="grid size-11 place-items-center rounded-xl bg-indigo-600 text-base font-bold text-white">AP</span>
                        <div>
                            <p class="text-lg font-bold text-slate-950">ALO IT Consultants</p>
                            <p class="text-sm text-slate-600">Mini POS &amp; Sales System</p>
                        </div>
                    </div>
                    <p class="mt-5 text-sm leading-6 text-slate-600">Dhaka, Bangladesh<br>sales@aloit.test</p>
                </div>
                <div class="sm:text-right">
                    <p class="text-sm font-semibold uppercase tracking-wider text-indigo-600">Tax invoice</p>
                    <h1 class="mt-1 text-3xl font-bold tracking-tight text-slate-950">{{ $invoiceNumber }}</h1>
                    <span class="mt-3 inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-800">Completed</span>
                    <p class="mt-3 text-sm text-slate-600">Invoice date: {{ $order->order_date->format('d M Y') }}</p>
                </div>
            </header>

            <section class="grid gap-6 border-b border-slate-200 py-8 sm:grid-cols-2">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Bill to</p>
                    <p class="mt-2 font-semibold text-slate-950">{{ $order->customer->name }}</p>
                    @if ($order->customer->address)
                        <p class="mt-1 whitespace-pre-line text-sm leading-6 text-slate-600">{{ $order->customer->address }}</p>
                    @endif
                    @if ($order->customer->email)
                        <p class="mt-1 text-sm text-slate-600">{{ $order->customer->email }}</p>
                    @endif
                    @if ($order->customer->phone)
                        <p class="mt-1 text-sm text-slate-600">{{ $order->customer->phone }}</p>
                    @endif
                </div>
                <div class="sm:text-right">
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Order reference</p>
                    <p class="mt-2 font-semibold text-slate-950">Order #{{ $order->id }}</p>
                    <p class="mt-1 text-sm text-slate-600">Order date: {{ $order->order_date->format('d M Y') }}</p>
                    <p class="mt-1 text-sm text-slate-600">Journal ref: {{ $order->journalEntry?->reference }}</p>
                </div>
            </section>

            <section class="py-8">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="border-y border-slate-200 bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-4 py-3">Item</th>
                                <th class="px-4 py-3 text-right">Unit price</th>
                                <th class="px-4 py-3 text-right">Qty</th>
                                <th class="px-4 py-3 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($order->items as $item)
                                <tr>
                                    <td class="px-4 py-4 font-medium text-slate-900">{{ $item->product_name }}</td>
                                    <td class="whitespace-nowrap px-4 py-4 text-right text-slate-700">৳ {{ number_format((float) $item->unit_price, 2) }}</td>
                                    <td class="px-4 py-4 text-right text-slate-700">{{ $item->quantity }}</td>
                                    <td class="whitespace-nowrap px-4 py-4 text-right font-semibold text-slate-900">৳ {{ number_format((float) $item->line_total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <dl class="ml-auto mt-6 max-w-xs space-y-3 text-sm">
                    <div class="flex justify-between gap-6"><dt class="text-slate-600">Subtotal</dt><dd class="font-medium text-slate-900">৳ {{ number_format((float) $order->subtotal, 2) }}</dd></div>
                    <div class="flex justify-between gap-6"><dt class="text-slate-600">Discount</dt><dd class="font-medium text-slate-900">− ৳ {{ number_format((float) $order->discount, 2) }}</dd></div>
                    <div class="flex justify-between gap-6"><dt class="text-slate-600">Tax (5%)</dt><dd class="font-medium text-slate-900">৳ {{ number_format((float) $order->tax, 2) }}</dd></div>
                    <div class="flex justify-between gap-6 border-t border-slate-300 pt-3"><dt class="font-semibold text-slate-950">Grand total</dt><dd class="text-lg font-bold text-indigo-700">৳ {{ number_format((float) $order->grand_total, 2) }}</dd></div>
                </dl>
            </section>

            <section class="border-t border-slate-200 pt-8">
                <div class="flex items-end justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Accounting breakdown</p>
                        <h2 class="mt-1 text-lg font-semibold text-slate-950">Journal entry</h2>
                    </div>
                    <span class="text-sm text-slate-600">{{ $order->journalEntry?->entry_date?->format('d M Y') }}</span>
                </div>
                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="border-y border-slate-200 bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-4 py-3">Account</th>
                                <th class="px-4 py-3 text-right">Debit</th>
                                <th class="px-4 py-3 text-right">Credit</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($order->journalEntry?->lines ?? [] as $line)
                                <tr>
                                    <td class="px-4 py-4"><span class="font-medium text-slate-900">{{ $line->account->name }}</span><span class="ml-2 text-slate-500">{{ $line->account->code }}</span></td>
                                    <td class="whitespace-nowrap px-4 py-4 text-right text-slate-700">{{ (float) $line->debit > 0 ? '৳ '.number_format((float) $line->debit, 2) : '—' }}</td>
                                    <td class="whitespace-nowrap px-4 py-4 text-right text-slate-700">{{ (float) $line->credit > 0 ? '৳ '.number_format((float) $line->credit, 2) : '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        </article>
    @endif
</x-layouts.app>
