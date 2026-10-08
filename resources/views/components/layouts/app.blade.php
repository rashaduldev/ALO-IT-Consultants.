<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? 'ALO POS' }}</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" integrity="sha512-1cK78a1o+ht2JcaW6g8U8eiHdnyxU7076WKWYK48EX42hEx4in6XCUN2ma85ckx74ZD53fc9z7hyGhEGt74ecg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    </head>
    <body class="min-h-screen bg-slate-50 text-slate-900 antialiased">
        <header class="border-b border-slate-200 bg-white">
            <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 py-4 sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8">
                <a href="{{ route('orders.index') }}" class="flex items-center gap-3 font-semibold text-slate-950">
                    <span class="grid size-9 place-items-center rounded-lg bg-indigo-600 text-sm font-bold text-white">AP</span>
                    <span>ALO POS</span>
                </a>

                <nav aria-label="Primary navigation" class="flex flex-wrap items-center gap-x-1 gap-y-2 text-sm font-medium">
                    <a href="{{ route('orders.index') }}" @class(['rounded-lg px-3 py-2 transition', 'bg-indigo-50 text-indigo-700' => request()->routeIs('orders.index'), 'text-slate-600 hover:bg-slate-100 hover:text-slate-950' => ! request()->routeIs('orders.index')])>Orders</a>
                    <a href="{{ route('orders.create') }}" @class(['rounded-lg px-3 py-2 transition', 'bg-indigo-50 text-indigo-700' => request()->routeIs('orders.create'), 'text-slate-600 hover:bg-slate-100 hover:text-slate-950' => ! request()->routeIs('orders.create')])>New Order</a>
                    <span class="cursor-not-allowed rounded-lg px-3 py-2 text-slate-400" title="Available in a later step">Customers</span>
                    <span class="cursor-not-allowed rounded-lg px-3 py-2 text-slate-400" title="Available in a later step">Products</span>
                    <a href="{{ route('accounting.index') }}" @class(['rounded-lg px-3 py-2 transition', 'bg-indigo-50 text-indigo-700' => request()->routeIs('accounting.*'), 'text-slate-600 hover:bg-slate-100 hover:text-slate-950' => ! request()->routeIs('accounting.*')])>Accounting Dashboard</a>
                </nav>
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <x-flash-message />
            {{ $slot }}
        </main>

        @stack('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js" integrity="sha512-A7AYk1fGKX6S2SsHywmPkrnzTZHrgiVT7GcQkLGDe2ev0aWb8zejytzS8wjo7PGEXKqJOrjQ4oORtnimIRZBtw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                AOS.init({
                    duration: 600,
                    easing: 'ease-in-out',
                    once: true,
                    offset: 40,
                });
            });
        </script>
    </body>
</html>
