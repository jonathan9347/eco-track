<x-layouts::app :title="__('Eco Tips')">
    <div class="space-y-8">
        <section class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div class="min-w-0">
                <p class="text-sm font-semibold uppercase tracking-[0.28em] text-emerald-700">Resources</p>
                <h1 class="mt-1 text-2xl font-black text-zinc-900 dark:text-zinc-100 sm:text-3xl">Eco Tips</h1>
                <p class="mt-1 max-w-3xl text-sm text-zinc-600 dark:text-zinc-400">
                    A simple reference page for understanding climate change, why it matters, and what actions students can take through everyday choices tracked in Eco Track.
                </p>
            </div>

            <div class="rounded-xl border border-emerald-100 bg-white px-4 py-3 shadow-sm dark:border-zinc-800 dark:bg-zinc-950">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-emerald-700">Purpose</p>
                <p class="mt-2 max-w-xs text-sm text-zinc-600 dark:text-zinc-400">Learn the basics, connect them to your habits, and use Eco Track as a small action tool for climate awareness.</p>
            </div>
        </section>

        <section class="grid gap-4 lg:grid-cols-[minmax(0,1.45fr)_minmax(280px,0.9fr)]">
            <article class="rounded-[1.2rem] border border-zinc-200 bg-white p-6 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-emerald-700">Climate Basics</p>
                <h2 class="mt-2 text-xl font-bold text-zinc-900">What climate change means</h2>
                <div class="mt-4 space-y-4 text-sm leading-7 text-zinc-600">
                    <p>
                        Climate change refers to long-term shifts in Earth&rsquo;s average temperature and weather patterns. A major driver today is the increase of greenhouse gases from human activities such as transport, energy use, food systems, and industrial production.
                    </p>
                    <p>
                        These gases trap heat in the atmosphere. As more heat is trapped, the planet warms, which can change rainfall patterns, intensify heat waves, increase flooding risk, damage ecosystems, and place pressure on food, water, and health systems.
                    </p>
                    <p>
                        Climate change is not caused by one single behavior alone. It is shaped by many linked factors including how people travel, what they eat, how much electricity they use, how products are made, and how communities manage waste and natural resources.
                    </p>
                </div>
            </article>

            <aside class="space-y-4">
                <div class="flex min-h-[220px] items-center justify-center rounded-[1.2rem] border border-dashed border-zinc-300 bg-zinc-50 p-6 text-center text-sm text-zinc-400">
                    Placeholder climate image
                </div>

                <article class="rounded-[1.2rem] border border-zinc-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-zinc-500">Key Effects</p>
                    <ul class="mt-3 space-y-3 text-sm leading-6 text-zinc-600">
                        <li>Hotter days and longer heat periods</li>
                        <li>Stronger storms, heavier rain, and flood risk</li>
                        <li>Pressure on food systems, water supply, and health</li>
                        <li>Damage to biodiversity and local ecosystems</li>
                    </ul>
                </article>
            </aside>
        </section>

        <section class="grid gap-4 lg:grid-cols-3">
            <article class="rounded-[1.1rem] border border-zinc-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-emerald-700">Transport</p>
                <p class="mt-3 text-sm leading-6 text-zinc-600">
                    Cars and fuel-based travel contribute to greenhouse gas emissions. Walking, shared transport, and shorter trips can reduce the impact of daily movement.
                </p>
            </article>

            <article class="rounded-[1.1rem] border border-zinc-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-emerald-700">Food</p>
                <p class="mt-3 text-sm leading-6 text-zinc-600">
                    Food systems affect land use, water, transport, and emissions. More balanced and lower-emission food choices can reduce pressure on the climate.
                </p>
            </article>

            <article class="rounded-[1.1rem] border border-zinc-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-emerald-700">Energy Use</p>
                <p class="mt-3 text-sm leading-6 text-zinc-600">
                    Device use and electricity demand also matter. Saving power, reducing unnecessary screen time, and turning off idle devices are small but meaningful habits.
                </p>
            </article>
        </section>

        <section class="grid gap-4 lg:grid-cols-[minmax(0,1.15fr)_minmax(0,1fr)]">
            <article class="rounded-[1.2rem] border border-emerald-100 bg-white p-6 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-emerald-700">SDG 13</p>
                <h2 class="mt-2 text-xl font-bold text-zinc-900">Climate Action</h2>
                <div class="mt-4 space-y-4 text-sm leading-7 text-zinc-600">
                    <p>
                        Sustainable Development Goal 13 calls for urgent action to combat climate change and its impacts. It focuses on awareness, resilience, education, and practical action at both community and individual levels.
                    </p>
                    <p>
                        Eco Track supports this goal in a small but direct way. It helps users understand their daily carbon-related choices, reflect on patterns, compare progress, and build habits that can lower emissions over time.
                    </p>
                    <p>
                        By making climate-related behavior visible through logs, predictions, tips, achievements, and classroom motivation, Eco Track turns abstract climate action into something people can practice day by day.
                    </p>
                </div>
            </article>

            <div class="space-y-4">
                <div class="flex min-h-[200px] items-center justify-center rounded-[1.2rem] border border-dashed border-zinc-300 bg-zinc-50 p-6 text-center text-sm text-zinc-400">
                    Placeholder SDG 13 image
                </div>

                <article class="rounded-[1.2rem] border border-zinc-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-zinc-500">Eco Track And SDG 13</p>
                    <ul class="mt-3 space-y-3 text-sm leading-6 text-zinc-600">
                        <li>Improves awareness through visible carbon logs</li>
                        <li>Encourages habit change through practical feedback</li>
                        <li>Builds community motivation through rankings and achievements</li>
                        <li>Connects personal action to a wider sustainability goal</li>
                    </ul>
                </article>
            </div>
        </section>

        <section class="rounded-[1.2rem] border border-zinc-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-emerald-700">Action Guide</p>
                    <h2 class="mt-2 text-xl font-bold text-zinc-900">Simple ways to reduce climate impact</h2>
                </div>
                <p class="max-w-xl text-sm text-zinc-500">These are basic practical tips that align with the kind of behaviors Eco Track already helps you observe.</p>
            </div>

            <div class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <article class="rounded-[1rem] border border-zinc-200 bg-zinc-50 p-4">
                    <p class="text-sm font-semibold text-zinc-900">Choose lower-emission transport</p>
                    <p class="mt-2 text-sm leading-6 text-zinc-600">Walk when possible, combine errands, or use shared transport for routine trips.</p>
                </article>

                <article class="rounded-[1rem] border border-zinc-200 bg-zinc-50 p-4">
                    <p class="text-sm font-semibold text-zinc-900">Be mindful of food choices</p>
                    <p class="mt-2 text-sm leading-6 text-zinc-600">Try lower-emission meals more often and avoid unnecessary food waste.</p>
                </article>

                <article class="rounded-[1rem] border border-zinc-200 bg-zinc-50 p-4">
                    <p class="text-sm font-semibold text-zinc-900">Reduce unnecessary electricity use</p>
                    <p class="mt-2 text-sm leading-6 text-zinc-600">Switch off devices when idle and avoid leaving gadgets running without need.</p>
                </article>

                <article class="rounded-[1rem] border border-zinc-200 bg-zinc-50 p-4">
                    <p class="text-sm font-semibold text-zinc-900">Track and improve consistently</p>
                    <p class="mt-2 text-sm leading-6 text-zinc-600">Use your logs and predictions as feedback so small changes become visible over time.</p>
                </article>
            </div>
        </section>
    </div>
</x-layouts::app>
