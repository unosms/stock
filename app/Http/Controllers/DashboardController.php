<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;

class DashboardController extends Controller
{
    public function index()
    {
        $availableItems = Product::where('is_active', true)->count();
        $totalStockAmount = (float) Product::query()
            ->selectRaw('COALESCE(SUM(quantity * cost_price), 0) as total')
            ->value('total');
        $purchasingCount = Purchase::count();
        $salesCount = Sale::count();

        $recentPurchases = Purchase::query()
            ->with(['product:id,name,sku', 'source:id,name'])
            ->orderByDesc('purchased_at')
            ->orderByDesc('id')
            ->limit(6)
            ->get();

        $recentSales = Sale::query()
            ->with(['product:id,name,sku'])
            ->orderByDesc('sold_at')
            ->orderByDesc('id')
            ->limit(6)
            ->get();

        $defaultCurrency = Currency::query()->where('is_default', true)->first();
        $currencySymbol = $defaultCurrency?->symbol ?? '$';

        return view('dashboard', compact(
            'availableItems',
            'totalStockAmount',
            'purchasingCount',
            'salesCount',
            'recentPurchases',
            'recentSales',
            'currencySymbol'
        ));
    }
}
