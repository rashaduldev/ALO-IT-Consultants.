<x-layouts.app title="{{ $invoiceNumber }} · ALO POS">
    <style>
        @media print {
            /* 1. Neutralize all AOS animations, transforms & opacities */
            [data-aos] {
                opacity: 1 !important;
                transform: none !important;
                filter: none !important;
                transition: none !important;
                animation: none !important;
            }

            /* 2. Hide navigational chrome and action bars */
            header,
            nav,
            .print-hidden {
                display: none !important;
            }

            body {
                background: #ffffff !important;
                color: #000000 !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            main {
                max-width: none !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            /* Print Mode: Standard A4 Invoice */
            body:not(.print-thermal-mode) #thermal-receipt-wrapper {
                display: none !important;
            }

            body:not(.print-thermal-mode) #standard-invoice-wrapper {
                display: block !important;
            }

            body:not(.print-thermal-mode) .invoice-sheet {
                border: 0 !important;
                box-shadow: none !important;
                padding: 0 !important;
                margin: 0 !important;
                max-width: 100% !important;
            }

            /* Print Mode: 80mm Thermal Receipt */
            body.print-thermal-mode #standard-invoice-wrapper {
                display: none !important;
            }

            body.print-thermal-mode #thermal-receipt-wrapper {
                display: block !important;
                width: 80mm !important;
                max-width: 80mm !important;
                margin: 0 auto !important;
                padding: 0 !important;
            }

            body.print-thermal-mode #thermal-receipt {
                box-shadow: none !important;
                padding: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
            }

            @page {
                margin: 0;
            }
        }
    </style>

    @if (! $isCompleted)
        <section data-aos="zoom-in" data-aos-duration="500" class="mx-auto max-w-2xl rounded-2xl border border-amber-200 bg-amber-50 p-6 text-center shadow-sm">
            <span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800">Pending order</span>
            <h1 class="mt-4 text-2xl font-bold tracking-tight text-amber-950">Invoice unavailable until completion</h1>
            <p class="mt-2 text-sm leading-6 text-amber-800">Order #{{ $order->id }} must be completed before an invoice and its accounting entry can be issued.</p>
            <a href="{{ route('orders.show', $order) }}" class="print-hidden mt-6 inline-flex rounded-lg bg-amber-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-amber-800">Return to order</a>
        </section>
    @else
        {{-- Professional Action & View Switcher Bar --}}
        <div data-aos="fade-down" data-aos-duration="500" class="print-hidden mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <a href="{{ route('orders.show', $order) }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 transition-colors hover:text-slate-900">
                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to order #{{ $order->id }}
            </a>

            <div class="flex flex-wrap items-center gap-3">
                {{-- Format View Toggle: Standard A4 vs 80mm Thermal Receipt --}}
                <div class="inline-flex rounded-lg border border-slate-200 bg-slate-100/80 p-1 text-xs font-medium">
                    <button id="toggle-standard" type="button" class="flex items-center gap-1.5 rounded-md bg-white px-3 py-1.5 font-semibold text-slate-900 shadow-2xs transition-all">
                        <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Standard (A4)
                    </button>
                    <button id="toggle-thermal" type="button" class="flex items-center gap-1.5 rounded-md px-3 py-1.5 text-slate-600 transition-all hover:text-slate-900">
                        <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        80mm POS Thermal
                    </button>
                </div>

                <button type="button" onclick="window.print()" class="inline-flex h-9 items-center gap-1.5 rounded-md bg-slate-900 px-4 text-xs font-semibold text-white shadow-2xs transition-all hover:bg-slate-800 active:scale-[0.99]">
                    <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Print
                </button>
            </div>
        </div>

        {{-- 1. Standard A4 View Container --}}
        <div id="standard-invoice-wrapper">
            <article data-aos="fade-up" data-aos-duration="600" class="invoice-sheet mx-auto max-w-4xl rounded-2xl border border-slate-200 bg-white p-6 shadow-sm transition-all duration-300 sm:p-10">
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
        </div>

        {{-- 2. Thermal POS Receipt View Container (80mm) --}}
        <div id="thermal-receipt-wrapper" class="hidden py-6">
            <x-pos-thermal-receipt :order="$order" :invoice-number="$invoiceNumber" />
        </div>
    @endif

    @push('scripts')
        <script>
            const toggleStandard = document.getElementById('toggle-standard');
            const toggleThermal = document.getElementById('toggle-thermal');
            const standardWrapper = document.getElementById('standard-invoice-wrapper');
            const thermalWrapper = document.getElementById('thermal-receipt-wrapper');

            if (toggleStandard && toggleThermal) {
                toggleStandard.addEventListener('click', () => {
                    standardWrapper.classList.remove('hidden');
                    thermalWrapper.classList.add('hidden');
                    document.body.classList.remove('print-thermal-mode');

                    toggleStandard.className = 'flex items-center gap-1.5 rounded-md bg-white px-3 py-1.5 font-semibold text-slate-900 shadow-2xs transition-all';
                    toggleThermal.className = 'flex items-center gap-1.5 rounded-md px-3 py-1.5 text-slate-600 transition-all hover:text-slate-900';
                });

                toggleThermal.addEventListener('click', () => {
                    standardWrapper.classList.add('hidden');
                    thermalWrapper.classList.remove('hidden');
                    document.body.classList.add('print-thermal-mode');

                    toggleThermal.className = 'flex items-center gap-1.5 rounded-md bg-white px-3 py-1.5 font-semibold text-slate-900 shadow-2xs transition-all';
                    toggleStandard.className = 'flex items-center gap-1.5 rounded-md px-3 py-1.5 text-slate-600 transition-all hover:text-slate-900';
                });
            }
        </script>
    @endpush
</x-layouts.app>
