<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Throwable;

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
            'mac_address' => ['nullable', 'string', 'max:50'],
            'product_id' => ['required', 'exists:products,id'],
            'unit_price' => ['required', 'numeric', 'min:0'],
            'quantity' => ['required', 'integer', 'min:1'],
            'note' => ['nullable', 'string', 'max:255'],
            'sold_at' => ['nullable', 'date'],
        ]);

        $normalizedMacAddress = $this->normalizeMacAddress($validated['mac_address'] ?? null);

        DB::transaction(function () use ($validated, $normalizedMacAddress) {
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
                'mac_address' => $normalizedMacAddress,
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

        $redirect = redirect()
            ->route('sales.index')
            ->with('status', 'Sale saved successfully.');

        if ($normalizedMacAddress !== null) {
            $apiSync = $this->sendMacToAllowedListApi($normalizedMacAddress, $validated['note'] ?? null);
            if (! $apiSync['ok']) {
                $redirect->with('warning', 'Sale was saved, but MAC API sync failed: ' . $apiSync['message']);
            } else {
                $redirect->with('status', 'Sale saved successfully. MAC address synced.');
            }
        }

        return $redirect;
    }

    private function normalizeMacAddress(?string $macAddress): ?string
    {
        $raw = trim((string) $macAddress);
        if ($raw === '') {
            return null;
        }

        $hex = strtoupper((string) preg_replace('/[^0-9A-Fa-f]/', '', $raw));
        if (strlen($hex) !== 12 || ! ctype_xdigit($hex)) {
            throw ValidationException::withMessages([
                'mac_address' => 'MAC address format is invalid. Use format like 00:1A:2B:3C:4D:5E.',
            ]);
        }

        return implode(':', str_split($hex, 2));
    }

    private function sendMacToAllowedListApi(string $macAddress, ?string $notes): array
    {
        $url = (string) config('services.stock.allowed_mac_api_url');
        $timeout = max(5, (int) config('services.stock.allowed_mac_api_timeout', 15));

        if ($url === '') {
            return [
                'ok' => false,
                'message' => 'API URL is not configured.',
            ];
        }

        try {
            $response = Http::asJson()
                ->acceptJson()
                ->timeout($timeout)
                ->retry(2, 300)
                ->post($url, [
                    'mac_address' => $macAddress,
                    'source' => 'stock',
                    'notes' => ($notes && trim($notes) !== '') ? trim($notes) : 'new device',
                ]);

            if ($response->successful()) {
                return ['ok' => true, 'message' => 'ok'];
            }

            $body = trim((string) $response->body());
            if (strlen($body) > 300) {
                $body = substr($body, 0, 300) . '...';
            }

            return [
                'ok' => false,
                'message' => "HTTP {$response->status()}" . ($body !== '' ? " - {$body}" : ''),
            ];
        } catch (Throwable $exception) {
            return [
                'ok' => false,
                'message' => $exception->getMessage(),
            ];
        }
    }
}
