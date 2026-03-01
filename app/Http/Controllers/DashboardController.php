<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProducts = Product::count();
        $activeProducts = Product::where('is_active', true)->count();
        $lowStockProducts = Product::whereColumn('quantity', '<=', 'min_quantity')->count();
        $outOfStockProducts = Product::where('quantity', '<=', 0)->count();

        $inventoryCostValue = (float) Product::query()
            ->selectRaw('COALESCE(SUM(quantity * cost_price), 0) as total')
            ->value('total');

        $inventorySalesValue = (float) Product::query()
            ->selectRaw('COALESCE(SUM(quantity * selling_price), 0) as total')
            ->value('total');

        $recentMovements = StockMovement::query()
            ->with(['product:id,sku,name,unit', 'user:id,name'])
            ->orderByDesc('moved_at')
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        $criticalProducts = Product::query()
            ->whereColumn('quantity', '<=', 'min_quantity')
            ->orderBy('quantity')
            ->limit(8)
            ->get(['id', 'sku', 'name', 'quantity', 'min_quantity', 'unit']);

        return view('dashboard', compact(
            'totalProducts',
            'activeProducts',
            'lowStockProducts',
            'outOfStockProducts',
            'inventoryCostValue',
            'inventorySalesValue',
            'recentMovements',
            'criticalProducts'
        ));
    }
}

