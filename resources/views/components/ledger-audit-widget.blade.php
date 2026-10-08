@props([
    'isBalanced' => true,
    'totalDebit' => '0.00',
    'totalCredit' => '0.00',
    'discrepancy' => '0.00',
])

<div @class([
    'rounded-xl border p-5 shadow-2xs transition-all',
    'border-emerald-200/90 bg-emerald-50/50' => $isBalanced,
    'border-rose-300 bg-rose-50/90' => ! $isBalanced,
])>
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-start gap-3.5">
            <div @class([
                'flex size-10 shrink-0 items-center justify-center rounded-lg text-sm font-bold shadow-2xs',
                'bg-emerald-600 text-white' => $isBalanced,
                'bg-rose-600 text-white animate-pulse' => ! $isBalanced,
            ])>
                @if ($isBalanced)
                    {{-- Shield Checkmark SVG --}}
                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                @else
                    {{-- Alert Warning SVG --}}
                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                @endif
            </div>

            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <h3 class="text-sm font-bold text-slate-900">General Ledger Integrity Audit</h3>
                    @if ($isBalanced)
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-semibold text-emerald-800">
                            <span class="size-1.5 rounded-full bg-emerald-500"></span>
                            Ledger Audit Status: BALANCED (Healthy)
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-100 px-2.5 py-0.5 text-xs font-semibold text-rose-800">
                            <span class="size-1.5 rounded-full bg-rose-500"></span>
                            Ledger Audit Status: UNBALANCED (Error Alert)
                        </span>
                    @endif
                </div>
                <p class="mt-1 text-xs text-slate-600">
                    @if ($isBalanced)
                        Mathematical parity verified: SUM(Debit) equals SUM(Credit) across all journal lines in integer cents.
                    @else
                        <span class="font-semibold text-rose-700">Audit Discrepancy Detected:</span> Total debits do not match total credits. Mismatch delta: ৳ {{ $discrepancy }}
                    @endif
                </p>
            </div>
        </div>

        {{-- Verification Metrics --}}
        <div class="flex items-center gap-6 border-t border-slate-200/60 pt-3 font-mono text-xs sm:border-t-0 sm:pt-0">
            <div>
                <span class="block text-[10px] font-sans font-medium uppercase tracking-wider text-slate-500">Total Debits</span>
                <span class="font-bold text-slate-900">৳ {{ $totalDebit }}</span>
            </div>
            <div>
                <span class="block text-[10px] font-sans font-medium uppercase tracking-wider text-slate-500">Total Credits</span>
                <span class="font-bold text-slate-900">৳ {{ $totalCredit }}</span>
            </div>
            <div>
                <span class="block text-[10px] font-sans font-medium uppercase tracking-wider text-slate-500">Delta</span>
                <span @class([
                    'font-bold',
                    'text-emerald-700' => $isBalanced,
                    'text-rose-700' => ! $isBalanced,
                ])>
                    ৳ {{ $discrepancy }}
                </span>
            </div>
        </div>
    </div>
</div>

