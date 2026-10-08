<x-layouts.accounting title="{{ $account->code }} - {{ $account->name }} · ALO Accounting" breadcrumb="Account {{ $account->code }}">
    {{-- Header --}}
    <div data-aos="fade-down" data-aos-duration="500" class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <div class="inline-flex items-center gap-2">
                <a href="{{ route('accounting.ledger') }}" class="inline-flex items-center gap-1 text-xs font-medium text-slate-500 hover:text-slate-800">
                    &larr; General Ledger
                </a>
                <span class="text-slate-300">/</span>
                <span class="inline-flex items-center rounded-md bg-slate-100 px-2 py-0.5 text-xs font-semibold uppercase text-slate-700">
                    {{ $account->type }}
                </span>
            </div>
            <h1 class="mt-1.5 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                <span class="font-mono text-slate-500">[{{ $account->code }}]</span> {{ $account->name }}
            </h1>
            <p class="mt-1 text-sm text-slate-500">
                Detailed transaction statement and running ledger balances for Account {{ $account->code }}.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('accounting.ledger', ['account' => $account->code]) }}" class="inline-flex h-9 items-center justify-center rounded-md border border-slate-200 bg-white px-3.5 text-xs font-medium text-slate-700 shadow-2xs hover:bg-slate-50">
                Filter Global Ledger
            </a>
            <button type="button" onclick="window.print()" class="inline-flex h-9 items-center justify-center gap-1.5 rounded-md bg-slate-900 px-3.5 text-xs font-semibold text-white shadow-2xs transition-all hover:bg-slate-800 active:scale-[0.99]">
                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print Statement
            </button>
        </div>
    </div>

    {{-- Account Details & Key Statistics --}}
    <section class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
        {{-- Current Balance --}}
        <article data-aos="fade-up" data-aos-delay="100" class="rounded-xl border border-slate-200/90 bg-white p-5 shadow-2xs">
            <span class="text-xs font-medium text-slate-500">Current Balance</span>
            <div class="mt-2 text-2xl font-bold font-mono text-slate-900">
                ৳ {{ number_format((float) ($account->latestLedgerEntry?->running_balance ?? 0), 2) }}
            </div>
            <p class="mt-1 text-[11px] text-slate-500">Live ledger ending balance</p>
        </article>

        {{-- Normal Balance Side --}}
        <article data-aos="fade-up" data-aos-delay="200" class="rounded-xl border border-slate-200/90 bg-white p-5 shadow-2xs">
            <span class="text-xs font-medium text-slate-500">Normal Balance Nature</span>
            <div class="mt-2 text-lg font-bold uppercase text-slate-800">
                {{ $account->type === 'asset' || $account->type === 'expense' ? 'Debit Normal (Dr)' : 'Credit Normal (Cr)' }}
            </div>
            <p class="mt-1 text-[11px] text-slate-500">Increases on {{ $account->type === 'asset' || $account->type === 'expense' ? 'Debit' : 'Credit' }}</p>
        </article>

        {{-- Total Debits Recorded --}}
        <article data-aos="fade-up" data-aos-delay="300" class="rounded-xl border border-slate-200/90 bg-white p-5 shadow-2xs">
            <span class="text-xs font-medium text-slate-500">Total Account Debits</span>
            <div class="mt-2 text-xl font-bold font-mono text-emerald-700">
                ৳ {{ number_format((float) $account->ledgerEntries()->sum('debit'), 2) }}
            </div>
            <p class="mt-1 text-[11px] text-slate-500">Cumulative debit movement</p>
        </article>

        {{-- Total Credits Recorded --}}
        <article data-aos="fade-up" data-aos-delay="400" class="rounded-xl border border-slate-200/90 bg-white p-5 shadow-2xs">
            <span class="text-xs font-medium text-slate-500">Total Account Credits</span>
            <div class="mt-2 text-xl font-bold font-mono text-sky-700">
                ৳ {{ number_format((float) $account->ledgerEntries()->sum('credit'), 2) }}
            </div>
            <p class="mt-1 text-[11px] text-slate-500">Cumulative credit movement</p>
        </article>
    </section>

    {{-- Account Ledger Table --}}
    <section data-aos="fade-up" data-aos-delay="200" class="mt-8 overflow-hidden rounded-xl border border-slate-200/90 bg-white shadow-2xs">
        <div class="flex items-center justify-between border-b border-slate-100 p-5 sm:px-6">
            <div>
                <h2 class="text-base font-semibold text-slate-900">Ledger Activity Register</h2>
                <p class="text-xs text-slate-500">Chronological entries posted to account {{ $account->code }}</p>
            </div>
            <span class="font-mono text-xs text-slate-500">Total Entries: {{ $ledgerEntries->total() }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="border-b border-slate-100 bg-slate-50/75 text-[11px] font-medium uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-6 py-3">Date</th>
                        <th class="px-4 py-3">Reference</th>
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

                            {{-- Description --}}
                            <td class="px-4 py-4 text-xs text-slate-600">
                                {{ $entry->journalEntry->description }}
                                @if ($entry->journalEntry->order)
                                    · <span class="text-slate-500">Order #{{ $entry->journalEntry->order->id }}</span>
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
                            <td colspan="6" class="px-6 py-14 text-center text-xs text-slate-400">
                                No entries posted to this account yet.
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

