<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-slate-900">Edit Item</h2>
    </x-slot>

    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
        <form method="POST" action="{{ route('items.update', $item) }}" enctype="multipart/form-data" class="grid grid-cols-1 gap-5 md:grid-cols-2">
            @csrf
            @method('PUT')

            <div>
                <x-input-label for="name" :value="'Item Name'" />
                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $item->name)" required />
            </div>

            <div>
                <x-input-label for="source_id" :value="'Source'" />
                <select id="source_id" name="source_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Select source</option>
                    @foreach($sources as $source)
                        <option value="{{ $source->id }}" @selected(old('source_id', $item->source_id) == $source->id)>{{ $source->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2">
                <x-input-label for="new_source_name" :value="'Or Add New Source'" />
                <x-text-input id="new_source_name" name="new_source_name" type="text" class="mt-1 block w-full" :value="old('new_source_name')" />
            </div>

            <div class="md:col-span-2">
                <x-input-label for="description" :value="'Description'" />
                <textarea id="description" name="description" rows="4"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $item->description) }}</textarea>
            </div>

            <div class="md:col-span-2">
                <x-input-label for="image" :value="'Picture of Item'" />
                <input id="image" name="image" type="file" accept="image/*"
                    class="mt-1 block w-full rounded-md border border-gray-300 bg-white p-2 text-sm text-gray-900" />
                @if($item->image_url)
                    <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="mt-3 h-20 w-20 rounded-lg object-cover ring-1 ring-slate-200" />
                @endif
            </div>

            <div class="md:col-span-2">
                <label class="inline-flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $item->is_active))
                        class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="text-sm text-slate-700">Active item</span>
                </label>
            </div>

            <div class="md:col-span-2 flex items-center gap-3">
                <x-primary-button>Update Item</x-primary-button>
                <a href="{{ route('items.index') }}" class="rounded-md border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                    Back
                </a>
            </div>
        </form>
    </div>
</x-app-layout>

