<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-slate-900">Sales Report</h2>
    </x-slot>

    <div class="space-y-5">
        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <form method="GET" action="{{ route('reports.sales') }}" class="grid grid-cols-1 gap-3 md:grid-cols-5">
                <div>
                    <x-input-label for="item_id" :value="'Item'" />
                    <select id="item_id" name="item_id"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All items</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}" @selected((string) $itemId === (string) $item->id)>{{ $item->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <x-input-label for="from" :value="'From'" />
                    <x-text-input id="from" name="from" type="date" class="mt-1 block w-full" :value="$from" />
                </div>
                <div>
                    <x-input-label for="to" :value="'To'" />
                    <x-text-input id="to" name="to" type="date" class="mt-1 block w-full" :value="$to" />
                </div>
                <div class="flex items-end gap-2 md:col-span-2">
                    <x-primary-button type="submit">Filter</x-primary-button>
                    <a href="{{ route('reports.sales') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Reset</a>
                </div>
            </form>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-sm text-slate-500">Total Sold Quantity</p>
                <p class="mt-2 text-2xl font-extrabold text-slate-900">{{ number_format($totalSalesQty) }}</p>
            </div>
            <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-sm text-slate-500">Total Sales Amount</p>
                <p class="mt-2 text-2xl font-extrabold text-slate-900">{{ $currencySymbol }} {{ number_format($totalSalesAmount, 2) }}</p>
            </div>
        </div>

        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full table-fixed divide-y divide-slate-200" style="width:100%;">
                    <colgroup>
                        <col style="width:19%;">
                        <col style="width:23%;">
                        <col style="width:16%;">
                        <col style="width:14%;">
                        <col style="width:10%;">
                        <col style="width:18%;">
                    </colgroup>
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500 whitespace-nowrap">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Customer</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Item</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-slate-500 whitespace-nowrap">Price</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-slate-500 whitespace-nowrap">Qty</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-slate-500 whitespace-nowrap">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($sales as $sale)
                            <tr>
                                <td class="px-4 py-3 align-middle text-sm text-slate-700 whitespace-nowrap">{{ $sale->sold_at?->format('Y-m-d H:i') }}</td>
                                <td class="px-4 py-3 align-middle text-sm text-slate-900">{{ $sale->customer_name }}</td>
                                <td class="px-4 py-3 align-middle text-sm text-slate-900">{{ $sale->product?->name }}</td>
                                <td class="px-4 py-3 align-middle text-right text-sm text-slate-700 whitespace-nowrap">{{ number_format($sale->unit_price, 2) }}</td>
                                <td class="px-4 py-3 align-middle text-right text-sm text-slate-900 whitespace-nowrap">{{ number_format($sale->quantity) }}</td>
                                <td class="px-4 py-3 align-middle text-right text-sm font-semibold text-slate-900 whitespace-nowrap">{{ number_format($sale->total_price, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-sm text-slate-500">No data found.</td>
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
</x-app-layout>
