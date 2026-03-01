@php
    $editing = isset($product) && $product->exists;
@endphp

<div class="grid grid-cols-1 gap-6 md:grid-cols-2">
    <div>
        <x-input-label for="sku" :value="'SKU'" />
        <x-text-input id="sku" name="sku" type="text" class="mt-1 block w-full"
            :value="old('sku', $product->sku ?? '')" required />
        <x-input-error :messages="$errors->get('sku')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="name" :value="'Product Name'" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
            :value="old('name', $product->name ?? '')" required />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <div class="md:col-span-2">
        <x-input-label for="description" :value="'Description'" />
        <textarea id="description" name="description" rows="4"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $product->description ?? '') }}</textarea>
        <x-input-error :messages="$errors->get('description')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="unit" :value="'Unit (pcs, box, kg...)'" />
        <x-text-input id="unit" name="unit" type="text" class="mt-1 block w-full"
            :value="old('unit', $product->unit ?? 'pcs')" required />
        <x-input-error :messages="$errors->get('unit')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="image" :value="'Product Image'" />
        <input id="image" name="image" type="file" accept="image/*"
            class="mt-1 block w-full rounded-md border border-gray-300 bg-white p-2 text-sm text-gray-900" />
        <x-input-error :messages="$errors->get('image')" class="mt-2" />

        @if ($editing && $product->image_url)
            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="mt-3 h-20 w-20 rounded object-cover ring-1 ring-gray-200" />
        @endif
    </div>

    <div>
        <x-input-label for="cost_price" :value="'Cost Price'" />
        <x-text-input id="cost_price" name="cost_price" type="number" step="0.01" min="0" class="mt-1 block w-full"
            :value="old('cost_price', $product->cost_price ?? '0.00')" required />
        <x-input-error :messages="$errors->get('cost_price')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="selling_price" :value="'Selling Price'" />
        <x-text-input id="selling_price" name="selling_price" type="number" step="0.01" min="0" class="mt-1 block w-full"
            :value="old('selling_price', $product->selling_price ?? '0.00')" required />
        <x-input-error :messages="$errors->get('selling_price')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="quantity" :value="'Quantity'" />
        <x-text-input id="quantity" name="quantity" type="number" min="0" class="mt-1 block w-full"
            :value="old('quantity', $product->quantity ?? 0)" required />
        <x-input-error :messages="$errors->get('quantity')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="min_quantity" :value="'Minimum Quantity Alert'" />
        <x-text-input id="min_quantity" name="min_quantity" type="number" min="0" class="mt-1 block w-full"
            :value="old('min_quantity', $product->min_quantity ?? 0)" required />
        <x-input-error :messages="$errors->get('min_quantity')" class="mt-2" />
    </div>
</div>

<label class="mt-6 inline-flex items-center gap-2">
    <input type="checkbox" name="is_active" value="1"
        @checked(old('is_active', $product->is_active ?? true))
        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
    <span class="text-sm text-gray-700">Active product</span>
</label>

<div class="mt-6 flex items-center gap-3">
    <x-primary-button>{{ $submitLabel }}</x-primary-button>
    <a href="{{ route('products.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
        Cancel
    </a>
</div>

