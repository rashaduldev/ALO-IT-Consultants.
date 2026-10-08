<x-layouts.accounting title="General Ledger · ALO Accounting" breadcrumb="General Ledger">
    <div data-aos="fade-down" data-aos-duration="500" class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <div class="inline-flex items-center gap-2">
                <span class="inline-flex items-center rounded-md bg-slate-100 px-2 py-0.5 text-xs font-medium text-slate-800 ring-1 ring-inset ring-slate-500/10">Ledger Book</span>
                <span class="text-xs font-medium uppercase tracking-wider text-slate-500">General Ledger</span>
            </div>
            <h1 class="mt-1.5 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">General Ledger Postings</h1>
            <p class="mt-1 text-sm text-slate-500">Chronological transaction journal with running balances across all active accounts.</p>
        </div>

        {{-- Filter by specific account --}}
        <form method="GET" action="{{ route('accounting.ledger') }}" class="flex items-center gap-2">
            <select name="account" onchange="this.form.submit()" aria-label="Filter by account" class="h-9 rounded-md border border-slate-200 bg-white px-3 py-1.5 text-xs font-medium text-slate-700 shadow-2xs focus:border-slate-950 focus:outline-none focus:ring-1 focus:ring-slate-950 cursor-pointer">
                <option value="">All Accounts (Global Ledger)</option>
                @foreach ($accounts as $account)
                    <option value="{{ $account->code }}" @selected($selectedAccountCode === $account->code)>
                        {{ $account->code }} — {{ $account->name }} ({{ ucfirst($account->type) }})
                    </option>
                @endforeach
            </select>
            @if ($selectedAccountCode)
                <a href="{{ route('accounting.ledger') }}" class="inline-flex h-9 items-center rounded-md border border-dashed border-slate-300 px-2.5 text-xs font-medium text-slate-500 hover:text-slate-900">
                    Reset
                </a>
            @endif
        </form>
    </div>

    {{-- Summary Cards for Current View --}}
    <div class="mt-6 grid gap-4 sm:grid-cols-2">
        <div class="rounded-xl border border-slate-200/90 bg-white p-4 shadow-2xs">
            <span class="text-xs font-medium text-slate-500">Total Filtered Debits</span>
            <div class="mt-1 text-xl font-bold font-mono text-emerald-700">৳ {{ $totalDebits }}</div>
        </div>
        <div class="rounded-xl border border-slate-200/90 bg-white p-4 shadow-2xs">
            <span class="text-xs font-medium text-slate-500">Total Filtered Credits</span>
            <div class="mt-1 text-xl font-bold font-mono text-sky-700">৳ {{ $totalCredits }}</div>
        </div>
    </div>

    {{-- Data Table --}}
    <section class="mt-6 overflow-hidden rounded-xl border border-slate-200/90 bg-white shadow-2xs">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="border-b border-slate-100 bg-slate-50/75 text-[11px] font-medium uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-4 py-3">Reference</th>
                        <th class="px-4 py-3">Account</th>
                        <th class="px-4 py-3">Description</th>
                        <th class="px-4 py-3 text-right">Debit</th>
                        <th class="px-4 py-3 text-right">Credit</th>
                        <th class="px-6 py-3 text-right">Running Balance</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($ledgerEntries as $entry)
                        <tr class="transition-colors hover:bg-slate-50/50">
                            {{-- Date --}}
                            <td class="whitespace-nowrap px-6 py-4 text-xs text-slate-500">
                                {{ $entry->journalEntry->entry_date->format('d M Y') }}
                            </td>

                            {{-- Reference --}}
                            <td class="whitespace-nowrap px-4 py-4 font-mono text-xs font-semibold text-slate-900">
                                <a href="{{ route('orders.show', $entry->journalEntry->order_id) }}" class="hover:text-indigo-600 hover:underline">
                                    {{ $entry->journalEntry->reference }}
                                </a>
                            </td>

                            {{-- Account Code & Name --}}
                            <td class="px-4 py-4 text-xs font-medium text-slate-800">
                                <a href="{{ route('accounting.accounts.show', $entry->account) }}" class="inline-flex items-center gap-1.5 hover:text-indigo-600">
                                    <span class="font-mono text-slate-500">[{{ $entry->account->code }}]</span>
                                    <span>{{ $entry->account->name }}</span>
                                </a>
                            </td>

                            {{-- Description --}}
                            <td class="px-4 py-4 text-xs text-slate-600">
                                {{ $entry->journalEntry->description }}
                                @if ($entry->journalEntry->order?->customer)
                                    · <span class="text-slate-500">{{ $entry->journalEntry->order->customer->name }}</span>
                                @endif
                            </td>

                            {{-- Debit Badge --}}
                            <td class="whitespace-nowrap px-4 py-4 text-right">
                                @if ((float) $entry->debit > 0)
                                    <span class="inline-flex rounded-md bg-emerald-50 px-2 py-0.5 font-mono text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                                        Dr ৳ {{ number_format((float) $entry->debit, 2) }}
                                    </span>
                                @else
                                    <span class="text-xs text-slate-300">—</span>
                                @endif
                            </td>

                            {{-- Credit Badge --}}
                            <td class="whitespace-nowrap px-4 py-4 text-right">
                                @if ((float) $entry->credit > 0)
                                    <span class="inline-flex rounded-md bg-sky-50 px-2 py-0.5 font-mono text-xs font-semibold text-sky-700 ring-1 ring-inset ring-sky-600/20">
                                        Cr ৳ {{ number_format((float) $entry->credit, 2) }}
                                    </span>
                                @else
                                    <span class="text-xs text-slate-300">—</span>
                                @endif
                            </td>

                            {{-- Running Balance --}}
                            <td class="whitespace-nowrap px-6 py-4 text-right font-mono text-xs font-bold text-slate-900">
                                ৳ {{ number_format((float) $entry->running_balance, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-14 text-center text-xs text-slate-400">
                                No ledger entries found for the selected filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($ledgerEntries->hasPages())
            <div class="border-t border-slate-100 px-6 py-4">
                {{ $ledgerEntries->links() }}
            </div>
        @endif
    </section>
</x-layouts.accounting>
