@php
    $user = auth()->user();
    $username = $user?->email ? '@' . \Illuminate\Support\Str::before($user->email, '@') : '@eco-track-user';
@endphp

<flux:dropdown
    position="bottom"
    align="end"
    x-data="{
        menuOpen: false,
        compactView: localStorage.getItem('eco-track-compact-view') === 'true',
        applyCompactView() {
            document.body.classList.toggle('compact-view', this.compactView);
        },
        toggleCompactView() {
            this.compactView = !this.compactView;
            localStorage.setItem('eco-track-compact-view', this.compactView ? 'true' : 'false');
            this.applyCompactView();
        },
        setTheme(theme) {
            const root = document.documentElement;
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

            if (theme === 'dark') {
                root.classList.add('dark');
                window.localStorage.setItem('flux.appearance', 'dark');
            } else if (theme === 'light') {
                root.classList.remove('dark');
                window.localStorage.setItem('flux.appearance', 'light');
            } else {
                root.classList.toggle('dark', prefersDark);
                window.localStorage.removeItem('flux.appearance');
            }

            if (window.Flux && typeof window.Flux.applyAppearance === 'function') {
                window.Flux.applyAppearance(theme);
            }

            if (window.Flux) {
                window.Flux.appearance = theme;
            }

            $flux.appearance = theme;
        }
    }"
    x-init="applyCompactView()"
    x-on:open="menuOpen = true"
    x-on:close="menuOpen = false"
>
    <div
        x-cloak
        x-show="menuOpen"
        x-transition:enter="transition duration-150 ease-out"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition duration-100 ease-in"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="pointer-events-none fixed inset-0 z-40 bg-white/10 backdrop-blur-[4px] dark:bg-zinc-950/10"
    ></div>

    <flux:avatar
        as="button"
        :src="$user->profileImage()"
        :initials="$user->initials()"
        :name="$user->name"
        size="sm"
        class="relative z-50 [--avatar-radius:0.35rem]"
        data-test="sidebar-menu-button"
    />

    <flux:menu class="relative z-50 mt-3 min-w-80">
        <div class="flex items-center gap-3 px-2 py-2.5 text-start">
            <flux:avatar
                :src="$user->profileImage()"
                :name="$user->name"
                :initials="$user->initials()"
                size="lg"
                class="[--avatar-radius:0.5rem]"
            />
            <div class="min-w-0 flex-1">
                <flux:heading class="truncate text-sm">{{ $user->name }}</flux:heading>
                <flux:text class="truncate text-xs text-zinc-500">{{ $username }}</flux:text>
            </div>
        </div>

        <flux:menu.group heading="ACCOUNT">
            <flux:menu.item :href="route('profile.edit')" icon="user-circle" wire:navigate>
                My Profile
            </flux:menu.item>

            <flux:menu.item :href="route('profile.edit')" icon="cog-6-tooth" wire:navigate>
                Account Settings
            </flux:menu.item>

            <flux:menu.item :href="route('dashboard')" icon="chart-bar-square" wire:navigate>
                Carbon Stats Summary
            </flux:menu.item>
        </flux:menu.group>

        <flux:menu.group heading="APPEARANCE">
            <flux:menu.submenu heading="Theme" icon="moon" keep-open>
                <flux:menu.item as="button" type="button" keep-open x-on:click.stop="setTheme('light')">
                    Light
                    <x-slot:suffix>
                        <span x-show="!$flux.dark" class="text-xs text-emerald-600">Active</span>
                    </x-slot:suffix>
                </flux:menu.item>

                <flux:menu.item as="button" type="button" keep-open x-on:click.stop="setTheme('dark')">
                    Dark
                    <x-slot:suffix>
                        <span x-show="$flux.dark" class="text-xs text-emerald-600">Active</span>
                    </x-slot:suffix>
                </flux:menu.item>
            </flux:menu.submenu>

            <flux:menu.item as="button" type="button" icon="squares-2x2" x-on:click="toggleCompactView()">
                Compact View
                <x-slot:suffix>
                    <span class="text-xs text-zinc-400" x-text="compactView ? 'On' : 'Off'"></span>
                </x-slot:suffix>
            </flux:menu.item>
        </flux:menu.group>

        <flux:menu.group heading="ACTIVITY">
            <flux:menu.item :href="route('carbon.history')" icon="clipboard-document-list" wire:navigate>
                My Log History
            </flux:menu.item>

            <flux:menu.item :href="route('eco.chat')" icon="chat-bubble-left-right" wire:navigate>
                Eco Chat
            </flux:menu.item>

            <flux:menu.item :href="route('achievements')" icon="check-badge" wire:navigate>
                Achievements
            </flux:menu.item>

            <flux:menu.item href="#" icon="calendar-days">
                Weekly Reports
            </flux:menu.item>
        </flux:menu.group>

        <flux:menu.group heading="SUPPORT">
            <flux:menu.item href="#" icon="lifebuoy">
                Help &amp; Tutorial
            </flux:menu.item>

            <flux:menu.item href="#" icon="chat-bubble-left-ellipsis">
                Feedback
            </flux:menu.item>

            <flux:menu.item href="#" icon="information-circle">
                About Eco Track
            </flux:menu.item>
        </flux:menu.group>

        <div class="-mx-[.3125rem] px-[.3125rem]">
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
                    Log Out
                </flux:menu.item>
            </form>
        </div>
    </flux:menu>
</flux:dropdown>
