<x-layouts::app :title="__('AI Predictions')">
    <div class="space-y-8">
        <section class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div class="min-w-0">
                <p class="text-sm font-semibold uppercase tracking-[0.28em] text-emerald-700">Insights</p>
                <h1 class="mt-1 text-2xl font-black text-zinc-900 dark:text-zinc-100 sm:text-3xl">AI Predictions</h1>
                <p class="mt-1 max-w-2xl text-sm text-zinc-600 dark:text-zinc-400">
                    Explore forecasted emissions, trend signals, and practical recommendations generated from your activity patterns.
                </p>
            </div>

            <div class="grid gap-3 sm:grid-cols-2">
                <article class="border border-emerald-100 bg-white px-4 py-3 dark:border-zinc-800 dark:bg-zinc-950" style="border-radius: 0.35rem;">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-emerald-700">Predict</p>
                    <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">See where your footprint may head next based on recent logs.</p>
                </article>

                <article class="border border-emerald-100 bg-white px-4 py-3 dark:border-zinc-800 dark:bg-zinc-950" style="border-radius: 0.35rem;">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-emerald-700">Act</p>
                    <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">Turn forecasts into smaller daily changes with guided recommendations.</p>
                </article>
            </div>
        </section>

        <section class="grid gap-4 lg:grid-cols-[minmax(0,1.65fr)_minmax(280px,0.95fr)]">
            <div class="min-w-0">
                <livewire:ai-predictions />
            </div>

            <aside class="space-y-4">
                <article class="border border-emerald-100 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-950" style="border-radius: 0.35rem;">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-emerald-700">How To Use</p>
                    <h2 class="mt-2 text-lg font-bold text-zinc-900 dark:text-zinc-100">Make the forecast useful</h2>
                    <ul class="mt-3 space-y-2 text-sm text-zinc-600 dark:text-zinc-400">
                        <li>Log activities consistently so the prediction has better context.</li>
                        <li>Compare the trend direction with your latest habits and weekly patterns.</li>
                        <li>Start with one easy recommendation before moving to medium-effort changes.</li>
                    </ul>
                </article>

                <article class="border border-zinc-200 bg-zinc-50 p-5 dark:border-zinc-800 dark:bg-zinc-900" style="border-radius: 0.35rem;">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-zinc-500 dark:text-zinc-400">Page Note</p>
                    <p class="mt-3 text-sm leading-6 text-zinc-600 dark:text-zinc-400">
                        This page reuses the existing prediction component and current data flow, so it stays aligned with the dashboard experience without changing prediction logic or storage behavior.
                    </p>
                </article>
            </aside>
        </section>
    </div>
</x-layouts::app>
