<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class StockMovementController extends Controller
{
    public function index(Request $request)
    {
        $productId = $request->query('product_id');
        $type = $request->query('type');

        $products = Product::query()
            ->orderBy('name')
            ->get(['id', 'sku', 'name', 'quantity']);

        $movements = StockMovement::query()
            ->with([
                'product:id,sku,name,unit',
                'user:id,name',
            ])
            ->when($productId, fn ($query) => $query->where('product_id', $productId))
            ->when($type, fn ($query) => $query->where('type', $type))
            ->orderByDesc('moved_at')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('stock-movements.index', compact('products', 'movements', 'productId', 'type'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'type' => ['required', Rule::in(['in', 'out', 'adjustment'])],
            'quantity' => ['required', 'integer', 'min:1'],
            'adjustment_direction' => ['nullable', Rule::in(['increase', 'decrease'])],
            'unit_price' => ['nullable', 'numeric', 'min:0'],
            'note' => ['nullable', 'string', 'max:255'],
            'moved_at' => ['nullable', 'date'],
        ]);

        DB::transaction(function () use ($validated) {
            /** @var Product $product */
            $product = Product::query()->lockForUpdate()->findOrFail($validated['product_id']);

            $before = (int) $product->quantity;
            $baseQuantity = (int) $validated['quantity'];
            $delta = 0;

            if ($validated['type'] === 'in') {
                $delta = $baseQuantity;
            } elseif ($validated['type'] === 'out') {
                $delta = -$baseQuantity;
            } else {
                $direction = $validated['adjustment_direction'] ?? 'increase';
                $delta = $direction === 'decrease' ? -$baseQuantity : $baseQuantity;
            }

            $after = $before + $delta;

            if ($after < 0) {
                throw ValidationException::withMessages([
                    'quantity' => 'Stock cannot go below zero.',
                ]);
            }

            $product->update([
                'quantity' => $after,
            ]);

            StockMovement::create([
                'product_id' => $product->id,
                'user_id' => Auth::id(),
                'type' => $validated['type'],
                'quantity' => $delta,
                'quantity_before' => $before,
                'quantity_after' => $after,
                'unit_price' => $validated['unit_price'] ?? $product->cost_price,
                'note' => $validated['note'] ?? null,
                'moved_at' => $validated['moved_at'] ?? now(),
            ]);
        });

        return redirect()->route('stock-movements.index')->with('status', 'Stock movement saved successfully.');
    }
}
