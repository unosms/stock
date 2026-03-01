<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-slate-900">Items</h2>
            <a href="{{ route('items.create') }}" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                Add New Item
            </a>
        </div>
    </x-slot>

    <div class="space-y-5">
        @if (session('status'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                {{ session('status') }}
            </div>
        @endif

        <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <form method="GET" action="{{ route('items.index') }}" class="flex flex-col gap-3 sm:flex-row">
                <x-text-input type="text" name="q" :value="$search" class="w-full sm:max-w-md"
                    placeholder="Search by item name, description, SKU" />
                <x-primary-button type="submit">Search</x-primary-button>
            </form>
        </div>

        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Picture</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Item Name</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Source</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-slate-500">Quantity</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-slate-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($items as $item)
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="h-14 w-14 overflow-hidden rounded-lg bg-slate-100 ring-1 ring-slate-200">
                                        @if($item->image_url)
                                            <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="h-full w-full object-cover">
                                        @else
                                            <div class="flex h-full items-center justify-center text-xs text-slate-400">No image</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="text-sm font-semibold text-slate-900">{{ $item->name }}</div>
                                    <div class="max-w-xs truncate text-xs text-slate-500">{{ $item->description ?: 'No description' }}</div>
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-700">{{ $item->source?->name ?? 'N/A' }}</td>
                                <td class="px-4 py-3 text-right text-sm font-semibold text-slate-900">{{ number_format($item->quantity) }}</td>
                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('items.edit', $item) }}"
                                        class="rounded-md border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50">
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-sm text-slate-500">No items yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-slate-100 px-4 py-3">
                {{ $items->links() }}
            </div>
        </div>
    </div>
</x-app-layout>

