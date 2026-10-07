<x-layouts.app title="Order #{{ $order->id }} · ALO POS">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wider text-indigo-600">Sales order</p>
            <div class="mt-1 flex flex-wrap items-center gap-3">
                <h1 class="text-3xl font-bold tracking-tight text-slate-950">Order #{{ $order->id }}</h1>
                <span @class(['inline-flex rounded-full px-2.5 py-1 text-xs font-semibold', 'bg-amber-100 text-amber-800' => $order->status->value === 'pending', 'bg-emerald-100 text-emerald-800' => $order->status->value === 'completed'])>{{ ucfirst($order->status->value) }}</span>
            </div>
            <p class="mt-2 text-sm text-slate-600">{{ $order->customer->name }} · {{ $order->order_date->format('d M Y') }}</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('orders.index') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Back to orders</a>
            @if ($order->status->value === 'completed')
                <a href="{{ route('orders.invoice', $order) }}" class="inline-flex items-center justify-center rounded-lg border border-indigo-200 bg-indigo-50 px-4 py-2.5 text-sm font-semibold text-indigo-700 transition hover:bg-indigo-100">View invoice</a>
            @endif
            @if ($order->status->value === 'pending')
                <form method="POST" action="{{ route('orders.complete', $order) }}" onsubmit="return confirm('Complete this order? Stock will be deducted.');">
                    @csrf
                    <button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">Complete order</button>
                </form>
            @endif
        </div>
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-[minmax(0,1fr)_20rem]">
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 px-5 py-5 sm:px-6">
                <h2 class="text-lg font-semibold text-slate-950">Items</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-5 py-3 sm:px-6">Product</th>
                            <th class="px-5 py-3 text-right">Unit price</th>
                            <th class="px-5 py-3 text-right">Quantity</th>
                            <th class="px-5 py-3 text-right sm:px-6">Line total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($order->items as $item)
                            <tr>
                                <td class="px-5 py-4 font-medium text-slate-900 sm:px-6">{{ $item->product_name }}</td>
                                <td class="whitespace-nowrap px-5 py-4 text-right text-slate-700">৳ {{ number_format((float) $item->unit_price, 2) }}</td>
                                <td class="px-5 py-4 text-right text-slate-700">{{ $item->quantity }}</td>
                                <td class="whitespace-nowrap px-5 py-4 text-right font-semibold text-slate-900 sm:px-6">৳ {{ number_format((float) $item->line_total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <aside class="h-fit rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <h2 class="text-lg font-semibold text-slate-950">Summary</h2>
            <dl class="mt-5 space-y-3 text-sm">
                <div class="flex justify-between gap-4"><dt class="text-slate-600">Subtotal</dt><dd class="font-medium text-slate-900">৳ {{ number_format((float) $order->subtotal, 2) }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-slate-600">Discount</dt><dd class="font-medium text-slate-900">− ৳ {{ number_format((float) $order->discount, 2) }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-slate-600">Tax (5%)</dt><dd class="font-medium text-slate-900">৳ {{ number_format((float) $order->tax, 2) }}</dd></div>
                <div class="mt-4 flex justify-between gap-4 border-t border-slate-200 pt-4"><dt class="font-semibold text-slate-900">Grand total</dt><dd class="text-lg font-bold text-indigo-700">৳ {{ number_format((float) $order->grand_total, 2) }}</dd></div>
            </dl>
        </aside>
    </div>
</x-layouts.app>
