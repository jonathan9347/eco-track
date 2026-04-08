<x-layouts::app :title="__('Dashboard')">
    <div class="space-y-8">
        <section class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div class="min-w-0">
                <p class="text-sm font-semibold uppercase tracking-[0.28em] text-emerald-700">Dashboard</p>
                <h1 class="mt-1 text-2xl font-black text-zinc-900 dark:text-zinc-100 sm:text-3xl">Welcome back, {{ auth()->user()->name }}.</h1>
                <p class="mt-1 max-w-2xl text-sm text-zinc-600 dark:text-zinc-400">
                    Track your carbon footprint, compare with classmates, and make greener choices every day.
                </p>
            </div>

            <div class="sm:flex-shrink-0" x-data="{ carbonModalOpen: false }">
                <button
                    type="button"
                    @click="carbonModalOpen = true"
                    class="inline-flex items-center justify-center text-xs font-semibold transition focus:outline-none"
                    style="border-radius: 0.35rem; background: #059669; color: #ffffff; padding: 0.5rem 0.85rem; box-shadow: 0 10px 24px rgba(5, 150, 105, 0.18); letter-spacing: 0.02em;"
                >
                    Carbon Log
                </button>

                <!-- Carbon Calculator Modal -->
                <div
                    x-show="carbonModalOpen"
                    x-cloak
                    class="fixed inset-0 flex items-center justify-center z-[9999]"
                    @keydown.escape="carbonModalOpen = false"
                >
                    <!-- Overlay -->
                    <div
                        class="fixed inset-0 bg-black/50 backdrop-blur-sm"
                        @click="carbonModalOpen = false"
                    ></div>
                    
                    <!-- Modal Content -->
                    <div
                        class="relative mx-4 w-full max-w-xl rounded-lg bg-white shadow-2xl dark:bg-zinc-950 dark:ring-1 dark:ring-zinc-800"
                        style="animation: dialog-in 200ms ease-out; z-index: 10000;"
                        @click.stop
                    >
                        <div class="p-6">
                            <livewire:carbon-calculator />
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="grid gap-4 md:grid-cols-3" style="margin-top: 0.5rem !important;">
            <article class="border border-emerald-100 bg-white p-6 dark:border-emerald-900/40 dark:bg-zinc-950" style="border-radius: 0.35rem;">
                <div class="flex items-center gap-2">
                    <svg
                        style="width: 1.5rem; height: 1.5rem; color: #3b82f6;"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        aria-hidden="true"
                    >
                        <path d="M8 2v4" />
                        <path d="M16 2v4" />
                        <path d="M21 6v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2Z" />
                        <path d="M3 10h18" />
                    </svg>
                    <p class="text-sm font-medium text-slate-500 dark:text-zinc-400">This Week</p>
                </div>
                <p class="mt-3 text-3xl font-semibold tracking-tight text-slate-900 dark:text-zinc-100">18.4 kg</p>
                <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-zinc-400">Estimated carbon footprint logged so far.</p>
            </article>

            <article class="border border-emerald-100 bg-white p-6 dark:border-emerald-900/40 dark:bg-zinc-950" style="border-radius: 0.35rem;">
                <div class="flex items-center gap-2">
                    <svg
                        style="width: 1.5rem; height: 1.5rem; color: #16a34a;"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        aria-hidden="true"
                    >
                        <path d="M12 13V2l8 4-8 4" />
                        <path d="M20.55 10.23A9 9 0 1 1 8 4.94" />
                        <path d="M8 12a4 4 0 1 0 4-4" />
                    </svg>
                    <p class="text-sm font-medium text-slate-500 dark:text-zinc-400">Goal Progress</p>
                </div>
                <p class="mt-3 text-3xl font-semibold tracking-tight text-slate-900 dark:text-zinc-100">72%</p>
                <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-zinc-400">You are close to your weekly low-emission target.</p>
            </article>

            <article class="border border-emerald-100 bg-white p-6 dark:border-emerald-900/40 dark:bg-zinc-950" style="border-radius: 0.35rem;">
                <div class="flex items-center gap-2">
                    <svg
                        style="width: 1.5rem; height: 1.5rem; color: #9333ea;"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        aria-hidden="true"
                    >
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                        <path d="M12 22V2" />
                    </svg>
                    <p class="text-sm font-medium text-slate-500 dark:text-zinc-400">Class Standing</p>
                </div>
                <p class="mt-3 text-3xl font-semibold tracking-tight text-slate-900 dark:text-zinc-100">#2</p>
                <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-zinc-400">Placeholder ranking among students in your classroom.</p>
            </article>
        </section>

        <section class="px-6">
            <livewire:dashboard-stats />
        </section>

        <section class="px-6">
            <livewire:ai-predictions />
        </section>
    </div>
</x-layouts::app>
