<section class="eco-page-palette w-full px-2 py-2">
    <style>
        @keyframes badge-pop {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 rgba(16, 185, 129, 0);
            }

            60% {
                transform: scale(1.03);
            }

            100% {
                transform: scale(1);
            }
        }

        .badge-unlock {
            animation: badge-pop 700ms ease-out both;
        }

        .achievement-flip-card {
            perspective: 1200px;
        }

        .achievement-flip-card summary {
            list-style: none;
        }

        .achievement-flip-card summary::-webkit-details-marker {
            display: none;
        }

        .achievement-flip-card__inner {
            position: relative;
            height: 100%;
            min-height: 22rem;
            transform-style: preserve-3d;
            transition: transform 700ms cubic-bezier(0.22, 1, 0.36, 1);
        }

        .achievement-flip-card[open] .achievement-flip-card__inner {
            transform: rotateY(180deg);
        }

        .achievement-flip-card__face {
            position: absolute;
            inset: 0;
            display: flex;
            flex-direction: column;
            backface-visibility: hidden;
            -webkit-backface-visibility: hidden;
        }

        .achievement-flip-card__back {
            transform: rotateY(180deg);
        }
    </style>

    <div class="space-y-8">
        <div class="overflow-hidden" style="border-radius: 0.35rem !important;">
            <div class="pb-6">
                <p class="text-sm font-semibold uppercase tracking-[0.28em] text-emerald-700">Badges</p>
                <h2 class="mt-1 text-2xl font-black text-zinc-900 dark:text-zinc-100 sm:text-3xl">Unlock recognition for greener habits.</h2>
                <p class="mt-1 max-w-2xl text-sm text-zinc-600 dark:text-zinc-400">
                    Earn badges from the activity in your carbon logs by walking more, keeping gadget use low, logging consistently, and building a stronger tracking streak.
                </p>
            </div>

            <div class="grid gap-6 px-6 py-8 sm:px-8 lg:grid-cols-2 xl:grid-cols-3">
                @foreach ($badges as $badge)
                    <details class="achievement-flip-card {{ $badge['earned'] ? 'badge-unlock' : '' }}">
                        <summary class="cursor-pointer focus:outline-none">
                            <div class="achievement-flip-card__inner">
                            <div
                                class="achievement-flip-card__face eco-page-card {{ $badge['front_class'] ?? 'eco-page-card--emerald' }} border border-emerald-200 p-6 dark:border-emerald-900/40"
                                style="border-radius: 0.35rem !important;"
                            >
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-[0.24em] {{ $badge['earned'] ? 'text-emerald-700 dark:text-emerald-300' : 'text-emerald-600/80 dark:text-emerald-200/80' }}">
                                            {{ $badge['earned'] ? 'Earned' : 'In Progress' }}
                                        </p>
                                        <h3 class="mt-2 text-2xl font-black text-emerald-950 dark:text-zinc-100">{{ $badge['title'] }}</h3>
                                        <p class="mt-2 text-sm leading-6 text-emerald-900/75 dark:text-emerald-100/80">{{ $badge['description'] }}</p>
                                    </div>

                                    <div class="flex h-14 w-14 items-center justify-center bg-white/75 text-emerald-700 ring-1 ring-emerald-200 backdrop-blur-sm dark:bg-emerald-900/45 dark:text-emerald-100 dark:ring-emerald-700/50" style="border-radius: 0.35rem !important;">
                                        <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="{{ $badge['icon_path'] }}"></path>
                                        </svg>
                                    </div>
                                </div>

                                <div class="mt-auto pt-8">
                                    <div class="mb-2 flex items-center justify-between text-sm">
                                        <span class="font-medium text-emerald-900/80 dark:text-emerald-100/80">{{ $badge['progress'] }} / {{ $badge['target'] }} {{ $badge['suffix'] }}</span>
                                        <span class="font-semibold {{ $badge['earned'] ? 'text-emerald-700 dark:text-emerald-300' : 'text-emerald-700/80 dark:text-emerald-200/80' }}">{{ $badge['percentage'] }}%</span>
                                    </div>
                                    <div class="h-3 bg-white/70 ring-1 ring-emerald-100 dark:bg-emerald-950/50 dark:ring-emerald-900/40" style="border-radius: 0.35rem !important;">
                                        <div
                                            class="h-3 {{ $badge['earned'] ? 'bg-emerald-500' : 'bg-emerald-400' }}"
                                            style="border-radius: 0.35rem !important; width: {{ $badge['percentage'] }}%;"
                                        ></div>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="achievement-flip-card__face achievement-flip-card__back eco-page-card eco-page-card--soft-blue border border-sky-200 p-6 dark:border-sky-900/40"
                                style="border-radius: 0.35rem !important;"
                            >
                                <div class="flex h-full flex-col justify-between">
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-emerald-700 dark:text-emerald-300">How To Earn</p>
                                        <h3 class="mt-2 text-2xl font-black text-emerald-950 dark:text-zinc-100">{{ $badge['title'] }}</h3>
                                        <p class="mt-4 text-sm leading-7 text-emerald-950/80 dark:text-emerald-100/85">{{ $badge['instruction'] }}</p>
                                    </div>

                                    <div class="mt-8 rounded-[0.35rem] bg-white/65 px-4 py-3 text-sm text-emerald-900/80 ring-1 ring-emerald-100 dark:bg-emerald-950/35 dark:text-emerald-100/80 dark:ring-emerald-900/40">
                                        Target: {{ $badge['target'] }} {{ $badge['suffix'] }}
                                    </div>
                                </div>
                            </div>
                            </div>
                        </summary>
                    </details>
                @endforeach
            </div>
        </div>

    </div>
</section>
