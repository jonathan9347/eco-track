<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen bg-white text-zinc-900 transition-colors dark:bg-zinc-900 dark:text-zinc-100">
    <!-- Top Header Bar - Full Width, Sticky -->
    <header class="fixed top-0 left-0 right-0 z-50 flex h-12 items-center border-b border-zinc-200 bg-white px-4 transition-colors dark:border-zinc-800 dark:bg-zinc-950">
        <!-- Logo (left) -->
        <div class="flex items-center gap-2">
            <img
                src="{{ asset('assets/eco-track-auth-logo.png') }}"
                alt="{{ __('Eco Track logo') }}"
                class="h-7 w-7 rounded-md object-contain"
            />
            <span class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Eco Track</span>
        </div>

        <!-- Command Palette (centered) - pure Alpine.js -->
        <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2">
            <x-command-palette />
        </div>

        <div class="ml-auto hidden items-center lg:flex">
            <a href="#" class="px-3 text-sm font-medium text-zinc-700 transition hover:text-zinc-900 dark:text-zinc-300 dark:hover:text-zinc-100">
                Contact Us
            </a>
            <span aria-hidden="true" class="h-4 w-px bg-zinc-300 dark:bg-zinc-700"></span>
            <a href="#" class="px-3 text-sm font-medium text-zinc-700 transition hover:text-zinc-900 dark:text-zinc-300 dark:hover:text-zinc-100">
                About Us
            </a>
            <div class="ml-10 flex items-center">
                <x-desktop-user-menu />
            </div>
        </div>
    </header>

    <!-- Page Content -->
    <div class="pt-12">
        <x-layouts::app.sidebar :title="$title ?? null">
            <flux:main>
                {{ $slot }}
            </flux:main>
        </x-layouts::app.sidebar>
    </div>

    @fluxScripts
</body>
</html>
