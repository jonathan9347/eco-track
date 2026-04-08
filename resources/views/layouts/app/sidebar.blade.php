<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white text-zinc-900 transition-colors dark:bg-zinc-900 dark:text-zinc-100">
        <flux:sidebar sticky collapsible="mobile" class="eco-sidebar bg-white dark:bg-zinc-950" style="display: flex; flex-direction: column; height: 100vh;">
            <flux:sidebar.nav class="mt-0 flex-1 space-y-5 overflow-y-auto px-1 pt-1 pb-3" style="min-height: 0;">
                <flux:sidebar.group class="space-y-1.5">
                    <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate class="eco-sidebar__item">
                        Dashboard
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="clipboard-document-list" :href="route('carbon.history')" :current="request()->routeIs('carbon.history')" wire:navigate class="eco-sidebar__item">
                        My Carbon Logs
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="sparkles" :href="route('ai.predictions')" :current="request()->routeIs('ai.predictions')" wire:navigate class="eco-sidebar__item">
                        AI Predictions
                    </flux:sidebar.item>
                </flux:sidebar.group>

                <flux:sidebar.group heading="SOCIAL" class="space-y-1.5">
                    <flux:sidebar.item icon="trophy" :href="route('leaderboard')" :current="request()->routeIs('leaderboard')" wire:navigate class="eco-sidebar__item">
                        Classroom Leaderboard
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="check-badge" :href="route('achievements')" :current="request()->routeIs('achievements')" wire:navigate class="eco-sidebar__item">
                        Achievements
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="chat-bubble-left-right" :href="route('eco.chat')" :current="request()->routeIs('eco.chat')" wire:navigate class="eco-sidebar__item">
                        Eco Chat (AI Chatbot)
                    </flux:sidebar.item>
                </flux:sidebar.group>

                <flux:sidebar.group heading="RESOURCES" class="space-y-1.5">
                    <flux:sidebar.item icon="light-bulb" :href="route('eco.tips')" :current="request()->routeIs('eco.tips')" wire:navigate class="eco-sidebar__item">
                        Eco Tips
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="book-open" :href="route('reports')" :current="request()->routeIs('reports')" wire:navigate class="eco-sidebar__item">
                        Reports
                    </flux:sidebar.item>
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:sidebar.nav class="px-1 py-3" style="flex-shrink: 0;">
                <flux:sidebar.group heading="CONTROLS" class="space-y-1.5">
                    <flux:sidebar.item icon="cog-6-tooth" href="{{ url('/settings/profile') }}" :current="request()->is('settings/profile')" wire:navigate class="eco-sidebar__item">
                        Settings
                    </flux:sidebar.item>

                    @if (auth()->user()->is_admin)
                        <flux:sidebar.item icon="wrench-screwdriver" :href="route('admin')" :current="request()->routeIs('admin')" wire:navigate class="eco-sidebar__item">
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
            </flux:sidebar.nav>
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
            .eco-sidebar {
                border-right: 0 !important;
                background: #ffffff !important;
            }

            .dark .eco-sidebar {
                background: #09090b !important;
            }

            .eco-sidebar a,
            .eco-sidebar button,
            .eco-sidebar [role="button"],
            .eco-sidebar [role="link"],
            .eco-sidebar [data-current],
            .eco-sidebar .eco-sidebar__item,
            .eco-sidebar .eco-sidebar__item * {
                color: #111827 !important;
            }

            .dark .eco-sidebar a,
            .dark .eco-sidebar button,
            .dark .eco-sidebar [role="button"],
            .dark .eco-sidebar [role="link"],
            .dark .eco-sidebar [data-current],
            .dark .eco-sidebar .eco-sidebar__item,
            .dark .eco-sidebar .eco-sidebar__item * {
                color: #e5e7eb !important;
            }

            .eco-sidebar [data-flux-sidebar-group-heading],
            .eco-sidebar [data-flux-sidebar-group-heading] *,
            .eco-sidebar flux\:sidebar\.group[heading],
            .eco-sidebar flux\:sidebar\.group[heading] * {
                color: #111827 !important;
                opacity: 1 !important;
            }

            .dark .eco-sidebar [data-flux-sidebar-group-heading],
            .dark .eco-sidebar [data-flux-sidebar-group-heading] *,
            .dark .eco-sidebar flux\:sidebar\.group[heading],
            .dark .eco-sidebar flux\:sidebar\.group[heading] * {
                color: #a1a1aa !important;
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
                background: transparent !important;
            }

            .eco-sidebar a:hover,
            .eco-sidebar a:hover *,
            .eco-sidebar button:hover,
            .eco-sidebar button:hover *,
            .eco-sidebar .eco-sidebar__item:hover,
            .eco-sidebar .eco-sidebar__item:hover svg,
            .eco-sidebar .eco-sidebar__item:hover [data-flux-icon] {
                color: #059669 !important;
            }

            .eco-sidebar a:active,
            .eco-sidebar a:active *,
            .eco-sidebar button:active,
            .eco-sidebar button:active *,
            .eco-sidebar .eco-sidebar__item:active,
            .eco-sidebar .eco-sidebar__item:active svg,
            .eco-sidebar .eco-sidebar__item:active [data-flux-icon] {
                color: #059669 !important;
            }

            .eco-sidebar [data-current="true"],
            .eco-sidebar [aria-current="page"] {
                background: transparent !important;
            }

            .eco-sidebar [data-current="true"],
            .eco-sidebar [data-current="true"] *,
            .eco-sidebar [aria-current="page"],
            .eco-sidebar [aria-current="page"] * {
                color: #059669 !important;
            }
        </style>
    </body>
</html>
