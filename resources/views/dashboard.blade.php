<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-slate-900">Stock Dashboard</h2>
            <div class="text-sm text-slate-500">
                Currency: <span class="font-semibold text-slate-700">{{ $currencySymbol }}</span>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('status'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                {{ session('status') }}
            </div>
        @endif

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-sky-500 to-cyan-400 p-5 text-white shadow-lg">
                <div class="absolute -right-8 -top-8 h-28 w-28 rounded-full bg-white/20"></div>
                <p class="text-sm font-medium text-white/90">Available Items</p>
                <p class="mt-2 text-3xl font-black">{{ number_format($availableItems) }}</p>
            </div>
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-600 to-violet-500 p-5 text-white shadow-lg">
                <div class="absolute -right-8 -top-8 h-28 w-28 rounded-full bg-white/20"></div>
                <p class="text-sm font-medium text-white/90">Total Stock Amount</p>
                <p class="mt-2 text-3xl font-black">{{ $currencySymbol }} {{ number_format($totalStockAmount, 2) }}</p>
            </div>
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-600 to-teal-400 p-5 text-white shadow-lg">
                <div class="absolute -right-8 -top-8 h-28 w-28 rounded-full bg-white/20"></div>
                <p class="text-sm font-medium text-white/90">Purchasing Count</p>
                <p class="mt-2 text-3xl font-black">{{ number_format($purchasingCount) }}</p>
            </div>
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-rose-600 to-orange-400 p-5 text-white shadow-lg">
                <div class="absolute -right-8 -top-8 h-28 w-28 rounded-full bg-white/20"></div>
                <p class="text-sm font-medium text-white/90">Sales Count</p>
                <p class="mt-2 text-3xl font-black">{{ number_format($salesCount) }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="mb-4 text-base font-bold text-slate-900">Recent Purchasing</h3>
                <div class="space-y-3">
                    @forelse($recentPurchases as $purchase)
                        <div class="rounded-lg border border-slate-100 bg-slate-50 px-3 py-2">
                            <div class="text-sm font-semibold text-slate-800">{{ $purchase->product?->name }}</div>
                            <div class="text-xs text-slate-500">
                                Qty {{ $purchase->quantity }} | {{ $currencySymbol }} {{ number_format($purchase->total_price, 2) }}
                                | {{ $purchase->source?->name ?? 'No source' }}
                            </div>
                        </div>
                    @empty
                        <div class="text-sm text-slate-500">No purchases yet.</div>
                    @endforelse
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <h3 class="mb-4 text-base font-bold text-slate-900">Recent Sales</h3>
                <div class="space-y-3">
                    @forelse($recentSales as $sale)
                        <div class="rounded-lg border border-slate-100 bg-slate-50 px-3 py-2">
                            <div class="text-sm font-semibold text-slate-800">{{ $sale->product?->name }}</div>
                            <div class="text-xs text-slate-500">
                                {{ $sale->customer_name }} | Qty {{ $sale->quantity }} | {{ $currencySymbol }} {{ number_format($sale->total_price, 2) }}
                            </div>
                        </div>
                    @empty
                        <div class="text-sm text-slate-500">No sales yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

