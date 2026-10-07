<x-layouts.app title="Orders · ALO POS">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wider text-indigo-600">Sales orders</p>
            <h1 class="mt-1 text-3xl font-bold tracking-tight text-slate-950">Orders</h1>
            <p class="mt-2 text-sm text-slate-600">Track pending and completed sales orders.</p>
        </div>
        <a href="{{ route('orders.create') }}" class="inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">New order</a>
    </div>

    <section class="mt-8 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <form method="GET" action="{{ route('orders.index') }}" class="flex flex-col gap-3 sm:flex-row sm:items-end">
            <div class="w-full sm:max-w-xs">
                <label for="status" class="mb-1.5 block text-sm font-medium text-slate-700">Filter by status</label>
                <select id="status" name="status" class="block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">All statuses</option>
                    <option value="pending" @selected($selectedStatus?->value === 'pending')>Pending</option>
                    <option value="completed" @selected($selectedStatus?->value === 'completed')>Completed</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-700">Apply</button>
                <a href="{{ route('orders.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Reset</a>
            </div>
        </form>
    </section>

    <section class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Order</th>
                        <th class="px-5 py-3">Customer</th>
                        <th class="px-5 py-3">Date</th>
                        <th class="px-5 py-3 text-right">Total</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($orders as $order)
                        <tr class="transition hover:bg-slate-50">
                            <td class="whitespace-nowrap px-5 py-4 font-semibold text-slate-900"><a href="{{ route('orders.show', $order) }}" class="hover:text-indigo-700">#{{ $order->id }}</a></td>
                            <td class="px-5 py-4 text-slate-700">{{ $order->customer->name }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-slate-600">{{ $order->order_date->format('d M Y') }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-right font-medium text-slate-900">৳ {{ number_format((float) $order->grand_total, 2) }}</td>
                            <td class="px-5 py-4">
                                <span @class(['inline-flex rounded-full px-2.5 py-1 text-xs font-semibold', 'bg-amber-100 text-amber-800' => $order->status->value === 'pending', 'bg-emerald-100 text-emerald-800' => $order->status->value === 'completed'])>{{ ucfirst($order->status->value) }}</span>
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-right">
                                @if ($order->status->value === 'pending')
                                    <form method="POST" action="{{ route('orders.complete', $order) }}" onsubmit="return confirm('Complete this order? Stock will be deducted.');">
                                        @csrf
                                        <button type="submit" class="rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-emerald-700">Complete</button>
                                    </form>
                                @else
                                    <a href="{{ route('orders.show', $order) }}" class="text-sm font-semibold text-indigo-700 hover:text-indigo-900">View</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-14 text-center text-sm text-slate-500">No orders found. Create your first sales order to begin.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($orders->hasPages())
            <div class="border-t border-slate-200 px-5 py-4">
                {{ $orders->withQueryString()->links() }}
            </div>
        @endif
    </section>
</x-layouts.app>
