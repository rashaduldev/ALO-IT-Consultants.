<x-layouts.app title="New Order · ALO POS">
    @php($oldItems = old('items', [['product_id' => '', 'quantity' => 1]]))

    <div data-aos="fade-down" data-aos-duration="500" class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wider text-indigo-600">Sales order</p>
            <h1 class="mt-1 text-3xl font-bold tracking-tight text-slate-950">Create new order</h1>
            <p class="mt-2 text-sm text-slate-600">Prices and totals are previewed here and recalculated securely on the server.</p>
        </div>
        <a href="{{ route('orders.index') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Back to orders</a>
    </div>

    <form id="order-form" method="POST" action="{{ route('orders.store') }}" class="mt-8 grid gap-6 xl:grid-cols-[minmax(0,1fr)_20rem]">
        @csrf

        <div class="space-y-6">
            <section data-aos="fade-up" data-aos-delay="100" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6 transition-all duration-300">
                <h2 class="text-lg font-semibold text-slate-950">Order details</h2>
                <div class="mt-5 grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="customer_id" class="mb-1.5 block text-sm font-medium text-slate-700">Customer</label>
                        <select id="customer_id" name="customer_id" class="block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('customer_id') border-red-400 @enderror" required>
                            <option value="">Select a customer</option>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}" @selected((string) old('customer_id') === (string) $customer->id)>{{ $customer->name }}</option>
                            @endforeach
                        </select>
                        @error('customer_id')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="order_date" class="mb-1.5 block text-sm font-medium text-slate-700">Order date</label>
                        <input id="order_date" name="order_date" type="date" value="{{ old('order_date', now()->toDateString()) }}" class="block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('order_date') border-red-400 @enderror" required>
                        @error('order_date')
                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </section>

            <section data-aos="fade-up" data-aos-delay="200" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition-all duration-300">
                <div class="flex flex-col gap-3 border-b border-slate-200 px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-950">Line items</h2>
                        <p class="mt-1 text-sm text-slate-600">Add one or more products to this order.</p>
                    </div>
                    <button id="add-item" type="button" class="inline-flex items-center justify-center rounded-lg border border-indigo-200 bg-indigo-50 px-3.5 py-2 text-sm font-semibold text-indigo-700 transition hover:bg-indigo-100">Add product</button>
                </div>

                @error('items')
                    <div class="mx-5 mt-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 sm:mx-6">{{ $message }}</div>
                @enderror

                <div class="overflow-x-auto">
                    <table class="min-w-[720px] w-full text-left text-sm">
                        <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-5 py-3 sm:px-6">Product</th>
                                <th class="px-5 py-3">Unit price</th>
                                <th class="w-32 px-5 py-3">Quantity</th>
                                <th class="px-5 py-3 text-right">Line total</th>
                                <th class="w-20 px-5 py-3 sm:px-6"><span class="sr-only">Remove</span></th>
                            </tr>
                        </thead>
                        <tbody id="item-rows" class="divide-y divide-slate-100">
                            @foreach ($oldItems as $index => $item)
                                @php($selectedProduct = $products->firstWhere('id', $item['product_id'] ?? null))
                                <tr data-item-row>
                                    <td class="px-5 py-4 sm:px-6">
                                        <select name="items[{{ $index }}][product_id]" data-product-select aria-label="Product" class="block w-full min-w-64 rounded-lg border-slate-300 bg-white text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error("items.$index.product_id") border-red-400 @enderror" required>
                                            <option value="">Select a product</option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->id }}" @selected((string) ($item['product_id'] ?? '') === (string) $product->id)>{{ $product->sku }} — {{ $product->name }}</option>
                                            @endforeach
                                        </select>
                                        <p data-stock-hint class="mt-1.5 text-xs text-slate-500">{{ $selectedProduct ? "In stock: {$selectedProduct->stock_quantity}" : 'Select a product to view stock.' }}</p>
                                        @error("items.$index.product_id")
                                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-4 font-medium text-slate-700" data-unit-price>{{ $selectedProduct ? '৳ '.number_format((float) $selectedProduct->price, 2) : '—' }}</td>
                                    <td class="px-5 py-4">
                                        <input name="items[{{ $index }}][quantity]" data-quantity type="number" min="1" max="{{ $selectedProduct?->stock_quantity }}" value="{{ $item['quantity'] ?? 1 }}" aria-label="Quantity" class="block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error("items.$index.quantity") border-red-400 @enderror" required>
                                        @error("items.$index.quantity")
                                            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </td>
                                    <td class="whitespace-nowrap px-5 py-4 text-right font-semibold text-slate-900" data-line-total>৳ 0.00</td>
                                    <td class="px-5 py-4 text-right sm:px-6">
                                        <button type="button" data-remove-item class="rounded-lg p-2 text-slate-400 transition hover:bg-red-50 hover:text-red-600" aria-label="Remove product">×</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <aside data-aos="fade-up" data-aos-delay="300" class="h-fit rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6 xl:sticky xl:top-6 transition-all duration-300">
            <h2 class="text-lg font-semibold text-slate-950">Order summary</h2>
            <div class="mt-5">
                <label for="discount" class="mb-1.5 block text-sm font-medium text-slate-700">Discount</label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-sm text-slate-500">৳</span>
                    <input id="discount" name="discount" data-discount type="number" min="0" step="0.01" value="{{ old('discount', 0) }}" class="block w-full rounded-lg border-slate-300 bg-white py-2 pl-7 pr-3 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 @error('discount') border-red-400 @enderror">
                </div>
                @error('discount')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <dl class="mt-6 space-y-3 border-y border-slate-200 py-5 text-sm">
                <div class="flex justify-between gap-4"><dt class="text-slate-600">Subtotal</dt><dd data-subtotal class="font-medium text-slate-900">৳ 0.00</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-slate-600">Discount</dt><dd data-discount-total class="font-medium text-slate-900">− ৳ 0.00</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-slate-600">Tax (5%)</dt><dd data-tax class="font-medium text-slate-900">৳ 0.00</dd></div>
            </dl>
            <div class="mt-5 flex items-end justify-between gap-4">
                <span class="text-sm font-semibold text-slate-700">Grand total</span>
                <span data-grand-total class="text-2xl font-bold tracking-tight text-indigo-700">৳ 0.00</span>
            </div>
            <p data-discount-warning class="mt-3 hidden text-sm text-red-600"></p>
            <button type="submit" class="mt-6 w-full rounded-lg bg-indigo-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Create pending order</button>
        </aside>
    </form>

    <template id="order-item-template">
        <tr data-item-row class="animate-fade-in transition-colors hover:bg-slate-50/50">
            <td class="px-5 py-4 sm:px-6">
                <select name="items[__INDEX__][product_id]" data-product-select aria-label="Product" class="block w-full min-w-64 rounded-lg border-slate-300 bg-white text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                    <option value="">Select a product</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}">{{ $product->sku }} — {{ $product->name }}</option>
                    @endforeach
                </select>
                <p data-stock-hint class="mt-1.5 text-xs text-slate-500">Select a product to view stock.</p>
            </td>
            <td class="whitespace-nowrap px-5 py-4 font-medium text-slate-700" data-unit-price>—</td>
            <td class="px-5 py-4"><input name="items[__INDEX__][quantity]" data-quantity type="number" min="1" value="1" aria-label="Quantity" class="block w-full rounded-lg border-slate-300 bg-white text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required></td>
            <td class="whitespace-nowrap px-5 py-4 text-right font-semibold text-slate-900" data-line-total>৳ 0.00</td>
            <td class="px-5 py-4 text-right sm:px-6"><button type="button" data-remove-item class="rounded-lg p-2 text-slate-400 transition hover:bg-red-50 hover:text-red-600" aria-label="Remove product">×</button></td>
        </tr>
    </template>

    @push('scripts')
        <script>
            const products = {{ Illuminate\Support\Js::from($products->keyBy('id')->map(fn ($product) => ['price' => $product->price, 'stock' => $product->stock_quantity])) }};
            const form = document.getElementById('order-form');
            const itemRows = document.getElementById('item-rows');
            const itemTemplate = document.getElementById('order-item-template');
            const discountInput = form.querySelector('[data-discount]');
            let nextIndex = {{ count($oldItems) }};

            const money = (cents) => `৳ ${(cents / 100).toFixed(2)}`;
            const toCents = (value) => Math.round((Number.parseFloat(value) || 0) * 100);

            function refreshSummary() {
                let subtotalCents = 0;

                itemRows.querySelectorAll('[data-item-row]').forEach((row) => {
                    const product = products[row.querySelector('[data-product-select]').value];
                    const quantity = Number.parseInt(row.querySelector('[data-quantity]').value, 10) || 0;
                    const lineTotalCents = product ? toCents(product.price) * quantity : 0;
                    row.querySelector('[data-line-total]').textContent = money(lineTotalCents);
                    subtotalCents += lineTotalCents;
                });

                const discountCents = Math.max(0, toCents(discountInput.value));
                const taxableCents = Math.max(0, subtotalCents - discountCents);
                const taxCents = Math.round(taxableCents * 0.05);
                const warning = form.querySelector('[data-discount-warning]');

                form.querySelector('[data-subtotal]').textContent = money(subtotalCents);
                form.querySelector('[data-discount-total]').textContent = `− ${money(Math.min(discountCents, subtotalCents))}`;
                form.querySelector('[data-tax]').textContent = money(taxCents);
                form.querySelector('[data-grand-total]').textContent = money(taxableCents + taxCents);

                warning.textContent = discountCents > subtotalCents ? 'Discount cannot exceed the subtotal.' : '';
                warning.classList.toggle('hidden', discountCents <= subtotalCents);
            }

            function refreshRow(row) {
                const selectedProduct = products[row.querySelector('[data-product-select]').value];
                const quantityInput = row.querySelector('[data-quantity]');
                const stockHint = row.querySelector('[data-stock-hint]');

                row.querySelector('[data-unit-price]').textContent = selectedProduct ? money(toCents(selectedProduct.price)) : '—';
                stockHint.textContent = selectedProduct ? `In stock: ${selectedProduct.stock}` : 'Select a product to view stock.';

                if (selectedProduct) {
                    quantityInput.max = selectedProduct.stock;
                } else {
                    quantityInput.removeAttribute('max');
                }

                refreshSummary();
            }

            function bindRow(row) {
                row.querySelector('[data-product-select]').addEventListener('change', () => refreshRow(row));
                row.querySelector('[data-quantity]').addEventListener('input', refreshSummary);
                row.querySelector('[data-remove-item]').addEventListener('click', () => {
                    row.remove();
                    refreshSummary();
                });
                refreshRow(row);
            }

            itemRows.querySelectorAll('[data-item-row]').forEach(bindRow);
            discountInput.addEventListener('input', refreshSummary);

            document.getElementById('add-item').addEventListener('click', () => {
                const fragment = itemTemplate.content.cloneNode(true);
                const row = fragment.querySelector('[data-item-row]');
                row.querySelectorAll('[name]').forEach((input) => {
                    input.name = input.name.replace('__INDEX__', nextIndex);
                });
                nextIndex += 1;
                itemRows.appendChild(fragment);
                bindRow(itemRows.lastElementChild);
            });

            refreshSummary();
        </script>
    @endpush
</x-layouts.app>
