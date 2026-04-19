<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    @php($pageTitle = filled($title ?? null) ? $title : config('app.name', 'Eco Track'))
    <body class="auth-forest-page min-h-screen antialiased">
        <span data-page-title="{{ $pageTitle }}" hidden></span>
        <div class="relative min-h-svh overflow-hidden px-5 py-8 sm:px-8 sm:py-10 lg:px-12 lg:py-12">
            <div class="auth-page-orb auth-page-orb-one"></div>
            <div class="auth-page-orb auth-page-orb-two"></div>
            <div class="auth-page-grid relative mx-auto grid min-h-[calc(100svh-4rem)] max-w-[1220px] items-center gap-12 lg:grid-cols-[1.12fr_0.88fr] lg:gap-16">
                <div class="text-white">
                    <div class="max-w-xl">
                        {{ $aside ?? '' }}
                    </div>
                </div>

                <div class="flex items-center justify-center lg:justify-end">
                    <div class="w-full max-w-[25rem]">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
        @fluxScripts
    </body>
</html>
