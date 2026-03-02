<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Purchase;
use App\Models\Source;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class PurchasingController extends Controller
{
    public function index()
    {
        $items = Product::query()->where('is_active', true)->orderBy('name')->get();
        $sources = Source::query()->where('is_active', true)->orderBy('name')->get();

        $purchases = Purchase::query()
            ->with(['product:id,name,sku', 'source:id,name'])
            ->orderByDesc('purchased_at')
            ->orderByDesc('id')
            ->paginate(15);

        return view('purchasing.index', compact('items', 'sources', 'purchases'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'source_id' => ['nullable', 'exists:sources,id'],
            'new_source_name' => ['nullable', 'string', 'max:150'],
            'unit_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'quantity' => ['required', 'integer', 'min:1'],
            'note' => ['nullable', 'string', 'max:255'],
            'purchased_at' => ['nullable', 'date'],
        ]);

        $sourceId = $validated['source_id'] ?? null;
        if (!empty($validated['new_source_name'])) {
            $source = Source::query()->firstOrCreate(
                ['name' => trim($validated['new_source_name'])],
                ['is_active' => true]
            );
            $sourceId = $source->id;
        }

        DB::transaction(function () use ($validated, $sourceId) {
            /** @var Product $item */
            $item = Product::query()->lockForUpdate()->findOrFail($validated['product_id']);

            $before = (int) $item->quantity;
            $quantity = (int) $validated['quantity'];
            $after = $before + $quantity;
            $price = (float) $validated['unit_price'];
            $sellingPrice = (float) $validated['selling_price'];

            Purchase::create([
                'product_id' => $item->id,
                'source_id' => $sourceId,
                'user_id' => Auth::id(),
                'unit_price' => $price,
                'quantity' => $quantity,
                'total_price' => $price * $quantity,
                'purchased_at' => $validated['purchased_at'] ?? now(),
                'note' => $validated['note'] ?? null,
            ]);

            $item->update([
                'quantity' => $after,
                'cost_price' => $price,
                'selling_price' => $sellingPrice,
                'source_id' => $sourceId ?? $item->source_id,
            ]);

            StockMovement::create([
                'product_id' => $item->id,
                'user_id' => Auth::id(),
                'type' => 'in',
                'quantity' => $quantity,
                'quantity_before' => $before,
                'quantity_after' => $after,
                'unit_price' => $price,
                'note' => 'Purchased stock',
                'moved_at' => $validated['purchased_at'] ?? now(),
            ]);
        });

        return redirect()->route('purchasing.index')->with('status', 'Purchase saved successfully.');
    }
}
