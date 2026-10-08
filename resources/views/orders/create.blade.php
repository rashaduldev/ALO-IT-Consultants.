<x-layouts.app title="New Order · ALO POS">
    @php($oldItems = old('items', [['product_id' => '', 'quantity' => 1]]))

    <div data-aos="fade-down" data-aos-duration="500" class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <div class="inline-flex items-center gap-2">
                <span class="inline-flex items-center rounded-md bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-800 ring-1 ring-inset ring-slate-500/10">POS Module</span>
                <span class="text-xs font-medium uppercase tracking-wider text-slate-500">Sales Order</span>
            </div>
            <h1 class="mt-1.5 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Create new order</h1>
            <p class="mt-1 text-sm text-slate-500">Configure customer details, select products, and preview real-time VAT totals.</p>
        </div>
        <a href="{{ route('orders.index') }}" class="inline-flex h-9 items-center justify-center rounded-md border border-slate-200 bg-white px-4 text-xs font-medium text-slate-900 shadow-2xs transition-colors hover:bg-slate-100 hover:text-slate-900 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-slate-950">
            Back to orders
        </a>
    </div>

    <form id="order-form" method="POST" action="{{ route('orders.store') }}" class="mt-8 grid gap-6 xl:grid-cols-[minmax(0,1fr)_22rem]">
        @csrf

        <div class="space-y-6">
            {{-- Card 1: Order Details (Shadcn Card) --}}
            <section data-aos="fade-up" data-aos-delay="100" class="rounded-xl border border-slate-200/90 bg-white p-6 shadow-2xs transition-all duration-300">
                <div class="border-b border-slate-100 pb-4">
                    <h2 class="text-base font-semibold leading-none tracking-tight text-slate-900">Order details</h2>
                    <p class="mt-1.5 text-xs text-slate-500">Select the customer and transaction date for this invoice.</p>
                </div>

                <div class="mt-5 grid gap-5 sm:grid-cols-2">
                    {{-- Customer Select --}}
                    <div>
                        <label for="customer_id" class="mb-1.5 block text-xs font-medium text-slate-700">Customer <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <select id="customer_id" name="customer_id" class="flex h-10 w-full appearance-none items-center justify-between rounded-md border border-slate-200 bg-white px-3 py-2 pr-9 text-sm text-slate-900 shadow-2xs ring-offset-white transition-colors placeholder:text-slate-400 focus:border-slate-950 focus:outline-none focus:ring-1 focus:ring-slate-950 disabled:cursor-not-allowed disabled:opacity-50 @error('customer_id') border-red-500 focus:ring-red-500 @enderror" required>
                                <option value="">Select a customer...</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}" @selected((string) old('customer_id') === (string) $customer->id)>{{ $customer->name }}</option>
                                @endforeach
                            </select>
                            <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-slate-400">
                                <svg class="size-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m7 15 5 5 5-5M7 9l5-5 5 5"/></svg>
                            </span>
                        </div>
                        @error('customer_id')
                            <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Order Date --}}
                    <div>
                        <label for="order_date" class="mb-1.5 block text-xs font-medium text-slate-700">Order date <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                            </div>
                            <input id="order_date" name="order_date" type="date" value="{{ old('order_date', now()->toDateString()) }}" class="flex h-10 w-full rounded-md border border-slate-200 bg-white pl-9 pr-3 py-2 text-sm text-slate-900 shadow-2xs ring-offset-white transition-colors focus:border-slate-950 focus:outline-none focus:ring-1 focus:ring-slate-950 disabled:cursor-not-allowed disabled:opacity-50 @error('order_date') border-red-500 focus:ring-red-500 @enderror" required>
                        </div>
                        @error('order_date')
                            <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </section>

            {{-- Card 2: Line Items Data Table (Shadcn Data Table) --}}
            <section data-aos="fade-up" data-aos-delay="200" class="overflow-hidden rounded-xl border border-slate-200/90 bg-white shadow-2xs transition-all duration-300">
                <div class="flex flex-col gap-3 border-b border-slate-100 p-6 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-base font-semibold leading-none tracking-tight text-slate-900">Line items</h2>
                        <p class="mt-1.5 text-xs text-slate-500">Products with real-time stock limits and automatic line totaling.</p>
                    </div>
                    <button id="add-item" type="button" class="inline-flex h-9 items-center justify-center gap-1.5 rounded-md border border-slate-200 bg-white px-3 text-xs font-medium text-slate-900 shadow-2xs transition-all hover:bg-slate-50 hover:text-slate-950 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-slate-950 active:scale-[0.98]">
                        <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add product
                    </button>
                </div>

                @error('items')
                    <div class="mx-6 mt-5 rounded-md border border-red-200 bg-red-50 p-3 text-xs font-medium text-red-700">{{ $message }}</div>
                @enderror

                <div class="overflow-x-auto">
                    <table class="min-w-[740px] w-full text-left text-sm">
                        <thead class="border-b border-slate-100 bg-slate-50/75 text-[11px] font-medium uppercase tracking-wider text-slate-500">
                            <tr>
                                <th class="px-6 py-3">Product</th>
                                <th class="px-4 py-3">Unit price</th>
                                <th class="w-36 px-4 py-3">Quantity</th>
                                <th class="px-4 py-3 text-right">Line total</th>
                                <th class="w-16 px-4 py-3 text-center sm:px-6"><span class="sr-only">Actions</span></th>
                            </tr>
                        </thead>
                        <tbody id="item-rows" class="divide-y divide-slate-100">
                            @foreach ($oldItems as $index => $item)
                                @php($selectedProduct = $products->firstWhere('id', $item['product_id'] ?? null))
                                <tr data-item-row class="transition-colors hover:bg-slate-50/50">
                                    <td class="px-6 py-4">
                                        <div class="relative">
                                            <select name="items[{{ $index }}][product_id]" data-product-select aria-label="Product" class="flex h-9 w-full min-w-64 appearance-none items-center justify-between rounded-md border border-slate-200 bg-white px-3 py-1.5 pr-8 text-xs text-slate-900 shadow-2xs transition-colors placeholder:text-slate-400 focus:border-slate-950 focus:outline-none focus:ring-1 focus:ring-slate-950 @error("items.$index.product_id") border-red-500 @enderror" required>
                                                <option value="">Select a product...</option>
                                                @foreach ($products as $product)
                                                    <option value="{{ $product->id }}" @selected((string) ($item['product_id'] ?? '') === (string) $product->id)>{{ $product->sku }} — {{ $product->name }}</option>
                                                @endforeach
                                            </select>
                                            <span class="pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400">
                                                <svg class="size-3.5 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m7 15 5 5 5-5M7 9l5-5 5 5"/></svg>
                                            </span>
                                        </div>

                                        <div data-stock-hint class="mt-1.5">
                                            @if ($selectedProduct)
                                                @if ($selectedProduct->stock_quantity > 5)
                                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2 py-0.5 text-[11px] font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20"><span class="size-1 rounded-full bg-emerald-500"></span>In stock: {{ $selectedProduct->stock_quantity }}</span>
                                                @elseif ($selectedProduct->stock_quantity > 0)
                                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-2 py-0.5 text-[11px] font-medium text-amber-700 ring-1 ring-inset ring-amber-600/20"><span class="size-1 rounded-full bg-amber-500"></span>Low stock: {{ $selectedProduct->stock_quantity }}</span>
                                                @else
                                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-50 px-2 py-0.5 text-[11px] font-medium text-rose-700 ring-1 ring-inset ring-rose-600/20"><span class="size-1 rounded-full bg-rose-500"></span>Out of stock</span>
                                                @endif
                                            @else
                                                <span class="text-[11px] text-slate-400">Select a product to view stock</span>
                                            @endif
                                        </div>

                                        @error("items.$index.product_id")
                                            <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                                        @enderror
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-4 font-mono text-xs font-medium text-slate-600" data-unit-price>
                                        {{ $selectedProduct ? '৳ '.number_format((float) $selectedProduct->price, 2) : '—' }}
                                    </td>
                                    <td class="px-4 py-4">
                                        {{-- Shadcn Styled Stepper Input --}}
                                        <div class="inline-flex h-8 w-28 items-center rounded-md border border-slate-200 bg-white shadow-2xs transition-all focus-within:border-slate-950 focus-within:ring-1 focus-within:ring-slate-950 @error("items.$index.quantity") border-red-500 @enderror">
                                            <button type="button" data-qty-decrement class="flex h-full w-8 items-center justify-center rounded-l-md text-slate-400 transition-colors hover:bg-slate-100 hover:text-slate-900 active:bg-slate-200" aria-label="Decrease quantity">
                                                <svg class="size-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14"/></svg>
                                            </button>
                                            <input name="items[{{ $index }}][quantity]" data-quantity type="number" min="1" max="{{ $selectedProduct?->stock_quantity }}" value="{{ $item['quantity'] ?? 1 }}" aria-label="Quantity" class="h-full w-full min-w-0 border-0 bg-transparent text-center font-mono text-xs font-semibold text-slate-900 focus:outline-none focus:ring-0 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none" required>
                                            <button type="button" data-qty-increment class="flex h-full w-8 items-center justify-center rounded-r-md text-slate-400 transition-colors hover:bg-slate-100 hover:text-slate-900 active:bg-slate-200" aria-label="Increase quantity">
                                                <svg class="size-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14m-7-7h14"/></svg>
                                            </button>
                                        </div>
                                        @error("items.$index.quantity")
                                            <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                                        @enderror
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-4 text-right font-mono text-xs font-semibold text-slate-900" data-line-total>
                                        ৳ 0.00
                                    </td>
                                    <td class="px-4 py-4 text-center sm:px-6">
                                        <button type="button" data-remove-item class="inline-flex size-8 items-center justify-center rounded-md text-slate-400 transition-colors hover:bg-rose-50 hover:text-rose-600" aria-label="Remove product">
                                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        {{-- Aside: Order Summary Card (Shadcn Card) --}}
        <aside data-aos="fade-up" data-aos-delay="300" class="h-fit rounded-xl border border-slate-200/90 bg-white p-6 shadow-2xs transition-all duration-300 xl:sticky xl:top-6">
            <div class="border-b border-slate-100 pb-4">
                <h2 class="text-base font-semibold leading-none tracking-tight text-slate-900">Order summary</h2>
                <p class="mt-1.5 text-xs text-slate-500">Live breakdown of totals and sales tax.</p>
            </div>

            <div class="mt-5">
                <label for="discount" class="mb-1.5 block text-xs font-medium text-slate-700">Special discount</label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 font-mono text-xs font-medium text-slate-400">৳</span>
                    <input id="discount" name="discount" data-discount type="number" min="0" step="0.01" value="{{ old('discount', 0) }}" class="flex h-9 w-full rounded-md border border-slate-200 bg-white pl-8 pr-3 py-1.5 font-mono text-xs font-medium text-slate-900 shadow-2xs transition-colors placeholder:text-slate-400 focus:border-slate-950 focus:outline-none focus:ring-1 focus:ring-slate-950 @error('discount') border-red-500 focus:ring-red-500 @enderror" placeholder="0.00">
                </div>
                @error('discount')
                    <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <dl class="mt-5 space-y-2.5 border-t border-slate-100 pt-4 text-xs">
                <div class="flex justify-between text-slate-500">
                    <dt>Subtotal</dt>
                    <dd data-subtotal class="font-mono font-medium text-slate-900">৳ 0.00</dd>
                </div>
                <div class="flex justify-between text-slate-500">
                    <dt>Discount</dt>
                    <dd data-discount-total class="font-mono font-medium text-emerald-600">− ৳ 0.00</dd>
                </div>
                <div class="flex justify-between text-slate-500">
                    <dt>Tax (5% VAT)</dt>
                    <dd data-tax class="font-mono font-medium text-slate-900">৳ 0.00</dd>
                </div>
            </dl>

            <div class="mt-4 border-t border-slate-100 pt-4">
                <div class="flex items-baseline justify-between">
                    <span class="text-xs font-medium uppercase tracking-wider text-slate-500">Total payable</span>
                    <span data-grand-total class="font-mono text-2xl font-bold tracking-tight text-slate-950">৳ 0.00</span>
                </div>
            </div>

            <div data-discount-warning class="mt-3 hidden rounded-md bg-rose-50 p-2.5 text-xs font-medium text-rose-700 ring-1 ring-inset ring-rose-200"></div>

            <button type="submit" class="mt-6 inline-flex h-10 w-full items-center justify-center rounded-md bg-slate-900 px-4 py-2 text-sm font-medium text-white shadow-2xs transition-all hover:bg-slate-800 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-950 focus-visible:ring-offset-2 active:scale-[0.99]">
                Create pending order
            </button>
        </aside>
    </form>

    {{-- Line Item Dynamic Template --}}
    <template id="order-item-template">
        <tr data-item-row class="animate-fade-in transition-colors hover:bg-slate-50/50">
            <td class="px-6 py-4">
                <div class="relative">
                    <select name="items[__INDEX__][product_id]" data-product-select aria-label="Product" class="flex h-9 w-full min-w-64 appearance-none items-center justify-between rounded-md border border-slate-200 bg-white px-3 py-1.5 pr-8 text-xs text-slate-900 shadow-2xs transition-colors placeholder:text-slate-400 focus:border-slate-950 focus:outline-none focus:ring-1 focus:ring-slate-950" required>
                        <option value="">Select a product...</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}">{{ $product->sku }} — {{ $product->name }}</option>
                        @endforeach
                    </select>
                    <span class="pointer-events-none absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400">
                        <svg class="size-3.5 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m7 15 5 5 5-5M7 9l5-5 5 5"/></svg>
                    </span>
                </div>
                <div data-stock-hint class="mt-1.5">
                    <span class="text-[11px] text-slate-400">Select a product to view stock</span>
                </div>
            </td>
            <td class="whitespace-nowrap px-4 py-4 font-mono text-xs font-medium text-slate-600" data-unit-price>—</td>
            <td class="px-4 py-4">
                <div class="inline-flex h-8 w-28 items-center rounded-md border border-slate-200 bg-white shadow-2xs transition-all focus-within:border-slate-950 focus-within:ring-1 focus-within:ring-slate-950">
                    <button type="button" data-qty-decrement class="flex h-full w-8 items-center justify-center rounded-l-md text-slate-400 transition-colors hover:bg-slate-100 hover:text-slate-900 active:bg-slate-200" aria-label="Decrease quantity">
                        <svg class="size-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14"/></svg>
                    </button>
                    <input name="items[__INDEX__][quantity]" data-quantity type="number" min="1" value="1" aria-label="Quantity" class="h-full w-full min-w-0 border-0 bg-transparent text-center font-mono text-xs font-semibold text-slate-900 focus:outline-none focus:ring-0 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none" required>
                    <button type="button" data-qty-increment class="flex h-full w-8 items-center justify-center rounded-r-md text-slate-400 transition-colors hover:bg-slate-100 hover:text-slate-900 active:bg-slate-200" aria-label="Increase quantity">
                        <svg class="size-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v14m-7-7h14"/></svg>
                    </button>
                </div>
            </td>
            <td class="whitespace-nowrap px-4 py-4 text-right font-mono text-xs font-semibold text-slate-900" data-line-total>৳ 0.00</td>
            <td class="px-4 py-4 text-center sm:px-6">
                <button type="button" data-remove-item class="inline-flex size-8 items-center justify-center rounded-md text-slate-400 transition-colors hover:bg-rose-50 hover:text-rose-600" aria-label="Remove product">
                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </td>
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

            function updateStockBadge(container, stock) {
                if (stock === undefined || stock === null) {
                    container.innerHTML = '<span class="text-[11px] text-slate-400">Select a product to view stock</span>';
                    return;
                }

                if (stock > 5) {
                    container.innerHTML = `<span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2 py-0.5 text-[11px] font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20"><span class="size-1 rounded-full bg-emerald-500"></span>In stock: ${stock}</span>`;
                } else if (stock > 0) {
                    container.innerHTML = `<span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-2 py-0.5 text-[11px] font-medium text-amber-700 ring-1 ring-inset ring-amber-600/20"><span class="size-1 rounded-full bg-amber-500"></span>Low stock: ${stock}</span>`;
                } else {
                    container.innerHTML = `<span class="inline-flex items-center gap-1.5 rounded-full bg-rose-50 px-2 py-0.5 text-[11px] font-medium text-rose-700 ring-1 ring-inset ring-rose-600/20"><span class="size-1 rounded-full bg-rose-500"></span>Out of stock</span>`;
                }
            }

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
                const stockContainer = row.querySelector('[data-stock-hint]');

                row.querySelector('[data-unit-price]').textContent = selectedProduct ? money(toCents(selectedProduct.price)) : '—';
                updateStockBadge(stockContainer, selectedProduct ? selectedProduct.stock : null);

                if (selectedProduct) {
                    quantityInput.max = selectedProduct.stock;
                    if (Number.parseInt(quantityInput.value, 10) > selectedProduct.stock) {
                        quantityInput.value = selectedProduct.stock;
                    }
                } else {
                    quantityInput.removeAttribute('max');
                }

                refreshSummary();
            }

            function bindRow(row) {
                const select = row.querySelector('[data-product-select]');
                const quantityInput = row.querySelector('[data-quantity]');
                const removeBtn = row.querySelector('[data-remove-item]');
                const decBtn = row.querySelector('[data-qty-decrement]');
                const incBtn = row.querySelector('[data-qty-increment]');

                select.addEventListener('change', () => refreshRow(row));
                quantityInput.addEventListener('input', refreshSummary);

                decBtn?.addEventListener('click', () => {
                    const current = Number.parseInt(quantityInput.value, 10) || 1;
                    if (current > 1) {
                        quantityInput.value = current - 1;
                        quantityInput.dispatchEvent(new Event('input'));
                    }
                });

                incBtn?.addEventListener('click', () => {
                    const current = Number.parseInt(quantityInput.value, 10) || 1;
                    const max = Number.parseInt(quantityInput.max, 10);
                    if (!max || current < max) {
                        quantityInput.value = current + 1;
                        quantityInput.dispatchEvent(new Event('input'));
                    }
                });

                removeBtn.addEventListener('click', () => {
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
