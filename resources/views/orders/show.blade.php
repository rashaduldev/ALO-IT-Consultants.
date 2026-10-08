<x-layouts.app title="Order #{{ $order->id }} · ALO POS">
    <div data-aos="fade-down" data-aos-duration="500" class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <div class="inline-flex items-center gap-2">
                <span class="inline-flex items-center rounded-md bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-800 ring-1 ring-inset ring-slate-500/10">POS Module</span>
                <span class="text-xs font-medium uppercase tracking-wider text-slate-500">Order Details</span>
            </div>
            <div class="mt-1.5 flex flex-wrap items-center gap-3">
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Order #{{ $order->id }}</h1>
                @if ($order->status->value === 'pending')
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-medium text-amber-700 ring-1 ring-inset ring-amber-600/20">
                        <span class="size-1.5 rounded-full bg-amber-500"></span>
                        Pending
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                        <span class="size-1.5 rounded-full bg-emerald-500"></span>
                        Completed
                    </span>
                @endif
            </div>
            <p class="mt-1 text-sm text-slate-500">{{ $order->customer->name }} · {{ $order->order_date->format('d M Y') }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('orders.index') }}" class="inline-flex h-9 items-center justify-center rounded-md border border-slate-200 bg-white px-3.5 text-xs font-medium text-slate-700 shadow-2xs transition-colors hover:bg-slate-50 hover:text-slate-900">
                Back to orders
            </a>
            @if ($order->status->value === 'completed')
                <a href="{{ route('orders.invoice', $order) }}" class="inline-flex h-9 items-center gap-1.5 justify-center rounded-md border border-indigo-200 bg-indigo-50/70 px-3.5 text-xs font-semibold text-indigo-700 shadow-2xs transition-colors hover:bg-indigo-100">
                    <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    View invoice
                </a>
            @endif
            @if ($order->status->value === 'pending')
                <form method="POST" action="{{ route('orders.complete', $order) }}" onsubmit="return confirm('Complete this order? Stock will be deducted atomically and journal entries will post.');">
                    @csrf
                    <button type="submit" class="inline-flex h-9 items-center justify-center rounded-md bg-emerald-600 px-4 text-xs font-semibold text-white shadow-2xs transition-all hover:bg-emerald-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-600 active:scale-[0.99]">
                        Complete order
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="mt-8 grid gap-6 xl:grid-cols-[minmax(0,1fr)_22rem]">
        <div class="space-y-6">
            {{-- Line Items Table Card --}}
            <section data-aos="fade-up" data-aos-delay="100" class="overflow-hidden rounded-xl border border-slate-200/90 bg-white shadow-2xs transition-all">
                <div class="border-b border-slate-100 p-5 sm:px-6">
                    <h2 class="text-base font-semibold leading-none tracking-tight text-slate-900">Ordered items</h2>
                    <p class="mt-1 text-xs text-slate-500">Products and pricing snapshots captured at time of order.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-left text-sm">
                        <thead class="border-b border-slate-100 bg-slate-50/75 text-[11px] font-medium uppercase tracking-wider text-slate-500">
                            <tr>
                                <th class="px-6 py-3">Product</th>
                                <th class="px-4 py-3 text-right">Unit price</th>
                                <th class="px-4 py-3 text-center">Quantity</th>
                                <th class="px-6 py-3 text-right">Line total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($order->items as $item)
                                <tr class="transition-colors hover:bg-slate-50/50">
                                    <td class="px-6 py-4 font-medium text-slate-900">{{ $item->product_name }}</td>
                                    <td class="whitespace-nowrap px-4 py-4 text-right font-mono text-xs text-slate-600">৳ {{ number_format((float) $item->unit_price, 2) }}</td>
                                    <td class="px-4 py-4 text-center font-mono text-xs text-slate-700">{{ $item->quantity }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right font-mono text-xs font-semibold text-slate-900">৳ {{ number_format((float) $item->line_total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>

            {{-- Audit & State Transition Timeline Widget --}}
            <section data-aos="fade-up" data-aos-delay="200">
                <x-order-timeline :logs="$order->auditLogs" />
            </section>
        </div>

        {{-- Order Summary Card --}}
        <aside data-aos="fade-up" data-aos-delay="300" class="h-fit rounded-xl border border-slate-200/90 bg-white p-6 shadow-2xs transition-all xl:sticky xl:top-6">
            <div class="border-b border-slate-100 pb-3">
                <h2 class="text-base font-semibold leading-none tracking-tight text-slate-900">Financial summary</h2>
                <p class="mt-1 text-xs text-slate-500">Breakdown of gross total, discount, and tax.</p>
            </div>
            <dl class="mt-5 space-y-2.5 text-xs">
                <div class="flex justify-between text-slate-500">
                    <dt>Subtotal</dt>
                    <dd class="font-mono font-medium text-slate-900">৳ {{ number_format((float) $order->subtotal, 2) }}</dd>
                </div>
                <div class="flex justify-between text-slate-500">
                    <dt>Discount</dt>
                    <dd class="font-mono font-medium text-emerald-600">− ৳ {{ number_format((float) $order->discount, 2) }}</dd>
                </div>
                <div class="flex justify-between text-slate-500">
                    <dt>Tax (5% VAT)</dt>
                    <dd class="font-mono font-medium text-slate-900">৳ {{ number_format((float) $order->tax, 2) }}</dd>
                </div>
            </dl>
            <div class="mt-4 border-t border-slate-100 pt-4">
                <div class="flex items-baseline justify-between">
                    <span class="text-xs font-medium uppercase tracking-wider text-slate-500">Grand total</span>
                    <span class="font-mono text-2xl font-bold tracking-tight text-slate-950">৳ {{ number_format((float) $order->grand_total, 2) }}</span>
                </div>
            </div>

            @if ($order->journalEntry)
                <div class="mt-4 rounded-md border border-slate-100 bg-slate-50 p-3 text-xs">
                    <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-500">Posted Journal</span>
                    <p class="mt-1 font-mono font-bold text-slate-900">{{ $order->journalEntry->reference }}</p>
                    <p class="text-[11px] text-slate-500">Dated {{ $order->journalEntry->entry_date->format('d M Y') }}</p>
                </div>
            @endif
        </aside>
    </div>
</x-layouts.app>
