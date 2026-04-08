<section class="w-full px-2 py-2">
    <style>
        @keyframes badge-pop {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 rgba(16, 185, 129, 0);
            }

            60% {
                transform: scale(1.03);
                box-shadow: 0 20px 40px rgba(16, 185, 129, 0.18);
            }

            100% {
                transform: scale(1);
                box-shadow: 0 18px 36px rgba(16, 185, 129, 0.12);
            }
        }

        .badge-unlock {
            animation: badge-pop 700ms ease-out both;
        }
    </style>

    <div class="space-y-8">
        <div class="overflow-hidden" style="border-radius: 0.35rem !important;">
            <div class="pb-6">
                <p class="text-sm font-semibold uppercase tracking-[0.28em] text-emerald-700">Badges</p>
                <h2 class="mt-1 text-2xl font-black text-zinc-900 dark:text-zinc-100 sm:text-3xl">Unlock recognition for greener habits.</h2>
                <p class="mt-1 max-w-2xl text-sm text-zinc-600 dark:text-zinc-400">
                    Earn badges by walking more, choosing vegan meals, saving gadget energy, cutting emissions, and staying consistent.
                </p>
            </div>

            <div class="grid gap-6 px-6 py-8 sm:px-8 lg:grid-cols-2 xl:grid-cols-3">
                @foreach ($badges as $badge)
                    <article
                        class="{{ $badge['earned'] ? 'badge-unlock border-emerald-300 bg-emerald-50 shadow-lg shadow-emerald-100/70 dark:border-emerald-900/40 dark:bg-emerald-950/20 dark:shadow-none' : 'border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-950' }} border p-6 transition"
                        style="border-radius: 0.35rem !important;"
                    >
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.24em] {{ $badge['earned'] ? 'text-emerald-700' : 'text-zinc-500' }}">
                                    {{ $badge['earned'] ? 'Earned' : 'In Progress' }}
                                </p>
                                <h3 class="mt-2 text-2xl font-black text-zinc-900 dark:text-zinc-100">{{ $badge['title'] }}</h3>
                                <p class="mt-2 text-sm leading-6 text-zinc-600 dark:text-zinc-400">{{ $badge['description'] }}</p>
                            </div>

                            <div class="flex h-14 w-14 items-center justify-center {{ $badge['earned'] ? 'bg-emerald-600 text-white dark:bg-emerald-500' : 'bg-zinc-100 text-zinc-500 dark:bg-zinc-800 dark:text-zinc-300' }} text-2xl" style="border-radius: 0.35rem !important;">
                                {{ $badge['earned'] ? '★' : '☆' }}
                            </div>
                        </div>

                        <div class="mt-6">
                            <div class="mb-2 flex items-center justify-between text-sm">
                                <span class="font-medium text-zinc-700 dark:text-zinc-300">{{ $badge['progress'] }} / {{ $badge['target'] }} {{ $badge['suffix'] }}</span>
                                <span class="font-semibold {{ $badge['earned'] ? 'text-emerald-700' : 'text-zinc-500' }}">{{ $badge['percentage'] }}%</span>
                            </div>
                            <div class="h-3 bg-zinc-100 dark:bg-zinc-800" style="border-radius: 0.35rem !important;">
                                <div
                                    class="h-3 {{ $badge['earned'] ? 'bg-emerald-500' : 'bg-lime-500' }}"
                                    style="border-radius: 0.35rem !important; width: {{ $badge['percentage'] }}%;"
                                ></div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>

        <div class="grid gap-8 xl:grid-cols-[0.98fr_1.02fr]">
            <section class="overflow-hidden border border-zinc-200 dark:border-zinc-800 dark:bg-zinc-950" style="border-radius: 0.35rem !important;">
                <div class="px-8 py-6 sm:px-10">
                    <p class="text-sm font-semibold uppercase tracking-[0.28em] text-emerald-700">Challenges</p>
                    <h2 class="mt-1 text-2xl font-black text-zinc-900 dark:text-zinc-100 sm:text-3xl">{{ $activeChallenge['title'] ?? 'Weekly Challenge' }}</h2>
                    <p class="mt-1 max-w-2xl text-sm text-zinc-600 dark:text-zinc-400">
                        {{ $activeChallenge['description'] ?? 'Take on a new challenge each week and earn points for your classroom.' }}
                    </p>
                </div>

                <div class="space-y-6 px-10 py-10 sm:px-12">
                    @if ($challengeMessage)
                        <div class="border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700" style="border-radius: 0.35rem !important;">
                            {{ $challengeMessage }}
                        </div>
                    @endif

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="bg-emerald-50 px-5 py-4 ring-1 ring-emerald-100 dark:bg-emerald-950/20 dark:ring-emerald-900/40" style="border-radius: 0.35rem !important;">
                            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-emerald-700">Progress</p>
                            <p class="mt-2 text-3xl font-black text-zinc-900 dark:text-zinc-100">
                                {{ $activeChallenge['progress'] ?? 0 }} / {{ $activeChallenge['target'] ?? 0 }}
                            </p>
                        </div>
                            <div class="bg-emerald-50 px-5 py-4 ring-1 ring-emerald-100 dark:bg-emerald-950/20 dark:ring-emerald-900/40" style="border-radius: 0.35rem !important;">
                            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-emerald-700">Points</p>
                            <p class="mt-2 text-3xl font-black text-zinc-900 dark:text-zinc-100">{{ $activeChallenge['points'] ?? 100 }}</p>
                            <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">Your total: {{ $userPoints }} points</p>
                        </div>
                    </div>

                    <div>
                        <div class="mb-2 flex items-center justify-between text-sm">
                            <span class="font-medium text-zinc-700 dark:text-zinc-300">Challenge completion</span>
                            <span class="font-semibold text-emerald-700">{{ $activeChallenge['percentage'] ?? 0 }}%</span>
                        </div>
                        <div class="h-3 bg-zinc-100 dark:bg-zinc-800" style="border-radius: 0.35rem !important;">
                            <div
                                class="h-3 bg-emerald-500 transition-all duration-700"
                                style="border-radius: 0.35rem !important; width: {{ $activeChallenge['percentage'] ?? 0 }}%;"
                            ></div>
                        </div>
                    </div>

                    <button
                        wire:click="completeChallenge"
                        class="inline-flex items-center justify-center px-6 py-3 text-sm font-semibold transition {{ ($activeChallenge['completed'] ?? false) ? 'cursor-not-allowed bg-zinc-200 text-zinc-500' : 'bg-emerald-600 text-white hover:bg-emerald-700' }}"
                        style="border-radius: 0.35rem !important;"
                        @disabled($activeChallenge['completed'] ?? false)
                    >
                        {{ ($activeChallenge['completed'] ?? false) ? 'Completed' : 'Complete' }}
                    </button>
                </div>
            </section>

            <section class="overflow-hidden border border-zinc-200 dark:border-zinc-800 dark:bg-zinc-950" style="border-radius: 0.35rem !important;">
                <div class="px-8 py-6 sm:px-10">
                    <p class="text-sm font-semibold uppercase tracking-[0.28em] text-emerald-700">Challenge Points Leaderboard</p>
                    <h2 class="mt-1 text-2xl font-black text-zinc-900 dark:text-zinc-100 sm:text-3xl">Top students by challenge points</h2>
                </div>

                <div class="space-y-4 px-10 py-10 sm:px-12">
                    @forelse ($challengeLeaderboard as $entry)
                        <article class="border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 dark:shadow-none" style="border-radius: 0.35rem !important;">
                            <div class="flex items-center justify-between gap-4">
                                <div class="flex items-center gap-4">
                                    <div class="flex h-12 w-12 items-center justify-center bg-emerald-50 text-xl ring-1 ring-emerald-100 dark:bg-emerald-950/30 dark:ring-emerald-900/40" style="border-radius: 0.35rem !important;">
                                        @if ($entry['rank'] === 1)
                                            🥇
                                        @elseif ($entry['rank'] === 2)
                                            🥈
                                        @elseif ($entry['rank'] === 3)
                                            🥉
                                        @else
                                            {{ $entry['rank'] }}
                                        @endif
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-100">{{ $entry['user_name'] }}</h3>
                                        <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $entry['classroom'] }}</p>
                                    </div>
                                </div>

                                <div class="text-right">
                                    <p class="text-2xl font-black text-emerald-700">{{ $entry['points'] }}</p>
                                    <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $entry['completed_challenges'] }} completed</p>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="border border-dashed border-zinc-200 bg-zinc-50 px-6 py-10 text-center text-sm text-zinc-500 dark:border-zinc-800 dark:bg-zinc-900 dark:text-zinc-400" style="border-radius: 0.35rem !important;">
                            No challenge completions yet. Complete the weekly challenge to start the leaderboard.
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</section>
