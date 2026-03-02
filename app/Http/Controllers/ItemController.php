<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Source;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));

        $items = Product::query()
            ->with('source:id,name')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $sources = Source::query()->where('is_active', true)->orderBy('name')->get();

        return view('items.index', compact('items', 'sources', 'search'));
    }

    public function create()
    {
        $sources = Source::query()->where('is_active', true)->orderBy('name')->get();

        return view('items.create', compact('sources'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'source_id' => ['nullable', 'exists:sources,id'],
            'new_source_name' => ['nullable', 'string', 'max:150'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
        ]);

        $sourceId = $validated['source_id'] ?? null;
        if (!empty($validated['new_source_name'])) {
            $source = Source::query()->firstOrCreate(
                ['name' => trim($validated['new_source_name'])],
                ['is_active' => true]
            );
            $sourceId = $source->id;
        }

        $itemData = [
            'sku' => $this->generateSku(),
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'source_id' => $sourceId,
            'unit' => 'pcs',
            'cost_price' => 0,
            'selling_price' => 0,
            'quantity' => 0,
            'min_quantity' => 0,
            'is_active' => true,
        ];

        if ($request->hasFile('image')) {
            $itemData['image_path'] = $request->file('image')->store('items', 'public');
        }

        Product::create($itemData);

        return redirect()->route('items.index')->with('status', 'Item added successfully.');
    }

    public function edit(Product $item)
    {
        $sources = Source::query()->where('is_active', true)->orderBy('name')->get();

        return view('items.edit', compact('item', 'sources'));
    }

    public function update(Request $request, Product $item)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'source_id' => ['nullable', 'exists:sources,id'],
            'new_source_name' => ['nullable', 'string', 'max:150'],
            'is_active' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
        ]);

        $sourceId = $validated['source_id'] ?? null;
        if (!empty($validated['new_source_name'])) {
            $source = Source::query()->firstOrCreate(
                ['name' => trim($validated['new_source_name'])],
                ['is_active' => true]
            );
            $sourceId = $source->id;
        }

        $itemData = [
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'source_id' => $sourceId,
            'is_active' => $request->boolean('is_active', true),
        ];

        if ($request->hasFile('image')) {
            if ($item->image_path) {
                Storage::disk('public')->delete($item->image_path);
            }
            $itemData['image_path'] = $request->file('image')->store('items', 'public');
        }

        $item->update($itemData);

        return redirect()->route('items.index')->with('status', 'Item updated successfully.');
    }

    public function image(string $path): StreamedResponse
    {
        $path = ltrim($path, '/');

        abort_unless(Storage::disk('public')->exists($path), 404);

        return Storage::disk('public')->response($path);
    }

    private function generateSku(): string
    {
        do {
            $sku = 'ITM-' . strtoupper(Str::random(8));
        } while (Product::query()->where('sku', $sku)->exists());

        return $sku;
    }
}
