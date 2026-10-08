<x-layouts.app title="Accounting Dashboard · ALO POS">
    <div data-aos="fade-down" data-aos-duration="500">
        <p class="text-sm font-semibold uppercase tracking-wider text-indigo-600">Accounting</p>
        <h1 class="mt-1 text-3xl font-bold tracking-tight text-slate-950">Accounting dashboard</h1>
        <p class="mt-2 text-sm text-slate-600">Live balances and sales activity generated from completed sales orders.</p>
    </div>

    {{-- Financial Invariant Guard Check: Global Double-Entry Parity --}}
    <div data-aos="fade-up" data-aos-delay="50" class="mt-6">
        <x-ledger-audit-widget 
            :is-balanced="$isLedgerBalanced" 
            :total-debit="$auditTotalDebit" 
            :total-credit="$auditTotalCredit" 
            :discrepancy="$auditDiscrepancy" 
        />
    </div>

    <section class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <article data-aos="fade-up" data-aos-delay="100" class="rounded-2xl border border-indigo-100 bg-indigo-50 p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md hover:border-indigo-200">
            <p class="text-sm font-medium text-indigo-700">Sales revenue</p>
            <p class="mt-2 text-2xl font-bold tracking-tight text-indigo-950">৳ {{ number_format((float) $totalRevenue, 2) }}</p>
            <p class="mt-1 text-xs text-indigo-700">Credit-normal balance</p>
        </article>
        <article data-aos="fade-up" data-aos-delay="200" class="rounded-2xl border border-sky-100 bg-sky-50 p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md hover:border-sky-200">
            <p class="text-sm font-medium text-sky-700">Accounts receivable</p>
            <p class="mt-2 text-2xl font-bold tracking-tight text-sky-950">৳ {{ number_format((float) $totalReceivable, 2) }}</p>
            <p class="mt-1 text-xs text-sky-700">Asset balance</p>
        </article>
        <article data-aos="fade-up" data-aos-delay="300" class="rounded-2xl border border-violet-100 bg-violet-50 p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md hover:border-violet-200">
            <p class="text-sm font-medium text-violet-700">Tax payable</p>
            <p class="mt-2 text-2xl font-bold tracking-tight text-violet-950">৳ {{ number_format((float) $totalTaxPayable, 2) }}</p>
            <p class="mt-1 text-xs text-violet-700">Liability balance</p>
        </article>
        <article data-aos="fade-up" data-aos-delay="400" class="rounded-2xl border border-emerald-100 bg-emerald-50 p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md hover:border-emerald-200">
            <p class="text-sm font-medium text-emerald-700">This month’s sales</p>
            <p class="mt-2 text-2xl font-bold tracking-tight text-emerald-950">৳ {{ number_format((float) $monthSales, 2) }}</p>
            <p class="mt-1 text-xs text-emerald-700">Today: ৳ {{ number_format((float) $todaySales, 2) }}</p>
        </article>
    </section>

    <section class="mt-6 grid gap-4 md:grid-cols-2">
        <article data-aos="fade-up" data-aos-delay="450" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md">
            <p class="text-sm font-medium text-slate-600">Completed orders</p>
            <p class="mt-2 text-3xl font-bold tracking-tight text-slate-950">{{ $completedOrderCount }}</p>
        </article>
        <article data-aos="fade-up" data-aos-delay="500" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-md">
            <p class="text-sm font-medium text-slate-600">Pending orders</p>
            <p class="mt-2 text-3xl font-bold tracking-tight text-slate-950">{{ $pendingOrderCount }}</p>
        </article>
    </section>

    <section data-aos="fade-up" data-aos-delay="550" class="mt-8 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-5 py-5 sm:px-6">
            <h2 class="text-lg font-semibold text-slate-950">Chart of accounts</h2>
            <p class="mt-1 text-sm text-slate-600">Balances are derived from the latest ledger entry for each account.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-3 sm:px-6">Code</th>
                        <th class="px-5 py-3">Account</th>
                        <th class="px-5 py-3">Type</th>
                        <th class="px-5 py-3 text-right">Debit</th>
                        <th class="px-5 py-3 text-right">Credit</th>
                        <th class="px-5 py-3 text-right sm:px-6">Balance</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($accounts as $account)
                        <tr class="transition hover:bg-slate-50">
                            <td class="whitespace-nowrap px-5 py-4 font-mono text-xs text-slate-600 sm:px-6">{{ $account->code }}</td>
                            <td class="px-5 py-4"><a href="{{ route('accounting.accounts.show', $account) }}" class="font-semibold text-indigo-700 hover:text-indigo-900">{{ $account->name }}</a></td>
                            <td class="px-5 py-4 capitalize text-slate-600">{{ $account->type }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-right text-slate-700">৳ {{ number_format((float) ($account->total_debit ?? 0), 2) }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-right text-slate-700">৳ {{ number_format((float) ($account->total_credit ?? 0), 2) }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-right font-semibold text-slate-950 sm:px-6">৳ {{ number_format((float) ($account->latestLedgerEntry?->running_balance ?? 0), 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <section data-aos="fade-up" data-aos-delay="600" class="mt-8 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-5 py-5 sm:px-6">
            <h2 class="text-lg font-semibold text-slate-950">Recent journal entries</h2>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse ($recentJournalEntries as $journalEntry)
                <article class="px-5 py-5 sm:px-6">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <p class="font-semibold text-slate-900">{{ $journalEntry->reference }}</p>
                            <p class="mt-1 text-sm text-slate-600">{{ $journalEntry->description }} · {{ $journalEntry->order->customer->name }}</p>
                        </div>
                        <p class="text-sm text-slate-500">{{ $journalEntry->entry_date->format('d M Y') }}</p>
                    </div>
                    <div class="mt-3 flex flex-wrap gap-x-5 gap-y-1 text-sm text-slate-600">
                        @foreach ($journalEntry->lines as $line)
                            <span>{{ $line->account->code }} {{ $line->account->name }}: {{ (float) $line->debit > 0 ? 'Dr ৳ '.number_format((float) $line->debit, 2) : 'Cr ৳ '.number_format((float) $line->credit, 2) }}</span>
                        @endforeach
                    </div>
                </article>
            @empty
                <p class="px-5 py-12 text-center text-sm text-slate-500 sm:px-6">No journal entries yet. Complete an order to create the first entry.</p>
            @endforelse
        </div>
    </section>
</x-layouts.app>
