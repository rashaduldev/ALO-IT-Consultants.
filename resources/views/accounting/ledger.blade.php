<x-layouts.app title="{{ $account->name }} Ledger · ALO POS">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wider text-indigo-600">Account ledger</p>
            <h1 class="mt-1 text-3xl font-bold tracking-tight text-slate-950">{{ $account->name }}</h1>
            <p class="mt-2 text-sm text-slate-600">{{ $account->code }} · <span class="capitalize">{{ $account->type }}</span> account · Running balance follows its normal balance.</p>
        </div>
        <a href="{{ route('accounting.index') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Back to dashboard</a>
    </div>

    <section class="mt-8 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                    <tr>
                        <th class="px-5 py-3 sm:px-6">Date</th>
                        <th class="px-5 py-3">Reference</th>
                        <th class="px-5 py-3">Description</th>
                        <th class="px-5 py-3 text-right">Debit</th>
                        <th class="px-5 py-3 text-right">Credit</th>
                        <th class="px-5 py-3 text-right sm:px-6">Running balance</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($ledgerEntries as $entry)
                        <tr>
                            <td class="whitespace-nowrap px-5 py-4 text-slate-600 sm:px-6">{{ $entry->journalEntry->entry_date->format('d M Y') }}</td>
                            <td class="whitespace-nowrap px-5 py-4 font-mono text-xs text-slate-600">{{ $entry->journalEntry->reference }}</td>
                            <td class="px-5 py-4 text-slate-700">{{ $entry->journalEntry->description }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-right text-slate-700">{{ (float) $entry->debit > 0 ? '৳ '.number_format((float) $entry->debit, 2) : '—' }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-right text-slate-700">{{ (float) $entry->credit > 0 ? '৳ '.number_format((float) $entry->credit, 2) : '—' }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-right font-semibold text-slate-900 sm:px-6">৳ {{ number_format((float) $entry->running_balance, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-14 text-center text-sm text-slate-500">No ledger movements for this account.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($ledgerEntries->hasPages())
            <div class="border-t border-slate-200 px-5 py-4 sm:px-6">{{ $ledgerEntries->links() }}</div>
        @endif
    </section>
</x-layouts.app>
