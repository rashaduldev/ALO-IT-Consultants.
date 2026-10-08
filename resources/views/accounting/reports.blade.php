<x-layouts.accounting title="Tax & Revenue Reports · ALO Accounting" breadcrumb="Tax & Revenue Reports">
    {{-- Header --}}
    <div data-aos="fade-down" data-aos-duration="500" class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <div class="inline-flex items-center gap-2">
                <span class="inline-flex items-center rounded-md bg-violet-50 px-2 py-0.5 text-xs font-semibold text-violet-700 ring-1 ring-inset ring-violet-700/10">Statutory Tax</span>
                <span class="text-xs font-medium uppercase tracking-wider text-slate-500">VAT &amp; Revenue</span>
            </div>
            <h1 class="mt-1.5 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Tax &amp; Revenue Compliance Report</h1>
            <p class="mt-1 text-sm text-slate-500">Statutory 5% VAT reconciliation and revenue breakdown across all completed orders.</p>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" onclick="window.print()" class="inline-flex h-9 items-center justify-center gap-1.5 rounded-md border border-slate-200 bg-white px-3.5 text-xs font-medium text-slate-700 shadow-2xs transition-colors hover:bg-slate-50 hover:text-slate-900 active:scale-[0.99]">
                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Export Tax Report
            </button>
        </div>
    </div>

    {{-- KPI Cards --}}
    <section class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
        {{-- Gross Sales --}}
        <article data-aos="fade-up" data-aos-delay="100" class="rounded-xl border border-slate-200/90 bg-white p-5 shadow-2xs transition-all duration-300 hover:-translate-y-1 hover:shadow-md">
            <div class="flex items-center justify-between text-xs font-medium text-slate-500">
                <span>Gross Order Value</span>
                <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-bold text-slate-700">Subtotal</span>
            </div>
            <div class="mt-3 text-2xl font-bold font-mono tracking-tight text-slate-900">৳ {{ $grossSales }}</div>
            <p class="mt-1 text-[11px] text-slate-500">Pre-discount order volume</p>
        </article>

        {{-- Discounts Given --}}
        <article data-aos="fade-up" data-aos-delay="200" class="rounded-xl border border-slate-200/90 bg-white p-5 shadow-2xs transition-all duration-300 hover:-translate-y-1 hover:shadow-md">
            <div class="flex items-center justify-between text-xs font-medium text-slate-500">
                <span>Total Discounts Given</span>
                <span class="rounded-full bg-amber-50 px-2 py-0.5 text-[10px] font-bold text-amber-700">Promotions</span>
            </div>
            <div class="mt-3 text-2xl font-bold font-mono tracking-tight text-amber-700">৳ {{ $totalDiscounts }}</div>
            <p class="mt-1 text-[11px] text-slate-500">Deductions applied to sales</p>
        </article>

        {{-- Taxable Base --}}
        <article data-aos="fade-up" data-aos-delay="300" class="rounded-xl border border-slate-200/90 bg-white p-5 shadow-2xs transition-all duration-300 hover:-translate-y-1 hover:shadow-md">
            <div class="flex items-center justify-between text-xs font-medium text-slate-500">
                <span>Taxable Base (Net)</span>
                <span class="rounded-full bg-indigo-50 px-2 py-0.5 text-[10px] font-bold text-indigo-700">Sales - Discount</span>
            </div>
            <div class="mt-3 text-2xl font-bold font-mono tracking-tight text-indigo-900">৳ {{ $taxableSales }}</div>
            <p class="mt-1 text-[11px] text-slate-500">Subject to 5% statutory tax</p>
        </article>

        {{-- Tax Payable (5% VAT) --}}
        <article data-aos="fade-up" data-aos-delay="400" class="rounded-xl border border-violet-200 bg-violet-50/50 p-5 shadow-2xs transition-all duration-300 hover:-translate-y-1 hover:shadow-md">
            <div class="flex items-center justify-between text-xs font-medium text-violet-700">
                <span>5% VAT Collected</span>
                <span class="rounded-full bg-violet-100 px-2 py-0.5 text-[10px] font-bold text-violet-800">Account 2100</span>
            </div>
            <div class="mt-3 text-2xl font-bold font-mono tracking-tight text-violet-950">৳ {{ $taxCollected }}</div>
            <p class="mt-1 text-[11px] text-violet-600">Total liability due to tax authority</p>
        </article>
    </section>

    {{-- Monthly Breakdown Table --}}
    <section data-aos="fade-up" data-aos-delay="250" class="mt-8 overflow-hidden rounded-xl border border-slate-200/90 bg-white shadow-2xs">
        <div class="flex items-center justify-between border-b border-slate-100 p-5 sm:px-6">
            <div>
                <h2 class="text-base font-semibold text-slate-900">Monthly Statutory Filing Schedule ({{ now()->year }})</h2>
                <p class="text-xs text-slate-500">Aggregated monthly tax and gross invoiced totals for tax filing audit</p>
            </div>
            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                <span class="size-1.5 rounded-full bg-emerald-500"></span>
                Reconciled with General Ledger
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead class="border-b border-slate-100 bg-slate-50/75 text-[11px] font-medium uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="px-6 py-3">Reporting Period</th>
                        <th class="px-4 py-3 text-center">Orders</th>
                        <th class="px-4 py-3 text-right">Gross Subtotal</th>
                        <th class="px-4 py-3 text-right">Discounts</th>
                        <th class="px-4 py-3 text-right">5% Tax (VAT)</th>
                        <th class="px-6 py-3 text-right">Net Grand Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($monthlyReport as $report)
                        <tr class="transition-colors hover:bg-slate-50/50">
                            <td class="whitespace-nowrap px-6 py-4 font-medium text-slate-900 text-xs">
                                {{ $report['month_name'] }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 text-center font-mono text-xs text-slate-600">
                                {{ $report['count'] }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 text-right font-mono text-xs text-slate-700">
                                ৳ {{ $report['subtotal'] }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 text-right font-mono text-xs text-amber-600">
                                ৳ {{ $report['discount'] }}
                            </td>
                            <td class="whitespace-nowrap px-4 py-4 text-right">
                                <span class="inline-flex rounded-md bg-violet-50 px-2 py-0.5 font-mono text-xs font-semibold text-violet-700 ring-1 ring-inset ring-violet-600/20">
                                    ৳ {{ $report['tax'] }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right font-mono text-xs font-bold text-slate-900">
                                ৳ {{ $report['grand_total'] }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-14 text-center text-xs text-slate-400">
                                No completed orders found for fiscal year {{ now()->year }}.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if (count($monthlyReport) > 0)
                    <tfoot class="border-t-2 border-slate-200 bg-slate-50/50 font-medium">
                        <tr>
                            <td class="px-6 py-3.5 text-xs font-bold text-slate-900 uppercase">FY {{ now()->year }} Totals</td>
                            <td class="px-4 py-3.5 text-center font-mono text-xs text-slate-600 font-bold">{{ collect($monthlyReport)->sum('count') }}</td>
                            <td class="px-4 py-3.5 text-right font-mono text-xs text-slate-800 font-bold">৳ {{ $grossSales }}</td>
                            <td class="px-4 py-3.5 text-right font-mono text-xs text-amber-700 font-bold">৳ {{ $totalDiscounts }}</td>
                            <td class="px-4 py-3.5 text-right font-mono text-xs text-violet-900 font-bold">৳ {{ $taxCollected }}</td>
                            <td class="px-6 py-3.5 text-right font-mono text-xs font-bold text-slate-900">৳ {{ $netGrandTotal }}</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </section>

    {{-- Audit Compliance Certificate Banner --}}
    <div data-aos="fade-up" data-aos-delay="300" class="mt-6 rounded-xl border border-slate-200 bg-white p-5 shadow-2xs">
        <div class="flex items-start gap-4">
            <div class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600">
                <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-slate-900">Statutory Compliance Note</h3>
                <p class="mt-1 text-xs text-slate-500">
                    Tax liability is calculated strictly at the statutory rate of 5.00% on net invoice values (Subtotal - Discount). All tax records are automatically posted to Account 2100 (Tax Payable) in real-time under database transactions.
                </p>
            </div>
        </div>
    </div>
</x-layouts.accounting>

