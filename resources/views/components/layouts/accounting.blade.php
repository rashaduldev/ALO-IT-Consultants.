<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? 'Accounting Engine · ALO Enterprise' }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <!-- AOS CSS CDN -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" integrity="sha512-1cK78a1o+ht2JcaW6g8U8eiHdnyxU7076WKWYK48EX42hEx4in6XCUN2ma85ckx74ZD53fc9z7hyGhEGt74ecg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <!-- Chart.js CDN -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

        <style>
            @media print {
                #accounting-sidebar,
                #accounting-topbar,
                .print-hidden {
                    display: none !important;
                }

                #accounting-main-wrapper {
                    margin-left: 0 !important;
                    padding: 0 !important;
                    background: #ffffff !important;
                    width: 100% !important;
                    max-width: 100% !important;
                }

                body {
                    background: #ffffff !important;
                    color: #000000 !important;
                }

                .card-container, section, article {
                    box-shadow: none !important;
                    border: 1px solid #cbd5e1 !important;
                }
            }
        </style>
    </head>
    <body class="min-h-full font-sans antialiased text-slate-900 bg-slate-50">
        {{-- ========================================== --}}
        {{-- 1. FIXED LEFT SIDEBAR (Shadcn Dark Theme)  --}}
        {{-- ========================================== --}}
        <aside id="accounting-sidebar" class="fixed inset-y-0 left-0 z-50 flex h-screen w-64 flex-col border-r border-slate-800 bg-slate-900 text-slate-100">
            {{-- Branding & Logo --}}
            <div class="flex h-16 shrink-0 items-center gap-3 border-b border-slate-800 px-6">
                <span class="grid size-9 place-items-center rounded-lg bg-indigo-500 text-sm font-bold text-white shadow-xs">
                    AE
                </span>
                <div>
                    <div class="text-sm font-bold tracking-tight text-white">ALO Accounting</div>
                    <div class="text-[10px] uppercase tracking-wider text-slate-400">Financial Engine v2.0</div>
                </div>
            </div>

            {{-- Main Navigation Links --}}
            <nav class="flex-1 space-y-1 px-3 py-4 text-xs font-medium">
                {{-- Dashboard Overview --}}
                <a href="{{ route('accounting.index') }}" @class([
                    'flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all',
                    'bg-slate-800 text-white font-semibold shadow-2xs' => request()->routeIs('accounting.index'),
                    'text-slate-400 hover:bg-slate-800/60 hover:text-slate-100' => ! request()->routeIs('accounting.index'),
                ])>
                    <svg class="size-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Dashboard Overview
                </a>

                {{-- General Ledger & Journals --}}
                <a href="{{ route('accounting.ledger') }}" @class([
                    'flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all',
                    'bg-slate-800 text-white font-semibold shadow-2xs' => request()->routeIs('accounting.ledger') || request()->routeIs('accounting.accounts.*'),
                    'text-slate-400 hover:bg-slate-800/60 hover:text-slate-100' => ! (request()->routeIs('accounting.ledger') || request()->routeIs('accounting.accounts.*')),
                ])>
                    <svg class="size-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    General Ledger &amp; Journals
                </a>

                {{-- Sales Orders & Transactions --}}
                <a href="{{ route('accounting.orders') }}" @class([
                    'flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all',
                    'bg-slate-800 text-white font-semibold shadow-2xs' => request()->routeIs('accounting.orders'),
                    'text-slate-400 hover:bg-slate-800/60 hover:text-slate-100' => ! request()->routeIs('accounting.orders'),
                ])>
                    <svg class="size-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    Sales Orders &amp; Invoices
                </a>

                {{-- Tax & Revenue Reports --}}
                <a href="{{ route('accounting.reports') }}" @class([
                    'flex items-center gap-3 rounded-lg px-3 py-2.5 transition-all',
                    'bg-slate-800 text-white font-semibold shadow-2xs' => request()->routeIs('accounting.reports'),
                    'text-slate-400 hover:bg-slate-800/60 hover:text-slate-100' => ! request()->routeIs('accounting.reports'),
                ])>
                    <svg class="size-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    Tax &amp; Revenue Reports
                </a>

                {{-- Switch back to POS register --}}
                <div class="pt-4">
                    <p class="px-3 text-[10px] font-semibold uppercase tracking-wider text-slate-500">Modules</p>
                    <a href="{{ route('orders.index') }}" class="mt-1 flex items-center gap-3 rounded-lg px-3 py-2 text-slate-400 transition hover:bg-slate-800/60 hover:text-slate-100">
                        <svg class="size-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        Switch to POS Register
                    </a>
                </div>
            </nav>

            {{-- Bottom Audit Status Widget in Sidebar --}}
            <div class="border-t border-slate-800 p-4">
                <div class="rounded-lg border border-slate-800 bg-slate-950/70 p-3">
                    <div class="flex items-center justify-between text-[11px]">
                        <span class="text-slate-400">Ledger Parity</span>
                        <span class="inline-flex items-center gap-1 font-semibold text-emerald-400">
                            <span class="size-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            HEALTHY
                        </span>
                    </div>
                    <p class="mt-1 font-mono text-[10px] text-slate-500">
                        Σ(Debit) == Σ(Credit)
                    </p>
                </div>
            </div>
        </aside>

        {{-- ========================================== --}}
        {{-- 2. MAIN CONTENT AREA & TOP BAR             --}}
        {{-- ========================================== --}}
        <div id="accounting-main-wrapper" class="ml-64 flex min-h-screen flex-col bg-slate-50">
            {{-- Top Bar (Breadcrumbs, Search, Actions) --}}
            <header id="accounting-topbar" class="sticky top-0 z-40 flex h-16 shrink-0 items-center justify-between border-b border-slate-200/90 bg-white/95 px-6 backdrop-blur-sm sm:px-8">
                {{-- Breadcrumbs --}}
                <div class="flex items-center gap-2 text-xs text-slate-500">
                    <span class="font-medium text-slate-900">Accounting</span>
                    <span>/</span>
                    <span class="text-slate-600">{{ $breadcrumb ?? 'Dashboard' }}</span>
                </div>

                {{-- Quick Actions --}}
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center gap-1.5 rounded-md border border-slate-200 bg-slate-50 px-2.5 py-1 text-xs font-medium text-slate-600">
                        <span class="size-1.5 rounded-full bg-indigo-500"></span>
                        Fiscal Year: {{ now()->year }}
                    </span>

                    <button type="button" onclick="window.print()" class="inline-flex h-8 items-center gap-1.5 rounded-md border border-slate-200 bg-white px-3 text-xs font-medium text-slate-700 shadow-2xs transition-colors hover:bg-slate-50 hover:text-slate-900 active:scale-[0.99]">
                        <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        Print Report
                    </button>
                </div>
            </header>

            {{-- Page Content --}}
            <main class="flex-1 p-6 sm:p-8">
                <x-flash-message />
                {{ $slot }}
            </main>
        </div>

        @stack('scripts')
        <!-- AOS JS CDN -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js" integrity="sha512-A7AYk1fGKX6S2SsHywmPkrnzTZHrgiVT7GcQkLGDe2ev0aWb8zejytzS8wjo7PGEXKqJOrjQ4oORtnimIRZBtw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                if (typeof AOS !== 'undefined') {
                    AOS.init({
                        duration: 500,
                        easing: 'ease-out',
                        once: true,
                        offset: 30,
                    });
                }
            });
        </script>
    </body>
</html>

