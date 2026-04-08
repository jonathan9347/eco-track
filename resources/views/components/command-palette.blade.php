@php
    $commands = [];

    if (Route::has('dashboard')) {
        $commands[] = [
            'title' => 'Dashboard',
            'description' => 'View your sustainability overview',
            'group' => 'Navigation',
            'href' => route('dashboard'),
            'keywords' => ['home', 'overview', 'stats'],
        ];
    }

    if (Route::has('carbon.history')) {
        $commands[] = [
            'title' => 'Carbon Log',
            'description' => 'Open your carbon history page',
            'group' => 'Navigation',
            'href' => route('carbon.history'),
            'keywords' => ['carbon', 'history', 'logs', 'entries'],
        ];
    }

    if (Route::has('ai.predictions')) {
        $commands[] = [
            'title' => 'AI Predictions',
            'description' => 'Review your forecasted emissions and suggestions',
            'group' => 'Navigation',
            'href' => route('ai.predictions'),
            'keywords' => ['ai', 'predictions', 'forecast', 'recommendations'],
        ];
    }

    if (Route::has('eco.chat')) {
        $commands[] = [
            'title' => 'Eco Chat',
            'description' => 'Talk to the Eco Track assistant about your activity and app data',
            'group' => 'Navigation',
            'href' => route('eco.chat'),
            'keywords' => ['chat', 'assistant', 'gemini', 'eco'],
        ];
    }

    if (Route::has('eco.tips')) {
        $commands[] = [
            'title' => 'Eco Tips',
            'description' => 'Read climate basics, SDG 13 context, and simple action tips',
            'group' => 'Navigation',
            'href' => route('eco.tips'),
            'keywords' => ['tips', 'climate', 'sdg 13', 'action', 'guide'],
        ];
    }

    if (Route::has('reports')) {
        $commands[] = [
            'title' => 'Reports',
            'description' => 'Review and export your Eco Track reporting data',
            'group' => 'Navigation',
            'href' => route('reports'),
            'keywords' => ['reports', 'export', 'csv', 'json', 'summary'],
        ];
    }

    if (Route::has('leaderboard')) {
        $commands[] = [
            'title' => 'Classroom Leaderboard',
            'description' => 'See the latest rankings',
            'group' => 'Navigation',
            'href' => route('leaderboard'),
            'keywords' => ['leaderboard', 'ranking', 'classroom'],
        ];
    }

    if (Route::has('achievements')) {
        $commands[] = [
            'title' => 'Achievements',
            'description' => 'Check badges and weekly challenges',
            'group' => 'Navigation',
            'href' => route('achievements'),
            'keywords' => ['badges', 'challenge', 'rewards'],
        ];
    }

    if (Route::has('profile.edit')) {
        $commands[] = [
            'title' => 'Settings',
            'description' => 'Manage your profile and account settings',
            'group' => 'Account',
            'href' => route('profile.edit'),
            'keywords' => ['profile', 'preferences', 'account'],
        ];
    }

    if (auth()->user()?->is_admin && Route::has('admin')) {
        $commands[] = [
            'title' => 'Admin Panel',
            'description' => 'Manage users and application settings',
            'group' => 'Admin',
            'href' => route('admin'),
            'keywords' => ['admin', 'manage', 'users'],
        ];
    }

    $groupOrder = ['Navigation', 'Account', 'Admin'];
@endphp

<div
    x-data="{
        open: false,
        query: '',
        commands: @js($commands),
        groupOrder: @js($groupOrder),
        shouldHandleShortcut(event) {
            const target = event.target;

            if (! target) {
                return true;
            }

            if (this.open && this.$root.contains(target)) {
                return true;
            }

            return ! target.closest('input, textarea, select, [contenteditable], [contenteditable=true]');
        },
        openPalette() {
            if (this.open) {
                this.focusSearch();
                return;
            }

            this.open = true;
            this.focusSearch();
        },
        closePalette() {
            if (! this.open) {
                return;
            }

            this.open = false;
            this.resetSearch();
        },
        resetSearch() {
            this.query = '';
        },
        focusSearch() {
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    this.$refs.searchInput?.focus();
                    this.$refs.searchInput?.select();
                });
            });
        },
        execute(command) {
            this.closePalette();

            if (window.Livewire?.navigate) {
                window.Livewire.navigate(command.href);
                return;
            }

            window.location.assign(command.href);
        },
        get filteredCommands() {
            const search = this.query.trim().toLowerCase();

            if (! search) {
                return this.commands;
            }

            return this.commands.filter((command) => {
                return [
                    command.title,
                    command.description,
                    ...(command.keywords || []),
                ].some((value) => value.toLowerCase().includes(search));
            });
        },
        commandsForGroup(group) {
            return this.filteredCommands.filter((command) => command.group === group);
        },
        hasGroup(group) {
            return this.commandsForGroup(group).length > 0;
        },
    }"
    x-on:keydown.meta.k.document="if (shouldHandleShortcut($event)) { $event.preventDefault(); openPalette(); }"
    x-on:keydown.ctrl.k.document="if (shouldHandleShortcut($event)) { $event.preventDefault(); openPalette(); }"
    x-on:keydown.escape.window="if (open) { $event.preventDefault(); closePalette(); }"
    x-on:livewire:navigated.window="closePalette()"
    class="relative"
>
    <button
        type="button"
        @click="openPalette()"
        class="flex items-center gap-2 rounded-md border border-zinc-300 bg-zinc-100 px-3 py-1.5 text-sm text-zinc-500 transition-colors hover:bg-zinc-200"
        style="min-width: 280px;"
    >
        <svg class="mr-2 h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"/>
            <path d="m21 21-4.3-4.3"/>
        </svg>
        <span class="flex-1 text-left">Type a command or search...</span>
        <kbd class="pointer-events-none inline-flex h-5 select-none items-center rounded border border-zinc-200 bg-zinc-50 px-1.5 font-mono text-[10px] font-medium text-zinc-500">
            <span>Cmd</span>
            <span>+</span>
            <span>K</span>
        </kbd>
    </button>

    <template x-teleport="body">
        <div
            x-cloak
            x-show="open"
            x-transition.opacity
            class="fixed inset-0 z-[1000] flex items-start justify-center px-4 pt-24 sm:px-6"
        >
            <div
                class="absolute inset-0 bg-slate-950/25 backdrop-blur-sm"
                @click="closePalette()"
            ></div>

            <div
                x-show="open"
                x-transition:enter="ease-out duration-150"
                x-transition:enter-start="opacity-0 translate-y-2 scale-[0.98]"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="ease-in duration-100"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 translate-y-2 scale-[0.98]"
                @click.stop
                class="relative z-[1001] w-full max-w-2xl overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-2xl"
            >
                <div class="flex items-center border-b border-zinc-100 px-4 py-3">
                    <svg class="mr-2 h-5 w-5 shrink-0 text-zinc-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.3-4.3"/>
                    </svg>

                    <input
                        x-ref="searchInput"
                        x-model="query"
                        type="text"
                        placeholder="Type a command or search..."
                        autocomplete="off"
                        class="flex-1 border-0 bg-transparent text-sm text-zinc-900 placeholder:text-zinc-400 focus:outline-none focus:ring-0"
                    />

                    <button
                        type="button"
                        @click="closePalette()"
                        class="ml-3 rounded-md px-2 py-1 text-xs font-medium text-zinc-500 transition hover:bg-zinc-100 hover:text-zinc-700"
                    >
                        Esc
                    </button>
                </div>

                <div class="max-h-[420px] overflow-y-auto p-2">
                    <template x-if="filteredCommands.length === 0">
                        <div class="px-3 py-8 text-center text-sm text-zinc-500">
                            No matching commands.
                        </div>
                    </template>

                    <template x-for="group in groupOrder" :key="group">
                        <div x-show="hasGroup(group)" class="py-1">
                            <p class="px-3 pb-2 pt-1 text-xs font-medium uppercase tracking-[0.18em] text-zinc-500" x-text="group"></p>

                            <div class="space-y-1">
                                <template x-for="command in commandsForGroup(group)" :key="command.href">
                                    <button
                                        type="button"
                                        @click="execute(command)"
                                        class="flex w-full items-start gap-3 rounded-xl px-3 py-3 text-left transition hover:bg-zinc-100"
                                    >
                                        <div class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-zinc-100 text-zinc-500">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M5 12h14"/>
                                                <path d="m12 5 7 7-7 7"/>
                                            </svg>
                                        </div>

                                        <div class="min-w-0 flex-1">
                                            <div class="text-sm font-medium text-zinc-900" x-text="command.title"></div>
                                            <div class="mt-1 text-sm text-zinc-500" x-text="command.description"></div>
                                        </div>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </template>
</div>

<style>
    [x-cloak] {
        display: none !important;
    }
</style>
