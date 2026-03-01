<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SalesController extends Controller
{
    public function index()
    {
        $items = Product::query()->where('is_active', true)->orderBy('name')->get();

        $sales = Sale::query()
            ->with('product:id,name,sku,image_path')
            ->orderByDesc('sold_at')
            ->orderByDesc('id')
            ->paginate(15);

        return view('sales.index', compact('items', 'sales'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:150'],
            'customer_mobile' => ['nullable', 'string', 'max:30'],
            'product_id' => ['required', 'exists:products,id'],
            'unit_price' => ['required', 'numeric', 'min:0'],
            'quantity' => ['required', 'integer', 'min:1'],
            'note' => ['nullable', 'string', 'max:255'],
            'sold_at' => ['nullable', 'date'],
        ]);

        DB::transaction(function () use ($validated) {
            /** @var Product $item */
            $item = Product::query()->lockForUpdate()->findOrFail($validated['product_id']);

            $before = (int) $item->quantity;
            $quantity = (int) $validated['quantity'];
            $after = $before - $quantity;

            if ($after < 0) {
                throw ValidationException::withMessages([
                    'quantity' => "Not enough stock. Available: {$before}",
                ]);
            }

            $price = (float) $validated['unit_price'];

            Sale::create([
                'customer_name' => $validated['customer_name'],
                'customer_mobile' => $validated['customer_mobile'] ?? null,
                'product_id' => $item->id,
                'user_id' => Auth::id(),
                'unit_price' => $price,
                'quantity' => $quantity,
                'total_price' => $price * $quantity,
                'note' => $validated['note'] ?? null,
                'sold_at' => $validated['sold_at'] ?? now(),
            ]);

            $item->update([
                'quantity' => $after,
                'selling_price' => $price,
            ]);

            StockMovement::create([
                'product_id' => $item->id,
                'user_id' => Auth::id(),
                'type' => 'out',
                'quantity' => -$quantity,
                'quantity_before' => $before,
                'quantity_after' => $after,
                'unit_price' => $price,
                'note' => 'Sold stock',
                'moved_at' => $validated['sold_at'] ?? now(),
            ]);
        });

        return redirect()->route('sales.index')->with('status', 'Sale saved successfully.');
    }
}
