@props(['logs' => []])

<div class="rounded-xl border border-slate-200/90 bg-white p-6 shadow-2xs">
    <div class="border-b border-slate-100 pb-3">
        <h3 class="text-sm font-semibold text-slate-900">Audit & State Transition Timeline</h3>
        <p class="mt-0.5 text-xs text-slate-500">Immutable ledger and state history for audit traceability.</p>
    </div>

    <div class="relative mt-6 space-y-6 pl-6 before:absolute before:bottom-2 before:left-2.5 before:top-2 before:w-0.5 before:bg-slate-200">
        @forelse ($logs as $log)
            <div class="relative">
                {{-- Event Icon Dot --}}
                <div @class([
                    'absolute -left-6 mt-0.5 flex size-5 items-center justify-center rounded-full text-[10px] font-bold text-white ring-4 ring-white shadow-2xs',
                    'bg-indigo-600' => $log->event === 'order_created',
                    'bg-amber-500' => $log->event === 'stock_deducted',
                    'bg-emerald-600' => $log->event === 'order_completed',
                    'bg-sky-600' => $log->event === 'ledger_posted',
                ])>
                    ✓
                </div>

                <div class="flex flex-col sm:flex-row sm:items-baseline sm:justify-between">
                    <span class="text-xs font-semibold text-slate-900">{{ $log->description }}</span>
                    <time class="font-mono text-[11px] text-slate-400">{{ $log->created_at->format('d M Y, h:i:s A') }}</time>
                </div>
                <p class="mt-0.5 text-[11px] text-slate-500">Initiated by: <span class="font-medium text-slate-700">{{ $log->actor }}</span></p>

                @if (!empty($log->properties))
                    <div class="mt-2 rounded-md border border-slate-100 bg-slate-50 p-2 font-mono text-[10px] text-slate-600">
                        @foreach ($log->properties as $key => $val)
                            <div><span class="text-slate-400">{{ $key }}:</span> <span class="font-semibold text-slate-800">{{ is_array($val) ? json_encode($val) : $val }}</span></div>
                        @endforeach
                    </div>
                @endif
            </div>
        @empty
            <p class="text-xs text-slate-400">No audit logs recorded for this order.</p>
        @endforelse
    </div>
</div>

