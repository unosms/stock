<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Stock Manager</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-slate-100 text-slate-900">
        <div class="min-h-screen">
            <div class="hidden lg:block">
                @include('layouts.navigation')
            </div>

            <div class="lg:pl-72">
                <div class="lg:hidden border-b border-slate-200 bg-white px-4 py-3">
                    <div class="flex items-center justify-between">
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                            <x-application-logo class="h-auto" style="width: 56px;" />
                            <span class="text-sm font-semibold text-slate-800">Stock Manager</span>
                        </a>
                        <a href="{{ route('items.index') }}"
                           class="rounded-md bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white">
                            Menu
                        </a>
                    </div>
                </div>

                @isset($header)
                    <header class="border-b border-slate-200 bg-white/80 backdrop-blur">
                        <div class="mx-auto max-w-7xl px-4 py-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset

                <main class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
