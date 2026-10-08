<x-layouts.app title="Orders · ALO POS">
    {{-- Header with action button --}}
    <div data-aos="fade-down" data-aos-duration="500" class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <div class="inline-flex items-center gap-2">
                <span class="inline-flex items-center rounded-md bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-800 ring-1 ring-inset ring-slate-500/10">POS Module</span>
                <span class="text-xs font-medium uppercase tracking-wider text-slate-500">Sales Register</span>
            </div>
            <h1 class="mt-1.5 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Orders</h1>
            <p class="mt-1 text-sm text-slate-500">Monitor live transactions, complete pending orders, and issue invoices.</p>
        </div>
        <a href="{{ route('orders.create') }}" class="inline-flex h-9 items-center justify-center gap-1.5 rounded-md bg-slate-900 px-4 text-xs font-semibold text-white shadow-2xs transition-all hover:bg-slate-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-950 focus-visible:ring-offset-2 active:scale-[0.99]">
            <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New sales order
        </a>
    </div>

    {{-- Orders Card & Data Table --}}
    <section data-aos="fade-up" data-aos-delay="100" class="mt-8 overflow-hidden rounded-xl border border-slate-200/90 bg-white shadow-2xs transition-all">
        {{-- Professional Filter Toolbar --}}
        <div class="flex flex-col gap-3 border-b border-slate-100 p-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            {{-- Segmented Status Filter Tabs (Linear / Stripe style) --}}
            <nav aria-label="Order status filter" class="inline-flex items-center rounded-lg border border-slate-200 bg-slate-100/75 p-1 text-xs font-medium">
                {{-- All Tab --}}
                <a href="{{ route('orders.index') }}" @class([
                    'flex items-center gap-2 rounded-md px-3 py-1.5 transition-all',
                    'bg-white text-slate-950 font-semibold shadow-2xs' => ! $selectedStatus,
                    'text-slate-600 hover:text-slate-900' => $selectedStatus,
                ])>
                    All Orders
                    <span @class([
                        'rounded-full px-1.5 py-0.5 text-[10px] font-bold leading-none',
                        'bg-slate-100 text-slate-800' => ! $selectedStatus,
                        'bg-slate-200/60 text-slate-600' => $selectedStatus,
                    ])>{{ $statusCounts['all'] ?? $orders->total() }}</span>
                </a>

                {{-- Pending Tab --}}
                <a href="{{ route('orders.index', ['status' => 'pending']) }}" @class([
                    'flex items-center gap-2 rounded-md px-3 py-1.5 transition-all',
                    'bg-white text-slate-950 font-semibold shadow-2xs' => $selectedStatus?->value === 'pending',
                    'text-slate-600 hover:text-slate-900' => $selectedStatus?->value !== 'pending',
                ])>
                    <span class="size-1.5 rounded-full bg-amber-500"></span>
                    Pending
                    <span @class([
                        'rounded-full px-1.5 py-0.5 text-[10px] font-bold leading-none',
                        'bg-amber-100 text-amber-800' => $selectedStatus?->value === 'pending',
                        'bg-slate-200/60 text-slate-600' => $selectedStatus?->value !== 'pending',
                    ])>{{ $statusCounts['pending'] ?? 0 }}</span>
                </a>

                {{-- Completed Tab --}}
                <a href="{{ route('orders.index', ['status' => 'completed']) }}" @class([
                    'flex items-center gap-2 rounded-md px-3 py-1.5 transition-all',
                    'bg-white text-slate-950 font-semibold shadow-2xs' => $selectedStatus?->value === 'completed',
                    'text-slate-600 hover:text-slate-900' => $selectedStatus?->value !== 'completed',
                ])>
                    <span class="size-1.5 rounded-full bg-emerald-500"></span>
                    Completed
                    <span @class([
                        'rounded-full px-1.5 py-0.5 text-[10px] font-bold leading-none',
                        'bg-emerald-100 text-emerald-800' => $selectedStatus?->value === 'completed',
                        'bg-slate-200/60 text-slate-600' => $selectedStatus?->value !== 'completed',
                    ])>{{ $statusCounts['completed'] ?? 0 }}</span>
                </a>
            </nav>

            {{-- Compact Shadcn Dropdown Filter (Auto-Submits on Selection) --}}
            <form method="GET" action="{{ route('orders.index') }}" class="flex items-center gap-2">
                <div class="relative">
                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-2.5 text-slate-400">
                        <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    </div>
                    <select name="status" onchange="this.form.submit()" aria-label="Filter orders by status" class="h-8 appearance-none rounded-md border border-slate-200 bg-white pl-8 pr-7 text-xs font-medium text-slate-700 shadow-2xs transition-colors hover:border-slate-300 focus:border-slate-950 focus:outline-none focus:ring-1 focus:ring-slate-950 cursor-pointer">
                        <option value="">Filter status: All</option>
                        <option value="pending" @selected($selectedStatus?->value === 'pending')>Filter status: Pending</option>
                        <option value="completed" @selected($selectedStatus?->value === 'completed')>Filter status: Completed</option>
                    </select>
                    <span class="pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400">
                        <svg class="size-3 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m6 9 6 6 6-6"/></svg>
                    </span>
                </div>

                @if ($selectedStatus)
                    <a href="{{ route('orders.index') }}" class="inline-flex h-8 items-center gap-1 rounded-md border border-dashed border-slate-300 px-2.5 text-xs font-medium text-slate-500 transition-colors hover:border-slate-400 hover:text-slate-900" title="Clear filter">
                        <svg class="size-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        Clear
                    </a>
                @endif
            </form>
        </div>

        {{-- Table Content --}}
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="border-b border-slate-100 bg-slate-50/75 text-[11px] font-medium uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-6 py-3">Order</th>
                        <th class="px-4 py-3">Customer</th>
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3 text-right">Total amount</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($orders as $order)
                        <tr class="transition-colors hover:bg-slate-50/50">
                            {{-- Order ID --}}
                            <td class="whitespace-nowrap px-6 py-4 font-mono text-xs font-semibold text-slate-900">
                                <a href="{{ route('orders.show', $order) }}" class="inline-flex items-center gap-1 hover:text-indigo-600 hover:underline">
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

                            {{-- Total --}}
                            <td class="whitespace-nowrap px-4 py-4 text-right font-mono text-xs font-bold text-slate-900">
                                ৳ {{ number_format((float) $order->grand_total, 2) }}
                            </td>

                            {{-- Status Badge --}}
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

                            {{-- Actions --}}
                            <td class="whitespace-nowrap px-6 py-4 text-right text-xs">
                                <div class="inline-flex items-center justify-end gap-2">
                                    <a href="{{ route('orders.show', $order) }}" class="inline-flex h-7 items-center rounded-md border border-slate-200 bg-white px-2.5 font-medium text-slate-700 shadow-2xs transition-colors hover:bg-slate-50 hover:text-slate-900">
                                        View
                                    </a>

                                    @if ($order->status->value === 'pending')
                                        <form method="POST" action="{{ route('orders.complete', $order) }}" onsubmit="return confirm('Complete order #{{ $order->id }}? Inventory will be deducted.');">
                                            @csrf
                                            <button type="submit" class="inline-flex h-7 items-center rounded-md bg-emerald-600 px-2.5 font-medium text-white shadow-2xs transition-colors hover:bg-emerald-700">
                                                Complete
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('orders.invoice', $order) }}" class="inline-flex h-7 items-center gap-1 rounded-md border border-indigo-200 bg-indigo-50/70 px-2.5 font-medium text-indigo-700 transition-colors hover:bg-indigo-100">
                                            <svg class="size-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                            Invoice
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-14 text-center">
                                <div class="mx-auto flex max-w-xs flex-col items-center">
                                    <div class="flex size-10 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                                        <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </div>
                                    <p class="mt-3 text-sm font-semibold text-slate-900">No orders found</p>
                                    <p class="mt-1 text-xs text-slate-500">
                                        @if ($selectedStatus)
                                            There are no orders matching the "{{ ucfirst($selectedStatus->value) }}" status.
                                        @else
                                            Get started by creating your first sales order.
                                        @endif
                                    </p>
                                    @if ($selectedStatus)
                                        <a href="{{ route('orders.index') }}" class="mt-4 inline-flex h-8 items-center rounded-md border border-slate-200 bg-white px-3 text-xs font-medium text-slate-700 shadow-2xs hover:bg-slate-50">
                                            Clear filter
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($orders->hasPages())
            <div class="border-t border-slate-100 px-6 py-4">
                {{ $orders->withQueryString()->links() }}
            </div>
        @endif
    </section>
</x-layouts.app>
