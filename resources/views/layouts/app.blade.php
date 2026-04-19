<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('partials.head')
</head>
@php($pageTitle = filled($title ?? null) ? $title : config('app.name', 'Eco Track'))
<body class="h-screen overflow-hidden bg-white text-zinc-900 transition-colors dark:bg-zinc-900 dark:text-zinc-100">
    <span data-page-title="{{ $pageTitle }}" hidden></span>
    <!-- Top Header Bar - Full Width, Sticky -->
    <header class="fixed top-0 left-0 right-0 z-50 flex h-12 items-center px-4 transition-colors" style="border-bottom: 1px solid rgba(36, 88, 47, 0.24); background: #62a86d; color: #f6fff4; box-shadow: 0 10px 24px rgba(48, 92, 54, 0.14);">
        <!-- Logo (left) -->
        <div class="flex items-center gap-2">
            <img
                src="{{ asset('assets/eco-track-auth-logo.png') }}"
                alt="{{ __('Eco Track logo') }}"
                class="h-7 w-7 rounded-md object-contain"
            />
            <span class="text-sm font-bold" style="color: #f8fff6;">
                <span style="color: #d9ffd8;">Eco</span> Track
            </span>
        </div>

        <!-- Command Palette (centered) - pure Alpine.js -->
        <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2">
            <x-command-palette />
        </div>

        <div class="ml-auto hidden items-center lg:flex">
            <a href="{{ route('contact.us') }}" wire:navigate class="px-3 text-sm font-medium transition" style="color: rgba(248, 255, 246, 0.92);">
                Contact Us
            </a>
            <span aria-hidden="true" class="h-4 w-px" style="background: rgba(241, 255, 241, 0.34);"></span>
            <a href="{{ route('about.us') }}" wire:navigate class="px-3 text-sm font-medium transition" style="color: rgba(248, 255, 246, 0.92);">
                About Us
            </a>
            <div class="ml-10 flex items-center">
                <x-desktop-user-menu />
            </div>
        </div>
    </header>

    <!-- Page Content -->
    <div class="h-screen pt-12 overflow-hidden">
        <x-layouts::app.sidebar :title="$title ?? null">
            <flux:main class="h-[calc(100vh-3rem)] overflow-y-auto">
                <div class="eco-page-shell">
                    <div class="eco-page-loader" data-page-loader aria-hidden="true">
                        <div class="eco-page-loader__inner">
                            <div class="eco-page-loader__hero">
                                <div class="eco-page-loader__eyebrow"></div>
                                <div class="eco-page-loader__title"></div>
                                <div class="eco-page-loader__line eco-page-loader__line--wide"></div>
                                <div class="eco-page-loader__line"></div>
                            </div>

                            <div class="eco-page-loader__grid">
                                <div class="eco-page-loader__card"></div>
                                <div class="eco-page-loader__card"></div>
                                <div class="eco-page-loader__card eco-page-loader__card--tall"></div>
                            </div>
                        </div>
                    </div>

                    <div class="eco-page-shell__content" data-page-content>
                        {{ $slot }}
                    </div>
                </div>
            </flux:main>
        </x-layouts::app.sidebar>
    </div>

    @fluxScripts
    <style>
        .eco-page-shell {
            position: relative;
            min-height: calc(100vh - 3rem);
        }

        .eco-page-shell__content {
            transition: opacity 180ms ease, transform 180ms ease;
        }

        .eco-page-loader {
            position: absolute;
            inset: 0;
            z-index: 30;
            display: grid;
            opacity: 0;
            pointer-events: none;
            transition: opacity 180ms ease;
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.96), rgba(250, 251, 248, 0.98));
        }

        .dark .eco-page-loader {
            background:
                linear-gradient(180deg, rgba(24, 24, 27, 0.96), rgba(15, 23, 15, 0.98));
        }

        .eco-page-loader__inner {
            display: grid;
            gap: 1.4rem;
            padding: 1.5rem;
        }

        .eco-page-loader__hero,
        .eco-page-loader__card {
            overflow: hidden;
            position: relative;
            border: 1px solid rgba(113, 149, 95, 0.12);
            border-radius: 0.75rem;
            background: rgba(255, 255, 255, 0.95);
        }

        .dark .eco-page-loader__hero,
        .dark .eco-page-loader__card {
            border-color: rgba(148, 190, 150, 0.12);
            background: rgba(39, 39, 42, 0.92);
        }

        .eco-page-loader__hero::after,
        .eco-page-loader__card::after,
        .eco-page-loader__eyebrow::after,
        .eco-page-loader__title::after,
        .eco-page-loader__line::after {
            content: '';
            position: absolute;
            inset: 0;
            transform: translateX(-100%);
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.85), transparent);
            animation: eco-page-skeleton 1.15s ease-in-out infinite;
        }

        .dark .eco-page-loader__hero::after,
        .dark .eco-page-loader__card::after,
        .dark .eco-page-loader__eyebrow::after,
        .dark .eco-page-loader__title::after,
        .dark .eco-page-loader__line::after {
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.12), transparent);
        }

        .eco-page-loader__hero {
            display: grid;
            gap: 0.9rem;
            padding: 1.5rem;
            min-height: 10rem;
        }

        .eco-page-loader__eyebrow,
        .eco-page-loader__title,
        .eco-page-loader__line {
            position: relative;
            overflow: hidden;
            border-radius: 999px;
            background: #e7efe1;
        }

        .dark .eco-page-loader__eyebrow,
        .dark .eco-page-loader__title,
        .dark .eco-page-loader__line {
            background: #3f473f;
        }

        .eco-page-loader__eyebrow {
            width: 8rem;
            height: 0.7rem;
        }

        .eco-page-loader__title {
            width: min(24rem, 72%);
            height: 2.1rem;
        }

        .eco-page-loader__line {
            width: 68%;
            height: 0.95rem;
        }

        .eco-page-loader__line--wide {
            width: 84%;
        }

        .eco-page-loader__grid {
            display: grid;
            gap: 1.2rem;
        }

        .eco-page-loader__card {
            min-height: 11rem;
        }

        .eco-page-loader__card--tall {
            min-height: 18rem;
        }

        .eco-page-shell.is-loading .eco-page-loader {
            opacity: 1;
            pointer-events: auto;
        }

        .eco-page-shell.is-loading .eco-page-shell__content {
            opacity: 0;
            transform: translateY(6px);
            pointer-events: none;
        }

        @media (min-width: 960px) {
            .eco-page-loader__grid {
                grid-template-columns: 1.15fr 1.15fr 0.9fr;
            }
        }

        @keyframes eco-page-skeleton {
            100% {
                transform: translateX(100%);
            }
        }
    </style>
    <script>
        (() => {
            const shell = document.querySelector('.eco-page-shell');

            if (! shell) {
                return;
            }

            let hideTimer;

            const showLoader = () => {
                window.clearTimeout(hideTimer);
                shell.classList.add('is-loading');
            };

            const hideLoader = () => {
                hideTimer = window.setTimeout(() => {
                    shell.classList.remove('is-loading');
                }, 120);
            };

            document.addEventListener('click', (event) => {
                const target = event.target.closest('[wire\\:navigate]');

                if (! target) {
                    return;
                }

                showLoader();
            }, true);

            document.addEventListener('livewire:navigate', showLoader);
            document.addEventListener('livewire:navigated', hideLoader);
        })();
    </script>
</body>
</html>
