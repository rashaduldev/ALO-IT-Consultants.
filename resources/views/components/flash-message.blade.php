@if (session('success'))
    <div {{ $attributes->merge(['class' => 'mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800']) }} role="status">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div {{ $attributes->merge(['class' => 'mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800']) }} role="alert">
        {{ session('error') }}
    </div>
@endif
