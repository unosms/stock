<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-slate-900">Purchasing</h2>
    </x-slot>

    <div class="space-y-5">
        @if (session('status'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <form method="POST" action="{{ route('purchasing.store') }}" class="grid grid-cols-1 gap-4 md:grid-cols-2">
                @csrf
                <div>
                    <x-input-label for="product_id" :value="'Select Item'" />
                    <select id="product_id" name="product_id" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Choose item</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}" @selected(old('product_id') == $item->id)>
                                {{ $item->name }} ({{ $item->sku }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <x-input-label for="source_id" :value="'Source'" />
                    <select id="source_id" name="source_id"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Choose source</option>
                        @foreach($sources as $source)
                            <option value="{{ $source->id }}" @selected(old('source_id') == $source->id)>{{ $source->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <x-input-label for="new_source_name" :value="'Or Add New Source'" />
                    <x-text-input id="new_source_name" name="new_source_name" type="text" class="mt-1 block w-full" :value="old('new_source_name')" />
                </div>

                <div>
                    <x-input-label for="unit_price" :value="'Price'" />
                    <x-text-input id="unit_price" name="unit_price" type="number" step="0.01" min="0" class="mt-1 block w-full"
                        :value="old('unit_price')" required />
                </div>

                <div>
                    <x-input-label for="selling_price" :value="'Sales Price'" />
                    <x-text-input id="selling_price" name="selling_price" type="number" step="0.01" min="0" class="mt-1 block w-full"
                        :value="old('selling_price')" required />
                </div>

                <div>
                    <x-input-label for="quantity" :value="'Quantity'" />
                    <x-text-input id="quantity" name="quantity" type="number" min="1" class="mt-1 block w-full"
                        :value="old('quantity', 1)" required />
                </div>

                <div>
                    <x-input-label for="purchased_at" :value="'Date & Time'" />
                    <x-text-input id="purchased_at" name="purchased_at" type="datetime-local" class="mt-1 block w-full"
                        :value="old('purchased_at', now()->format('Y-m-d\\TH:i'))" />
                </div>

                <div>
                    <x-input-label for="note" :value="'Note'" />
                    <x-text-input id="note" name="note" type="text" class="mt-1 block w-full" :value="old('note')" />
                </div>

                <div class="md:col-span-2">
                    <x-primary-button>Save Purchase</x-primary-button>
                </div>
            </form>
        </div>

        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Item</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Source</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-slate-500">Price</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-slate-500">Sales Price</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-slate-500">Qty</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-slate-500">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($purchases as $purchase)
                            <tr>
                                <td class="px-4 py-3 text-sm text-slate-700">{{ $purchase->purchased_at?->format('Y-m-d H:i') }}</td>
                                <td class="px-4 py-3 text-sm font-medium text-slate-900">{{ $purchase->product?->name }}</td>
                                <td class="px-4 py-3 text-sm text-slate-700">{{ $purchase->source?->name ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-right text-sm text-slate-700">{{ number_format($purchase->unit_price, 2) }}</td>
                                <td class="px-4 py-3 text-right text-sm text-slate-700">{{ number_format($purchase->product?->selling_price ?? 0, 2) }}</td>
                                <td class="px-4 py-3 text-right text-sm text-slate-900">{{ number_format($purchase->quantity) }}</td>
                                <td class="px-4 py-3 text-right text-sm font-semibold text-slate-900">{{ number_format($purchase->total_price, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-sm text-slate-500">No purchasing records yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-100 px-4 py-3">
                {{ $purchases->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
