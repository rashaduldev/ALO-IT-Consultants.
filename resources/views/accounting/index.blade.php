<x-layouts.accounting title="Overview Dashboard · ALO Accounting" breadcrumb="Overview">
    {{-- Top Heading & Subtitle --}}
    <div data-aos="fade-down" data-aos-duration="500" class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <div class="inline-flex items-center gap-2">
                <span class="inline-flex items-center rounded-md bg-indigo-50 px-2 py-0.5 text-xs font-semibold text-indigo-700 ring-1 ring-inset ring-indigo-700/10">Double-Entry Core</span>
                <span class="text-xs font-medium uppercase tracking-wider text-slate-500">Live Balances</span>
            </div>
            <h1 class="mt-1.5 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Accounting Overview</h1>
            <p class="mt-1 text-sm text-slate-500">Real-time financial positions, automated tax liabilities, and ledger movements.</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('accounting.ledger') }}" class="inline-flex h-9 items-center justify-center rounded-md border border-slate-200 bg-white px-3.5 text-xs font-medium text-slate-700 shadow-2xs transition-colors hover:bg-slate-50 hover:text-slate-900">
                View General Ledger
            </a>
            <a href="{{ route('orders.create') }}" class="inline-flex h-9 items-center justify-center rounded-md bg-slate-900 px-3.5 text-xs font-semibold text-white shadow-2xs transition-all hover:bg-slate-800 active:scale-[0.99]">
                + New Sales Order
            </a>
        </div>
    </div>

    {{-- Financial Guard Check: Live Ledger Audit Parity --}}
    <div data-aos="fade-up" data-aos-delay="50" class="mt-6">
        <x-ledger-audit-widget 
            :is-balanced="$isLedgerBalanced" 
            :total-debit="$auditTotalDebit" 
            :total-credit="$auditTotalCredit" 
            :discrepancy="$auditDiscrepancy" 
        />
    </div>

    {{-- Top 4 KPI Metric Cards (Staggered Hover-Lift) --}}
    <section class="mt-6 grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
        {{-- Total Revenue (Account 4000) --}}
        <article data-aos="fade-up" data-aos-delay="100" class="rounded-xl border border-slate-200/90 bg-white p-5 shadow-2xs transition-all duration-300 hover:-translate-y-1 hover:shadow-md">
            <div class="flex items-center justify-between text-xs font-medium text-slate-500">
                <span>Sales Revenue (4000)</span>
                <span class="rounded-full bg-indigo-50 px-2 py-0.5 text-[10px] font-bold text-indigo-700">Credit Normal</span>
            </div>
            <div class="mt-3 text-2xl font-bold tracking-tight text-slate-900">৳ {{ $totalRevenue }}</div>
            <p class="mt-1 text-[11px] text-slate-500">Net revenue posted from completed orders</p>
        </article>

        {{-- Accounts Receivable (Account 1100) --}}
        <article data-aos="fade-up" data-aos-delay="200" class="rounded-xl border border-slate-200/90 bg-white p-5 shadow-2xs transition-all duration-300 hover:-translate-y-1 hover:shadow-md">
            <div class="flex items-center justify-between text-xs font-medium text-slate-500">
                <span>Accounts Receivable (1100)</span>
                <span class="rounded-full bg-sky-50 px-2 py-0.5 text-[10px] font-bold text-sky-700">Asset</span>
            </div>
            <div class="mt-3 text-2xl font-bold tracking-tight text-slate-900">৳ {{ $totalReceivable }}</div>
            <p class="mt-1 text-[11px] text-slate-500">Gross receivable balance owed by customers</p>
        </article>

        {{-- Tax Payable (Account 2100) --}}
        <article data-aos="fade-up" data-aos-delay="300" class="rounded-xl border border-slate-200/90 bg-white p-5 shadow-2xs transition-all duration-300 hover:-translate-y-1 hover:shadow-md">
            <div class="flex items-center justify-between text-xs font-medium text-slate-500">
                <span>Tax Payable (2100)</span>
                <span class="rounded-full bg-violet-50 px-2 py-0.5 text-[10px] font-bold text-violet-700">5% VAT</span>
            </div>
            <div class="mt-3 text-2xl font-bold tracking-tight text-slate-900">৳ {{ $totalTaxPayable }}</div>
            <p class="mt-1 text-[11px] text-slate-500">Statutory liability collected for tax authority</p>
        </article>

        {{-- Net Operating Surplus --}}
        <article data-aos="fade-up" data-aos-delay="400" class="rounded-xl border border-slate-200/90 bg-white p-5 shadow-2xs transition-all duration-300 hover:-translate-y-1 hover:shadow-md">
            <div class="flex items-center justify-between text-xs font-medium text-slate-500">
                <span>Net Surplus Estimate</span>
                <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700">Retained</span>
            </div>
            <div class="mt-3 text-2xl font-bold tracking-tight text-slate-900">৳ {{ $netOperatingSurplus }}</div>
            <p class="mt-1 text-[11px] text-slate-500">Gross sales revenue net of tax liability</p>
        </article>
    </section>

    {{-- Visual Analytics Charts Section (Chart.js via CDN) --}}
    <section class="mt-6 grid gap-6 xl:grid-cols-3">
        {{-- Chart 1: Monthly Revenue vs Tax Collected (2 cols) --}}
        <div data-aos="fade-up" data-aos-delay="150" class="rounded-xl border border-slate-200/90 bg-white p-6 shadow-2xs xl:col-span-2">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between border-b border-slate-100 pb-4">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">Revenue &amp; Tax Velocity</h2>
                    <p class="text-xs text-slate-500">Monthly breakdown of gross taxable sales vs 5% VAT collected</p>
                </div>
                <div class="flex items-center gap-3 text-xs">
                    <span class="flex items-center gap-1.5"><span class="size-2 rounded-full bg-indigo-600"></span>Revenue</span>
                    <span class="flex items-center gap-1.5"><span class="size-2 rounded-full bg-violet-400"></span>Tax (5%)</span>
                </div>
            </div>
            <div class="mt-6 h-72">
                <canvas id="monthlyRevenueChart"></canvas>
            </div>
        </div>

        {{-- Chart 2: Account Balances Breakdown (1 col) --}}
        <div data-aos="fade-up" data-aos-delay="250" class="rounded-xl border border-slate-200/90 bg-white p-6 shadow-2xs">
            <div class="border-b border-slate-100 pb-4">
                <h2 class="text-base font-semibold text-slate-900">Balance Breakdown</h2>
                <p class="text-xs text-slate-500">Distribution by chart of accounts category</p>
            </div>
            <div class="mt-6 h-56 flex items-center justify-center">
                <canvas id="accountBreakdownChart"></canvas>
            </div>
            <div class="mt-4 grid grid-cols-2 gap-2 text-xs border-t border-slate-100 pt-3">
                <div class="flex items-center justify-between"><span class="text-slate-500">Assets:</span> <span class="font-semibold text-slate-900">৳ {{ number_format($assetBalance, 2) }}</span></div>
                <div class="flex items-center justify-between"><span class="text-slate-500">Liabilities:</span> <span class="font-semibold text-slate-900">৳ {{ number_format($liabilityBalance, 2) }}</span></div>
                <div class="flex items-center justify-between"><span class="text-slate-500">Revenue:</span> <span class="font-semibold text-slate-900">৳ {{ number_format($revenueBalance, 2) }}</span></div>
                <div class="flex items-center justify-between"><span class="text-slate-500">Equity:</span> <span class="font-semibold text-slate-900">৳ {{ number_format($equityBalance, 2) }}</span></div>
            </div>
        </div>
    </section>

    {{-- Recent Journal Entries Table --}}
    <section data-aos="fade-up" data-aos-delay="300" class="mt-8 overflow-hidden rounded-xl border border-slate-200/90 bg-white shadow-2xs">
        <div class="flex items-center justify-between border-b border-slate-100 p-5 sm:px-6">
            <div>
                <h2 class="text-base font-semibold text-slate-900">Recent Journal Postings</h2>
                <p class="text-xs text-slate-500">Double-entry audit log created automatically upon sales order completion</p>
            </div>
            <a href="{{ route('accounting.ledger') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                View all movements &rarr;
            </a>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse ($recentJournalEntries as $journalEntry)
                <article class="p-5 sm:px-6 transition-colors hover:bg-slate-50/50">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-mono text-xs font-bold text-slate-900">{{ $journalEntry->reference }}</span>
                                <span class="rounded bg-slate-100 px-1.5 py-0.5 text-[10px] font-semibold text-slate-600">Order #{{ $journalEntry->order_id }}</span>
                            </div>
                            <p class="mt-1 text-xs text-slate-600">{{ $journalEntry->description }} · Customer: <span class="font-medium text-slate-900">{{ $journalEntry->order->customer->name }}</span></p>
                        </div>
                        <time class="text-xs font-medium text-slate-400">{{ $journalEntry->entry_date->format('d M Y') }}</time>
                    </div>

                    {{-- Individual Debit/Credit Badges --}}
                    <div class="mt-3 flex flex-wrap gap-2 text-xs">
                        @foreach ($journalEntry->lines as $line)
                            <div class="inline-flex items-center gap-1.5 rounded-md border border-slate-200 bg-white px-2.5 py-1 text-[11px] shadow-2xs">
                                <span class="font-mono font-medium text-slate-500">{{ $line->account->code }}</span>
                                <span class="text-slate-700">{{ $line->account->name }}</span>
                                @if ((float) $line->debit > 0)
                                    <span class="rounded bg-emerald-50 px-1.5 py-0.2 font-mono font-bold text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                                        Dr ৳ {{ number_format((float) $line->debit, 2) }}
                                    </span>
                                @else
                                    <span class="rounded bg-sky-50 px-1.5 py-0.2 font-mono font-bold text-sky-700 ring-1 ring-inset ring-sky-600/20">
                                        Cr ৳ {{ number_format((float) $line->credit, 2) }}
                                    </span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </article>
            @empty
                <div class="p-12 text-center text-xs text-slate-400">
                    No journal entries recorded yet. Complete an order to generate the first entry.
                </div>
            @endforelse
        </div>
    </section>

    @push('scripts')
        <script>
            // Chart 1: Monthly Revenue vs Tax
            const revCtx = document.getElementById('monthlyRevenueChart');
            if (revCtx) {
                new Chart(revCtx, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($monthlyLabels) !!},
                        datasets: [
                            {
                                label: 'Revenue',
                                data: {!! json_encode($monthlyRevenue) !!},
                                backgroundColor: '#4f46e5',
                                borderRadius: 6,
                            },
                            {
                                label: 'Tax (5%)',
                                data: {!! json_encode($monthlyTax) !!},
                                backgroundColor: '#a78bfa',
                                borderRadius: 6,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: '#f1f5f9' },
                                ticks: { font: { size: 10 } }
                            },
                            x: {
                                grid: { display: false },
                                ticks: { font: { size: 10 } }
                            }
                        }
                    }
                });
            }

            // Chart 2: Account Balances Breakdown
            const accCtx = document.getElementById('accountBreakdownChart');
            if (accCtx) {
                new Chart(accCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Assets', 'Liabilities', 'Revenue', 'Equity'],
                        datasets: [{
                            data: [{{ $assetBalance }}, {{ $liabilityBalance }}, {{ $revenueBalance }}, {{ $equityBalance }}],
                            backgroundColor: ['#0284c7', '#8b5cf6', '#4f46e5', '#10b981'],
                            borderWidth: 2,
                            borderColor: '#ffffff',
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        cutout: '70%'
                    }
                });
            }
        </script>
    @endpush
</x-layouts.accounting>
