<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Stock Dashboard') }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('products.create') }}"
                   class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                    Add Product
                </a>
                <a href="{{ route('stock-movements.index') }}"
                   class="inline-flex items-center rounded-md bg-gray-800 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-700">
                    Stock Movements
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="rounded-md bg-green-50 p-4 text-sm text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
                    <p class="text-sm text-gray-500">Total Products</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($totalProducts) }}</p>
                </div>
                <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
                    <p class="text-sm text-gray-500">Active Products</p>
                    <p class="mt-2 text-3xl font-bold text-indigo-600">{{ number_format($activeProducts) }}</p>
                </div>
                <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
                    <p class="text-sm text-gray-500">Low Stock</p>
                    <p class="mt-2 text-3xl font-bold text-amber-500">{{ number_format($lowStockProducts) }}</p>
                </div>
                <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
                    <p class="text-sm text-gray-500">Out of Stock</p>
                    <p class="mt-2 text-3xl font-bold text-rose-600">{{ number_format($outOfStockProducts) }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
                    <p class="text-sm text-gray-500">Inventory Cost Value</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900">${{ number_format($inventoryCostValue, 2) }}</p>
                </div>
                <div class="rounded-lg bg-white p-5 shadow-sm ring-1 ring-gray-200">
                    <p class="text-sm text-gray-500">Inventory Sales Value</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900">${{ number_format($inventorySalesValue, 2) }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-5">
                <div class="rounded-lg bg-white shadow-sm ring-1 ring-gray-200 lg:col-span-3">
                    <div class="border-b border-gray-100 px-5 py-4">
                        <h3 class="text-base font-semibold text-gray-900">Recent Stock Movements</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Date</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Product</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Type</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Qty</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @forelse($recentMovements as $movement)
                                    <tr>
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ $movement->moved_at?->format('Y-m-d H:i') }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900">
                                            {{ $movement->product?->name ?? 'Deleted product' }}
                                            <div class="text-xs text-gray-500">{{ $movement->product?->sku }}</div>
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium
                                                {{ in_array($movement->type, ['in', 'initial']) ? 'bg-green-100 text-green-700' : '' }}
                                                {{ $movement->type === 'out' ? 'bg-rose-100 text-rose-700' : '' }}
                                                {{ $movement->type === 'adjustment' ? 'bg-amber-100 text-amber-700' : '' }}">
                                                {{ ucfirst($movement->type) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-right text-sm font-semibold {{ $movement->quantity >= 0 ? 'text-green-700' : 'text-rose-700' }}">
                                            {{ $movement->quantity >= 0 ? '+' : '' }}{{ number_format($movement->quantity) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-6 text-center text-sm text-gray-500">
                                            No stock movements yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="rounded-lg bg-white shadow-sm ring-1 ring-gray-200 lg:col-span-2">
                    <div class="border-b border-gray-100 px-5 py-4">
                        <h3 class="text-base font-semibold text-gray-900">Low Stock Products</h3>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @forelse($criticalProducts as $product)
                            <div class="px-5 py-3">
                                <div class="text-sm font-medium text-gray-900">{{ $product->name }}</div>
                                <div class="mt-1 text-xs text-gray-500">{{ $product->sku }}</div>
                                <div class="mt-1 text-sm">
                                    <span class="font-semibold text-rose-600">{{ $product->quantity }} {{ $product->unit }}</span>
                                    <span class="text-gray-500">/ Min {{ $product->min_quantity }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="px-5 py-6 text-sm text-gray-500">
                                Great. No low stock items.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

