<x-layouts::app :title="__('Eco Chat')">
    <div x-data="{ guideOpen: false }" class="space-y-8">
        <section class="flex items-start justify-between gap-4">
            <div class="min-w-0"></div>

            <div class="shrink-0">
                <button
                    type="button"
                    @click="guideOpen = true"
                    class="inline-flex items-center gap-2 rounded-xl border border-zinc-200 bg-white px-4 py-2.5 text-sm font-semibold text-zinc-700 shadow-sm transition hover:border-emerald-200 hover:bg-emerald-50 hover:text-zinc-900"
                >
                    <svg class="h-4 w-4 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 3a9 9 0 1 0 9 9"/>
                        <path d="M12 7v5l3 3"/>
                    </svg>
                    Chat Guide
                </button>
            </div>
        </section>

        <livewire:eco-chat />

        <template x-teleport="body">
            <div
                x-cloak
                x-show="guideOpen"
                x-transition.opacity
                class="fixed inset-0 z-[1000]"
            >
                <div class="absolute inset-0 bg-slate-950/30 backdrop-blur-sm" @click="guideOpen = false"></div>

                <aside
                    x-show="guideOpen"
                    x-transition:enter="transform transition ease-out duration-200"
                    x-transition:enter-start="translate-x-full"
                    x-transition:enter-end="translate-x-0"
                    x-transition:leave="transform transition ease-in duration-150"
                    x-transition:leave-start="translate-x-0"
                    x-transition:leave-end="translate-x-full"
                    class="absolute right-0 top-0 flex h-full w-full max-w-md flex-col overflow-y-auto border-l border-zinc-200 bg-white shadow-2xl"
                >
                    <div class="flex items-center justify-between border-b border-zinc-200 px-5 py-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-emerald-700">Flyout</p>
                            <h2 class="mt-1 text-lg font-bold text-zinc-900">Eco Chat Guide</h2>
                        </div>

                        <button
                            type="button"
                            @click="guideOpen = false"
                            class="rounded-lg p-2 text-zinc-400 transition hover:bg-zinc-100 hover:text-zinc-700"
                        >
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M18 6 6 18"/>
                                <path d="m6 6 12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-4 p-5">
                        <article class="rounded-[1rem] border border-emerald-100 bg-white p-5 shadow-sm">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-emerald-700">Understand</p>
                            <p class="mt-3 text-sm leading-6 text-zinc-600">Get clear explanations of your data, trends, predictions, and core Eco Track features.</p>
                        </article>

                        <article class="rounded-[1rem] border border-emerald-100 bg-white p-5 shadow-sm">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-emerald-700">Improve</p>
                            <p class="mt-3 text-sm leading-6 text-zinc-600">Turn recorded habits into practical next-step recommendations you can act on immediately.</p>
                        </article>

                        <article class="rounded-[1rem] border border-emerald-100 bg-white p-5 shadow-sm">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-emerald-700">What It Can Do</p>
                            <ul class="mt-3 space-y-3 text-sm leading-6 text-zinc-600">
                                <li>Summarize your recent carbon activity from saved logs.</li>
                                <li>Explain Eco Track calculations, pages, and prediction behavior.</li>
                                <li>Suggest next actions based on your transport, diet, and gadget patterns.</li>
                            </ul>
                        </article>

                        <article class="rounded-[1rem] border border-zinc-200 bg-zinc-50 p-5 shadow-sm">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-zinc-500">Scope</p>
                            <p class="mt-3 text-sm leading-6 text-zinc-600">
                                This chatbot is intentionally scoped to Eco Track. It answers questions about the app, your sustainability activity, and the data recorded in your account.
                            </p>
                        </article>
                    </div>
                </aside>
            </div>
        </template>
    </div>
</x-layouts::app>
