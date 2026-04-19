<x-layouts::app :title="__('AI Predictions')">
    <section class="space-y-6 rounded-[0.35rem] border border-emerald-200 bg-emerald-50/60 p-4 dark:border-emerald-900/40 dark:bg-emerald-950/20 sm:p-6">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.28em] text-emerald-700">AI Predictions</p>
            <h1 class="mt-1 text-3xl font-black text-zinc-900 dark:text-zinc-100 sm:text-4xl">See where your footprint may head next.</h1>
            <p class="mt-2 max-w-2xl text-sm text-zinc-600 dark:text-zinc-400">
                Review your forecasted emissions and practical suggestions based on your recent carbon logs.
            </p>
        </div>

        <livewire:ai-predictions />
    </section>
</x-layouts::app>
