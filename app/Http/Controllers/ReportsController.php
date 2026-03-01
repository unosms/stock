<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Sale;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function sales(Request $request)
    {
        $from = $request->query('from');
        $to = $request->query('to');
        $itemId = $request->query('item_id');

        $items = Product::query()->orderBy('name')->get(['id', 'name', 'sku']);

        $sales = Sale::query()
            ->with('product:id,name,sku')
            ->when($itemId, fn ($query) => $query->where('product_id', $itemId))
            ->when($from, fn ($query) => $query->whereDate('sold_at', '>=', $from))
            ->when($to, fn ($query) => $query->whereDate('sold_at', '<=', $to))
            ->orderByDesc('sold_at')
            ->paginate(25)
            ->withQueryString();

        $summaryQuery = Sale::query()
            ->when($itemId, fn ($query) => $query->where('product_id', $itemId))
            ->when($from, fn ($query) => $query->whereDate('sold_at', '>=', $from))
            ->when($to, fn ($query) => $query->whereDate('sold_at', '<=', $to));

        $totalSalesAmount = (float) (clone $summaryQuery)->sum('total_price');
        $totalSalesQty = (int) (clone $summaryQuery)->sum('quantity');

        $currencySymbol = Currency::query()->where('is_default', true)->value('symbol') ?? '$';

        return view('reports.sales', compact(
            'sales',
            'items',
            'from',
            'to',
            'itemId',
            'totalSalesAmount',
            'totalSalesQty',
            'currencySymbol'
        ));
    }

    public function purchasing(Request $request)
    {
        $from = $request->query('from');
        $to = $request->query('to');
        $itemId = $request->query('item_id');

        $items = Product::query()->orderBy('name')->get(['id', 'name', 'sku']);

        $purchases = Purchase::query()
            ->with(['product:id,name,sku', 'source:id,name'])
            ->when($itemId, fn ($query) => $query->where('product_id', $itemId))
            ->when($from, fn ($query) => $query->whereDate('purchased_at', '>=', $from))
            ->when($to, fn ($query) => $query->whereDate('purchased_at', '<=', $to))
            ->orderByDesc('purchased_at')
            ->paginate(25)
            ->withQueryString();

        $summaryQuery = Purchase::query()
            ->when($itemId, fn ($query) => $query->where('product_id', $itemId))
            ->when($from, fn ($query) => $query->whereDate('purchased_at', '>=', $from))
            ->when($to, fn ($query) => $query->whereDate('purchased_at', '<=', $to));

        $totalPurchasingAmount = (float) (clone $summaryQuery)->sum('total_price');
        $totalPurchasingQty = (int) (clone $summaryQuery)->sum('quantity');

        $currencySymbol = Currency::query()->where('is_default', true)->value('symbol') ?? '$';

        return view('reports.purchasing', compact(
            'purchases',
            'items',
            'from',
            'to',
            'itemId',
            'totalPurchasingAmount',
            'totalPurchasingQty',
            'currencySymbol'
        ));
    }
}
