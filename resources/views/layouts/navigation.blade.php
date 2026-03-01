<aside class="fixed inset-y-0 left-0 z-40 w-72 border-r border-slate-200 bg-white">
    <div class="flex h-full flex-col">
        <div class="border-b border-slate-200 px-5 py-4">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                <x-application-logo class="h-auto" style="width: 68px;" />
                <div>
                    <div class="text-sm font-extrabold tracking-wide text-slate-900">Lightwave</div>
                    <div class="text-xs font-medium text-slate-500">Stock Manager</div>
                </div>
            </a>
        </div>

        <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
            <a href="{{ route('dashboard') }}"
               class="block rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-700 hover:bg-slate-100' }}">
                Dashboard
            </a>
            <a href="{{ route('items.create') }}"
               class="block rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('items.create') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-700 hover:bg-slate-100' }}">
                Add New Item
            </a>
            <a href="{{ route('items.index') }}"
               class="block rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('items.index', 'items.edit') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-700 hover:bg-slate-100' }}">
                Items
            </a>
            <a href="{{ route('purchasing.index') }}"
               class="block rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('purchasing.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-700 hover:bg-slate-100' }}">
                Purchasing
            </a>
            <a href="{{ route('sales.index') }}"
               class="block rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('sales.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-700 hover:bg-slate-100' }}">
                Sales
            </a>
            <a href="{{ route('reports.purchasing') }}"
               class="block rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('reports.purchasing') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-700 hover:bg-slate-100' }}">
                Purchasing Report
            </a>
            <a href="{{ route('reports.sales') }}"
               class="block rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('reports.sales') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-700 hover:bg-slate-100' }}">
                Sales Report
            </a>
            <a href="{{ route('settings.index') }}"
               class="block rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('settings.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-700 hover:bg-slate-100' }}">
                Settings
            </a>
            <a href="{{ route('profile.edit') }}"
               class="block rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('profile.*') ? 'bg-indigo-50 text-indigo-700' : 'text-slate-700 hover:bg-slate-100' }}">
                Profile
            </a>
        </nav>

        <div class="border-t border-slate-200 px-4 py-4">
            <div class="mb-3 text-sm text-slate-600">
                Signed in as <span class="font-semibold text-slate-900">{{ Auth::user()->name }}</span>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full rounded-md bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">
                    Log Out
                </button>
            </form>
        </div>
    </div>
</aside>

