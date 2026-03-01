<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Stock Movements') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-5">
            @if (session('status'))
                <div class="rounded-md bg-green-50 p-4 text-sm text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-md bg-rose-50 p-4 text-sm text-rose-800">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="rounded-lg bg-white p-6 shadow-sm ring-1 ring-gray-200">
                <h3 class="text-base font-semibold text-gray-900">Add Stock Movement</h3>
                <form method="POST" action="{{ route('stock-movements.store') }}" class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-6">
                    @csrf
                    <div class="md:col-span-2">
                        <x-input-label for="product_id" :value="'Product'" />
                        <select id="product_id" name="product_id" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select product</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" @selected(old('product_id') == $product->id)>
                                    {{ $product->name }} ({{ $product->sku }}) - QTY {{ $product->quantity }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <x-input-label for="type" :value="'Type'" />
                        <select id="type" name="type" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="in" @selected(old('type') === 'in')>Stock In</option>
                            <option value="out" @selected(old('type') === 'out')>Stock Out</option>
                            <option value="adjustment" @selected(old('type') === 'adjustment')>Adjustment</option>
                        </select>
                    </div>

                    <div>
                        <x-input-label for="quantity" :value="'Quantity'" />
                        <x-text-input id="quantity" name="quantity" type="number" min="1" required class="mt-1 block w-full"
                            :value="old('quantity', 1)" />
                    </div>

                    <div>
                        <x-input-label for="adjustment_direction" :value="'Adjustment'" />
                        <select id="adjustment_direction" name="adjustment_direction"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="increase" @selected(old('adjustment_direction') === 'increase')>Increase</option>
                            <option value="decrease" @selected(old('adjustment_direction') === 'decrease')>Decrease</option>
                        </select>
                    </div>

                    <div>
                        <x-input-label for="unit_price" :value="'Unit Price'" />
                        <x-text-input id="unit_price" name="unit_price" type="number" step="0.01" min="0"
                            class="mt-1 block w-full" :value="old('unit_price')" />
                    </div>

                    <div class="md:col-span-3">
                        <x-input-label for="note" :value="'Note'" />
                        <x-text-input id="note" name="note" type="text" class="mt-1 block w-full"
                            :value="old('note')" />
                    </div>

                    <div class="md:col-span-2">
                        <x-input-label for="moved_at" :value="'Date & Time'" />
                        <x-text-input id="moved_at" name="moved_at" type="datetime-local" class="mt-1 block w-full"
                            :value="old('moved_at')" />
                    </div>

                    <div class="md:col-span-1 flex items-end">
                        <x-primary-button class="w-full justify-center">Save</x-primary-button>
                    </div>
                </form>
            </div>

            <div class="rounded-lg bg-white p-4 shadow-sm ring-1 ring-gray-200">
                <form method="GET" action="{{ route('stock-movements.index') }}" class="grid grid-cols-1 gap-3 md:grid-cols-5">
                    <div class="md:col-span-2">
                        <x-input-label for="filter_product_id" :value="'Filter by Product'" />
                        <select id="filter_product_id" name="product_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All products</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" @selected((string) $productId === (string) $product->id)>
                                    {{ $product->name }} ({{ $product->sku }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <x-input-label for="filter_type" :value="'Filter by Type'" />
                        <select id="filter_type" name="type"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">All types</option>
                            <option value="initial" @selected($type === 'initial')>Initial</option>
                            <option value="in" @selected($type === 'in')>In</option>
                            <option value="out" @selected($type === 'out')>Out</option>
                            <option value="adjustment" @selected($type === 'adjustment')>Adjustment</option>
                        </select>
                    </div>
                    <div class="flex items-end gap-2 md:col-span-2">
                        <x-primary-button type="submit">Filter</x-primary-button>
                        <a href="{{ route('stock-movements.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
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
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Product</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Type</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Change</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Before</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">After</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">By</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Note</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse($movements as $movement)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $movement->moved_at?->format('Y-m-d H:i') }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        <div class="font-medium text-gray-900">{{ $movement->product?->name ?? 'Deleted product' }}</div>
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
                                    <td class="px-4 py-3 text-right text-sm text-gray-700">{{ number_format($movement->quantity_before) }}</td>
                                    <td class="px-4 py-3 text-right text-sm text-gray-900 font-semibold">{{ number_format($movement->quantity_after) }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $movement->user?->name ?? 'System' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $movement->note }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-8 text-center text-sm text-gray-500">
                                        No stock movements yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-gray-100 px-4 py-3">
                    {{ $movements->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

