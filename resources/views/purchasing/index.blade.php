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
                    <a href="{{ route('reports.purchasing') }}" class="ml-2 inline-flex items-center rounded-md border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                        View Purchasing Report
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
