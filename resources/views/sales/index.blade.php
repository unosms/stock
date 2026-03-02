<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-slate-900">Sales</h2>
    </x-slot>

    <div class="space-y-5" x-data="salesForm()">
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
            <form method="POST" action="{{ route('sales.store') }}" class="grid grid-cols-1 gap-4 md:grid-cols-2">
                @csrf
                <div>
                    <x-input-label for="customer_name" :value="'Customer Name'" />
                    <x-text-input id="customer_name" name="customer_name" type="text" class="mt-1 block w-full"
                        :value="old('customer_name')" required />
                </div>

                <div>
                    <x-input-label for="customer_mobile" :value="'Mobile Number'" />
                    <x-text-input id="customer_mobile" name="customer_mobile" type="text" class="mt-1 block w-full"
                        :value="old('customer_mobile')" />
                </div>

                <div>
                    <x-input-label for="product_id" :value="'Select Item'" />
                    <select id="product_id" name="product_id" required x-model="selectedId" @change="syncSelected"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Choose item</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}"
                                    data-image="{{ $item->image_url }}"
                                    data-price="{{ $item->selling_price }}"
                                    data-qty="{{ $item->quantity }}"
                                    @selected(old('product_id') == $item->id)>
                                {{ $item->name }} ({{ $item->sku }}) - Available {{ $item->quantity }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="rounded-lg border border-slate-200 bg-slate-50 p-3">
                    <template x-if="selectedImage">
                        <img :src="selectedImage" alt="Selected item" class="h-20 w-20 rounded object-cover ring-1 ring-slate-200">
                    </template>
                    <template x-if="!selectedImage">
                        <div class="flex h-20 w-20 items-center justify-center rounded bg-slate-100 text-xs text-slate-400">No image</div>
                    </template>
                    <div class="mt-2 text-xs text-slate-500">
                        Available: <span class="font-semibold text-slate-700" x-text="selectedQty"></span>
                    </div>
                </div>

                <div>
                    <x-input-label for="unit_price" :value="'Price'" />
                    <x-text-input id="unit_price" name="unit_price" type="number" step="0.01" min="0" class="mt-1 block w-full"
                        x-model="unitPrice" :value="old('unit_price')" required />
                </div>

                <div>
                    <x-input-label for="quantity" :value="'Quantity'" />
                    <x-text-input id="quantity" name="quantity" type="number" min="1" class="mt-1 block w-full"
                        :value="old('quantity', 1)" required />
                </div>

                <div>
                    <x-input-label for="sold_at" :value="'Date & Time'" />
                    <x-text-input id="sold_at" name="sold_at" type="datetime-local" class="mt-1 block w-full"
                        :value="old('sold_at', now()->format('Y-m-d\\TH:i'))" />
                </div>

                <div>
                    <x-input-label for="note" :value="'Note'" />
                    <x-text-input id="note" name="note" type="text" class="mt-1 block w-full" :value="old('note')" />
                </div>

                <div class="md:col-span-2">
                    <x-primary-button>Save Sale</x-primary-button>
                </div>
            </form>
        </div>

        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full table-fixed divide-y divide-slate-200">
                    <colgroup>
                        <col style="width: 18%;">
                        <col style="width: 24%;">
                        <col style="width: 18%;">
                        <col style="width: 13%;">
                        <col style="width: 10%;">
                        <col style="width: 17%;">
                    </colgroup>
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Customer</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Item</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-slate-500">Price</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-slate-500">Qty</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-slate-500">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($sales as $sale)
                            <tr>
                                <td class="px-4 py-3 align-middle text-sm text-slate-700">{{ $sale->sold_at?->format('Y-m-d H:i') }}</td>
                                <td class="px-4 py-3 align-middle text-sm text-slate-900">
                                    <div class="font-medium">{{ $sale->customer_name }}</div>
                                    <div class="text-xs text-slate-500">{{ $sale->customer_mobile }}</div>
                                </td>
                                <td class="px-4 py-3 align-middle text-sm font-medium text-slate-900">{{ $sale->product?->name }}</td>
                                <td class="px-4 py-3 align-middle text-right text-sm text-slate-700">{{ number_format($sale->unit_price, 2) }}</td>
                                <td class="px-4 py-3 align-middle text-right text-sm text-slate-900">{{ number_format($sale->quantity) }}</td>
                                <td class="px-4 py-3 align-middle text-right text-sm font-semibold text-slate-900">{{ number_format($sale->total_price, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-sm text-slate-500">No sales records yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-100 px-4 py-3">
                {{ $sales->links() }}
            </div>
        </div>
    </div>

    <script>
        function salesForm() {
            return {
                selectedId: @js(old('product_id', '')),
                selectedImage: '',
                selectedQty: '0',
                unitPrice: @js(old('unit_price', '')),
                syncSelected() {
                    const select = document.getElementById('product_id');
                    const option = select ? select.options[select.selectedIndex] : null;
                    this.selectedImage = option?.dataset?.image || '';
                    this.selectedQty = option?.dataset?.qty || '0';
                    if (!this.unitPrice && option?.dataset?.price) {
                        this.unitPrice = option.dataset.price;
                    }
                },
                init() {
                    this.syncSelected();
                }
            };
        }
    </script>
</x-app-layout>
