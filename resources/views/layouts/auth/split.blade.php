<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-[radial-gradient(circle_at_top,_#f4f9f1,_#e5efe1_55%,_#d6e3d3)] antialiased dark:bg-neutral-950">
        <div class="min-h-svh px-3 py-3 sm:px-4 sm:py-4 lg:px-5 lg:py-3">
            <div class="mx-auto grid min-h-[calc(100svh-1.5rem)] max-w-5xl overflow-hidden rounded-[1.35rem] border border-emerald-950/10 bg-white/90 shadow-[0_18px_50px_rgba(28,55,35,0.12)] backdrop-blur lg:h-[calc(100svh-1.5rem)] lg:grid-cols-[1.02fr_0.98fr] dark:border-white/10 dark:bg-neutral-900/95">
                <div class="relative overflow-hidden bg-[linear-gradient(180deg,_rgba(13,83,53,0.96),_rgba(8,40,27,0.98))] p-5 text-white sm:p-6 lg:p-7">
                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,_rgba(134,239,172,0.22),_transparent_35%),radial-gradient(circle_at_bottom_left,_rgba(251,191,36,0.18),_transparent_30%)]"></div>
                    <div class="relative flex h-full flex-col">
                        <div class="flex-1">
                            {{ $aside ?? '' }}
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-center p-4 sm:p-5 lg:p-6">
                    <div class="w-full max-w-sm">
                        <a href="{{ route('home') }}" class="mb-4 flex justify-center" wire:navigate>
                            <img
                                src="{{ asset('assets/eco-track-auth-logo.png') }}"
                                alt="{{ config('app.name', 'Eco Track') }}"
                                class="h-14 w-14 rounded-2xl object-contain shadow-sm ring-1 ring-emerald-950/10 dark:ring-white/10"
                            />
                        </a>

                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
        @fluxScripts
    </body>
</html>
