<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Products') }}
            </h2>
            <a href="{{ route('products.create') }}"
               class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                Add Product
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-5">
            @if (session('status'))
                <div class="rounded-md bg-green-50 p-4 text-sm text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->has('product'))
                <div class="rounded-md bg-rose-50 p-4 text-sm text-rose-800">
                    {{ $errors->first('product') }}
                </div>
            @endif

            <div class="rounded-lg bg-white p-4 shadow-sm ring-1 ring-gray-200">
                <form method="GET" action="{{ route('products.index') }}" class="flex flex-col gap-3 sm:flex-row">
                    <x-text-input type="text" name="q" :value="$search" placeholder="Search by name, SKU, or description"
                        class="w-full sm:max-w-md" />
                    <div class="flex gap-2">
                        <x-primary-button type="submit">Search</x-primary-button>
                        <a href="{{ route('products.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <div class="overflow-hidden rounded-lg bg-white shadow-sm ring-1 ring-gray-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Item</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">SKU</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Price</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Quantity</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse($products as $product)
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <div class="h-12 w-12 shrink-0 overflow-hidden rounded-md bg-gray-100 ring-1 ring-gray-200">
                                                @if($product->image_url)
                                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-full w-full object-cover" />
                                                @else
                                                    <div class="flex h-full items-center justify-center text-xs text-gray-400">No image</div>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="text-sm font-semibold text-gray-900">{{ $product->name }}</div>
                                                <div class="max-w-xs truncate text-xs text-gray-500">{{ $product->description ?: 'No description' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $product->sku }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">
                                        <div>Cost: <span class="font-medium">${{ number_format($product->cost_price, 2) }}</span></div>
                                        <div>Selling: <span class="font-medium">${{ number_format($product->selling_price, 2) }}</span></div>
                                    </td>
                                    <td class="px-4 py-3 text-right text-sm">
                                        <span class="font-semibold {{ $product->quantity <= $product->min_quantity ? 'text-rose-600' : 'text-gray-900' }}">
                                            {{ number_format($product->quantity) }}
                                        </span>
                                        <span class="text-gray-500">{{ $product->unit }}</span>
                                        <div class="text-xs text-gray-500">Min {{ $product->min_quantity }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        @if($product->is_active)
                                            <span class="inline-flex rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-700">Active</span>
                                        @else
                                            <span class="inline-flex rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-700">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right text-sm">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('products.edit', $product) }}"
                                               class="rounded-md border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50">
                                                Edit
                                            </a>
                                            <form action="{{ route('products.destroy', $product) }}" method="POST"
                                                  onsubmit="return confirm('Delete this product? This is blocked when stock history exists.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="rounded-md border border-rose-200 px-3 py-1.5 text-xs font-medium text-rose-700 hover:bg-rose-50">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500">
                                        No products found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-gray-100 px-4 py-3">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
