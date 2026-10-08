<x-layouts.accounting title="Sales Orders & Invoices · ALO Accounting" breadcrumb="Sales Orders">
    <div data-aos="fade-down" data-aos-duration="500" class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <div class="inline-flex items-center gap-2">
                <span class="inline-flex items-center rounded-md bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-800 ring-1 ring-inset ring-slate-500/10">Commercial Audit</span>
                <span class="text-xs font-medium uppercase tracking-wider text-slate-500">Order Registers</span>
            </div>
            <h1 class="mt-1.5 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Sales Orders &amp; Invoices</h1>
            <p class="mt-1 text-sm text-slate-500">Trace orders from inventory reservation to double-entry journal creation.</p>
        </div>

        {{-- Filter by status --}}
        <form method="GET" action="{{ route('accounting.orders') }}" class="flex items-center gap-2">
            <select name="status" onchange="this.form.submit()" aria-label="Filter orders by status" class="h-9 rounded-md border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-700 shadow-2xs focus:border-slate-950 focus:outline-none focus:ring-1 focus:ring-slate-950 cursor-pointer">
                <option value="">All Orders</option>
                <option value="pending" @selected($selectedStatus === 'pending')>Status: Pending</option>
                <option value="completed" @selected($selectedStatus === 'completed')>Status: Completed</option>
            </select>
            @if ($selectedStatus)
                <a href="{{ route('accounting.orders') }}" class="inline-flex h-9 items-center rounded-md border border-dashed border-slate-300 px-2.5 text-xs font-medium text-slate-500 hover:text-slate-900">
                    Clear
                </a>
            @endif
        </form>
    </div>

    {{-- Top Metrics Card --}}
    <div class="mt-6 rounded-xl border border-slate-200/90 bg-white p-5 shadow-2xs">
        <div class="flex items-center justify-between">
            <div>
                <span class="text-xs font-medium text-slate-500">Total Completed Sales Revenue</span>
                <div class="mt-1 text-2xl font-bold font-mono text-slate-900">৳ {{ $totalSales }}</div>
            </div>
            <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                Posted to Accounts Receivable (1100)
            </span>
        </div>
    </div>

    {{-- Table of Orders --}}
    <section class="mt-6 overflow-hidden rounded-xl border border-slate-200/90 bg-white shadow-2xs">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="border-b border-slate-100 bg-slate-50/75 text-[11px] font-medium uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-6 py-3">Order ID</th>
                        <th class="px-4 py-3">Customer</th>
                        <th class="px-4 py-3">Order Date</th>
                        <th class="px-4 py-3 text-center">Items</th>
                        <th class="px-4 py-3 text-right">Grand Total</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3">Journal Ref</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($orders as $order)
                        <tr class="transition-colors hover:bg-slate-50/50">
                            {{-- Order ID --}}
                            <td class="whitespace-nowrap px-6 py-4 font-mono text-xs font-bold text-slate-900">
                                <a href="{{ route('orders.show', $order) }}" class="hover:text-indigo-600 hover:underline">
                                    #{{ $order->id }}
                                </a>
                            </td>

                            {{-- Customer --}}
                            <td class="px-4 py-4 text-xs font-medium text-slate-800">
                                {{ $order->customer->name }}
                            </td>

                            {{-- Date --}}
                            <td class="whitespace-nowrap px-4 py-4 text-xs text-slate-500">
                                {{ $order->order_date->format('d M Y') }}
                            </td>

                            {{-- Items Count --}}
                            <td class="whitespace-nowrap px-4 py-4 text-center text-xs font-mono text-slate-600">
                                {{ $order->items->count() }} line(s)
                            </td>

                            {{-- Grand Total --}}
                            <td class="whitespace-nowrap px-4 py-4 text-right font-mono text-xs font-bold text-slate-900">
                                ৳ {{ number_format((float) $order->grand_total, 2) }}
                            </td>

                            {{-- Status --}}
                            <td class="whitespace-nowrap px-4 py-4 text-center">
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
                            </td>

                            {{-- Journal Reference --}}
                            <td class="whitespace-nowrap px-4 py-4 font-mono text-xs text-slate-600">
                                @if ($order->journalEntry)
                                    <span class="font-semibold text-slate-900">{{ $order->journalEntry->reference }}</span>
                                @else
                                    <span class="text-slate-300">Pending post</span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="whitespace-nowrap px-6 py-4 text-right text-xs">
                                <div class="inline-flex items-center justify-end gap-2">
                                    <a href="{{ route('orders.show', $order) }}" class="inline-flex h-7 items-center rounded-md border border-slate-200 bg-white px-2.5 font-medium text-slate-700 shadow-2xs hover:bg-slate-50">
                                        Details
                                    </a>
                                    @if ($order->status->value === 'completed')
                                        <a href="{{ route('orders.invoice', $order) }}" class="inline-flex h-7 items-center gap-1 rounded-md border border-indigo-200 bg-indigo-50/70 px-2.5 font-medium text-indigo-700 hover:bg-indigo-100">
                                            Invoice
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-14 text-center text-xs text-slate-400">
                                No sales orders found for this status.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($orders->hasPages())
            <div class="border-t border-slate-100 px-6 py-4">
                {{ $orders->links() }}
            </div>
        @endif
    </section>
</x-layouts.accounting>

