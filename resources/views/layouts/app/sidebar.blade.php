<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white text-zinc-900 transition-colors dark:bg-zinc-900 dark:text-zinc-100">
        @php
            $activeSidebarStyle = 'background:#24582f;color:#ffffff;border-radius:0.35rem;box-shadow:0 8px 16px rgba(36,88,47,0.22);';
        @endphp
        <flux:sidebar sticky collapsible="mobile" class="eco-sidebar bg-white dark:bg-zinc-950" style="display: flex; flex-direction: column; height: 100vh;">
            <flux:sidebar.nav class="mt-0 flex-1 space-y-4 px-1 pt-1 pb-2" style="min-height: 0;">
                <flux:sidebar.group class="space-y-1.5">
                    <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate class="eco-sidebar__item" :style="request()->routeIs('dashboard') ? $activeSidebarStyle : ''">
                        Dashboard
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="clipboard-document-list" :href="route('carbon.history')" :current="request()->routeIs('carbon.history')" wire:navigate class="eco-sidebar__item" :style="request()->routeIs('carbon.history') ? $activeSidebarStyle : ''">
                        My Carbon Logs
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="sparkles" :href="route('ai.predictions')" :current="request()->routeIs('ai.predictions')" wire:navigate class="eco-sidebar__item" :style="request()->routeIs('ai.predictions') ? $activeSidebarStyle : ''">
                        AI Predictions
                    </flux:sidebar.item>
                </flux:sidebar.group>

                <flux:sidebar.group heading="SOCIAL" class="space-y-1.5">
                    <flux:sidebar.item icon="check-badge" :href="route('achievements')" :current="request()->routeIs('achievements')" wire:navigate class="eco-sidebar__item" :style="request()->routeIs('achievements') ? $activeSidebarStyle : ''">
                        Achievements
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="chat-bubble-left-right" :href="route('eco.chat')" :current="request()->routeIs('eco.chat')" wire:navigate class="eco-sidebar__item" :style="request()->routeIs('eco.chat') ? $activeSidebarStyle : ''">
                        Eco Chat (AI Chatbot)
                    </flux:sidebar.item>
                </flux:sidebar.group>

                <div class="eco-sidebar__stack">
                    <flux:sidebar.group heading="RESOURCES" class="space-y-1.5">
                        <flux:sidebar.item icon="light-bulb" :href="route('eco.tips')" :current="request()->routeIs('eco.tips')" wire:navigate class="eco-sidebar__item" :style="request()->routeIs('eco.tips') ? $activeSidebarStyle : ''">
                            Eco Tips
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="book-open" :href="route('reports')" :current="request()->routeIs('reports')" wire:navigate class="eco-sidebar__item" :style="request()->routeIs('reports') ? $activeSidebarStyle : ''">
                            Reports
                        </flux:sidebar.item>
                    </flux:sidebar.group>

                    <flux:sidebar.group heading="CONTROLS" class="space-y-1.5">
                        <flux:sidebar.item icon="cog-6-tooth" href="{{ url('/settings/profile') }}" :current="request()->is('settings/profile')" wire:navigate class="eco-sidebar__item" :style="request()->is('settings/profile') ? $activeSidebarStyle : ''">
                            Settings
                        </flux:sidebar.item>

                        @if (auth()->user()->is_admin)
                            <flux:sidebar.item icon="wrench-screwdriver" :href="route('admin')" :current="request()->routeIs('admin')" wire:navigate class="eco-sidebar__item" :style="request()->routeIs('admin') ? $activeSidebarStyle : ''">
                                Admin Panel
                            </flux:sidebar.item>
                        @endif

                        <form method="POST" action="{{ route('logout') }}" class="w-full">
                            @csrf
                            <flux:sidebar.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="eco-sidebar__item w-full cursor-pointer">
                                Logout
                            </flux:sidebar.item>
                        </form>
                    </flux:sidebar.group>
                </div>
            </flux:sidebar.nav>

            <div class="px-1 pb-2 eco-sidebar__footer" style="flex-shrink: 0;">
                <div class="eco-sidebar__info-box">
                    <p class="eco-sidebar__info-kicker">Quick Eco Boost</p>
                    <h3 class="eco-sidebar__info-title">Try one lighter habit today.</h3>
                    <p class="eco-sidebar__info-copy">Get one practical tip in seconds.</p>
                    <a href="{{ route('eco.tips') }}" wire:navigate class="eco-sidebar__info-cta">
                        Open Tips
                    </a>
                </div>
            </div>
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :avatar="auth()->user()->profileImage()"
                    :initials="auth()->user()->initials()"
                    :name="auth()->user()->name"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :src="auth()->user()->profileImage()"
                                    :name="auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        @if (Route::has('profile.edit'))
                            <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                                {{ __('Settings') }}
                            </flux:menu.item>
                        @endif
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer"
                            data-test="logout-button"
                        >
                            {{ __('Log out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @fluxScripts
        <style>
            .eco-sidebar__stack {
                display: grid;
                gap: 0.7rem;
            }

            .eco-sidebar__footer {
                margin-top: auto;
            }

            .eco-sidebar__info-box {
                border: 1px solid rgba(36, 88, 47, 0.2);
                border-radius: 0.35rem;
                background: rgba(246, 255, 244, 0.42);
                padding: 0.5rem;
                box-shadow: none;
            }

            .eco-sidebar__info-kicker {
                margin: 0;
                color: #164b24;
                font-size: 0.6rem;
                font-weight: 800;
                letter-spacing: 0.1em;
                text-transform: uppercase;
            }

            .eco-sidebar__info-title {
                margin: 0.14rem 0 0;
                color: #12351d;
                font-size: 0.78rem;
                font-weight: 800;
                line-height: 1.15;
            }

            .eco-sidebar__info-copy {
                margin: 0.14rem 0 0;
                color: rgba(18, 53, 29, 0.86);
                font-size: 0.68rem;
                line-height: 1.2;
            }

            .eco-sidebar__info-cta {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                margin-top: 0.38rem;
                width: 100%;
                border-radius: 0.35rem;
                background: #24582f;
                padding: 0.36rem 0.55rem;
                color: #ffffff;
                font-size: 0.72rem;
                font-weight: 700;
                text-decoration: none;
                transition: background-color 160ms ease;
            }

            .eco-sidebar__info-cta:hover {
                background: #1c4726;
            }

            .eco-sidebar {
                border-right: 0 !important;
                background: #4f9259 !important;
            }

            .dark .eco-sidebar {
                background: #4f9259 !important;
            }

            .dark .eco-sidebar__info-box {
                border-color: rgba(36, 88, 47, 0.2);
                background: rgba(246, 255, 244, 0.42);
                box-shadow: none;
            }

            .dark .eco-sidebar__info-kicker {
                color: #164b24;
            }

            .dark .eco-sidebar__info-title {
                color: #12351d;
            }

            .dark .eco-sidebar__info-copy {
                color: rgba(18, 53, 29, 0.86);
            }

            .eco-sidebar a,
            .eco-sidebar button,
            .eco-sidebar [role="button"],
            .eco-sidebar [role="link"],
            .eco-sidebar [data-current],
            .eco-sidebar .eco-sidebar__item,
            .eco-sidebar .eco-sidebar__item * {
                color: #ffffff !important;
            }

            .dark .eco-sidebar a,
            .dark .eco-sidebar button,
            .dark .eco-sidebar [role="button"],
            .dark .eco-sidebar [role="link"],
            .dark .eco-sidebar [data-current],
            .dark .eco-sidebar .eco-sidebar__item,
            .dark .eco-sidebar .eco-sidebar__item * {
                color: #ffffff !important;
            }

            .eco-sidebar [data-flux-sidebar-group-heading],
            .eco-sidebar [data-flux-sidebar-group-heading] *,
            .eco-sidebar flux\:sidebar\.group[heading],
            .eco-sidebar flux\:sidebar\.group[heading] *,
            .eco-sidebar [data-flux-sidebar-group] > .px-3.py-2 > .text-sm,
            .eco-sidebar [data-flux-sidebar-group] > .px-3.py-2 > div {
                color: #bff7c4 !important;
                opacity: 1 !important;
                font-size: 0.72rem !important;
                font-weight: 800 !important;
                letter-spacing: 0.08em !important;
                text-transform: uppercase !important;
            }

            .dark .eco-sidebar [data-flux-sidebar-group-heading],
            .dark .eco-sidebar [data-flux-sidebar-group-heading] *,
            .dark .eco-sidebar flux\:sidebar\.group[heading],
            .dark .eco-sidebar flux\:sidebar\.group[heading] *,
            .dark .eco-sidebar [data-flux-sidebar-group] > .px-3.py-2 > .text-sm,
            .dark .eco-sidebar [data-flux-sidebar-group] > .px-3.py-2 > div {
                color: #bff7c4 !important;
            }

            .eco-sidebar svg,
            .eco-sidebar [data-flux-icon],
            .eco-sidebar .eco-sidebar__item svg,
            .eco-sidebar .eco-sidebar__item [data-flux-icon] {
                color: inherit !important;
            }

            .eco-sidebar a:hover,
            .eco-sidebar button:hover,
            .eco-sidebar .eco-sidebar__item:hover {
                background: rgba(246, 255, 244, 0.26) !important;
            }

            .eco-sidebar a:hover,
            .eco-sidebar a:hover *,
            .eco-sidebar button:hover,
            .eco-sidebar button:hover *,
            .eco-sidebar .eco-sidebar__item:hover,
            .eco-sidebar .eco-sidebar__item:hover svg,
            .eco-sidebar .eco-sidebar__item:hover [data-flux-icon] {
                color: #ffffff !important;
            }

            .eco-sidebar a:active,
            .eco-sidebar a:active *,
            .eco-sidebar button:active,
            .eco-sidebar button:active *,
            .eco-sidebar .eco-sidebar__item:active,
            .eco-sidebar .eco-sidebar__item:active svg,
            .eco-sidebar .eco-sidebar__item:active [data-flux-icon] {
                color: #ffffff !important;
            }

            .eco-sidebar [data-current],
            .eco-sidebar [aria-current="page"] {
                background: #24582f !important;
                border-radius: 0.35rem !important;
                box-shadow: 0 8px 16px rgba(36, 88, 47, 0.22) !important;
            }

            .eco-sidebar [data-current],
            .eco-sidebar [data-current] *,
            .eco-sidebar [aria-current="page"],
            .eco-sidebar [aria-current="page"] * {
                color: #ffffff !important;
            }

            .eco-sidebar [data-current]:hover,
            .eco-sidebar [aria-current="page"]:hover {
                background: #1c4726 !important;
            }

            .eco-sidebar [data-current]:hover,
            .eco-sidebar [data-current]:hover *,
            .eco-sidebar [aria-current="page"]:hover,
            .eco-sidebar [aria-current="page"]:hover * {
                color: #ffffff !important;
            }

            .dark .eco-sidebar [data-current],
            .dark .eco-sidebar [aria-current="page"] {
                background: #24582f !important;
                box-shadow: 0 8px 16px rgba(36, 88, 47, 0.22) !important;
            }

            .dark .eco-sidebar [data-current],
            .dark .eco-sidebar [data-current] *,
            .dark .eco-sidebar [aria-current="page"],
            .dark .eco-sidebar [aria-current="page"] * {
                color: #ffffff !important;
            }

            .eco-sidebar [data-current] svg,
            .eco-sidebar [data-current] [data-flux-icon],
            .eco-sidebar [aria-current="page"] svg,
            .eco-sidebar [aria-current="page"] [data-flux-icon],
            .dark .eco-sidebar [data-current] svg,
            .dark .eco-sidebar [data-current] [data-flux-icon],
            .dark .eco-sidebar [aria-current="page"] svg,
            .dark .eco-sidebar [aria-current="page"] [data-flux-icon] {
                color: #ffffff !important;
            }

        </style>
    </body>
</html>
