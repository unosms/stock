<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-bold text-slate-900">Settings</h2>
    </x-slot>

    <div class="space-y-5" x-data="timezoneClock(@js($currentTimezone))">
        @if (session('status'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-base font-bold text-slate-900">Timezone</h3>
            <p class="mt-1 text-sm text-slate-500">
                Current timezone: <span class="font-semibold text-slate-800" x-text="timezone"></span>
                | Clock: <span class="font-semibold text-slate-800" x-text="clock"></span>
            </p>

            <form method="POST" action="{{ route('settings.timezone') }}" class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-3">
                @csrf
                <div class="md:col-span-2">
                    <x-input-label for="timezone" :value="'Select Timezone'" />
                    <select id="timezone" name="timezone" x-model="timezone" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @foreach($timezones as $tz)
                            <option value="{{ $tz }}" @selected($currentTimezone === $tz)>{{ $tz }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <x-primary-button>Save Timezone</x-primary-button>
                </div>
            </form>
        </div>

        <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-base font-bold text-slate-900">Currency</h3>
            <p class="mt-1 text-sm text-slate-500">Add currency and set default one.</p>

            <form method="POST" action="{{ route('settings.currencies.store') }}" class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-4">
                @csrf
                <div>
                    <x-input-label for="code" :value="'Code (USD)'" />
                    <x-text-input id="code" name="code" type="text" class="mt-1 block w-full" required />
                </div>
                <div>
                    <x-input-label for="name" :value="'Name'" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" required />
                </div>
                <div>
                    <x-input-label for="symbol" :value="'Symbol'" />
                    <x-text-input id="symbol" name="symbol" type="text" class="mt-1 block w-full" required />
                </div>
                <div class="flex items-end">
                    <x-primary-button>Add Currency</x-primary-button>
                </div>
            </form>

            <div class="mt-5 overflow-hidden rounded-lg border border-slate-200">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Code</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Name</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Symbol</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500">Default</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-slate-500">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($currencies as $currency)
                            <tr>
                                <td class="px-4 py-3 text-sm font-semibold text-slate-900">{{ $currency->code }}</td>
                                <td class="px-4 py-3 text-sm text-slate-700">{{ $currency->name }}</td>
                                <td class="px-4 py-3 text-sm text-slate-700">{{ $currency->symbol }}</td>
                                <td class="px-4 py-3 text-sm">
                                    @if($currency->is_default)
                                        <span class="rounded-full bg-green-100 px-2 py-0.5 text-xs font-semibold text-green-700">Default</span>
                                    @else
                                        <span class="text-slate-400">No</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    @if(!$currency->is_default)
                                        <form method="POST" action="{{ route('settings.currencies.default') }}">
                                            @csrf
                                            <input type="hidden" name="currency_id" value="{{ $currency->id }}">
                                            <button type="submit" class="rounded-md border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 hover:bg-slate-50">
                                                Make Default
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-sm text-slate-500">No currencies yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function timezoneClock(initialTimezone) {
            return {
                timezone: initialTimezone,
                clock: '',
                tick() {
                    try {
                        this.clock = new Intl.DateTimeFormat([], {
                            timeZone: this.timezone,
                            year: 'numeric',
                            month: '2-digit',
                            day: '2-digit',
                            hour: '2-digit',
                            minute: '2-digit',
                            second: '2-digit'
                        }).format(new Date());
                    } catch (e) {
                        this.clock = new Date().toLocaleString();
                    }
                },
                init() {
                    this.tick();
                    setInterval(() => this.tick(), 1000);
                }
            }
        }
    </script>
</x-app-layout>

