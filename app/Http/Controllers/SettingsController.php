<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    public function index()
    {
        $timezones = timezone_identifiers_list();
        $currentTimezone = AppSetting::getValue('timezone', config('app.timezone'));
        $currencies = Currency::query()->orderBy('name')->get();
        $defaultCurrencyId = Currency::query()->where('is_default', true)->value('id');

        return view('settings.index', compact(
            'timezones',
            'currentTimezone',
            'currencies',
            'defaultCurrencyId'
        ));
    }

    public function updateTimezone(Request $request)
    {
        $validated = $request->validate([
            'timezone' => ['required', Rule::in(timezone_identifiers_list())],
        ]);

        AppSetting::putValue('timezone', $validated['timezone']);

        return redirect()->route('settings.index')->with('status', 'Timezone updated successfully.');
    }

    public function storeCurrency(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:10', 'unique:currencies,code'],
            'name' => ['required', 'string', 'max:50'],
            'symbol' => ['required', 'string', 'max:10'],
        ]);

        Currency::create([
            'code' => strtoupper(trim($validated['code'])),
            'name' => trim($validated['name']),
            'symbol' => trim($validated['symbol']),
            'is_default' => Currency::query()->count() === 0,
        ]);

        return redirect()->route('settings.index')->with('status', 'Currency added successfully.');
    }

    public function setDefaultCurrency(Request $request)
    {
        $validated = $request->validate([
            'currency_id' => ['required', 'exists:currencies,id'],
        ]);

        DB::transaction(function () use ($validated) {
            Currency::query()->update(['is_default' => false]);
            Currency::query()->whereKey($validated['currency_id'])->update(['is_default' => true]);
        });

        return redirect()->route('settings.index')->with('status', 'Default currency updated successfully.');
    }
}
