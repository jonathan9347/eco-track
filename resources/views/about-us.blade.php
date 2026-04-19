<x-layouts::app :title="__('About Us')">
    <div class="mx-auto flex w-full max-w-6xl flex-col gap-6">
        <section class="overflow-hidden border border-emerald-100 px-6 py-8 text-white shadow-lg sm:px-8" style="background: linear-gradient(135deg, #052e16 0%, #166534 55%, #4d7c0f 100%); border-radius: 0.35rem !important;">
            <p class="text-sm font-semibold uppercase tracking-[0.28em]" style="color: rgba(236, 253, 245, 0.82);">About Eco Track</p>
            <h1 class="mt-3 text-3xl font-black sm:text-4xl">Helping students build greener habits with data they can understand.</h1>
            <p class="mt-3 max-w-3xl text-sm leading-7 sm:text-base" style="color: rgba(240, 253, 244, 0.90);">
                Eco Track is a student-focused sustainability platform designed to turn everyday choices into visible climate action. We make carbon awareness easier, more motivating, and more social inside the classroom.
            </p>
        </section>

        <section class="grid gap-6 lg:grid-cols-[1.15fr_0.85fr]">
            <article class="border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-950 dark:shadow-none" style="border-radius: 0.35rem !important;">
                <p class="text-xs font-bold uppercase tracking-[0.24em] text-emerald-700">Our Mission</p>
                <h2 class="mt-3 text-2xl font-black text-zinc-900 dark:text-zinc-100">Make climate responsibility feel practical, local, and achievable.</h2>
                <p class="mt-4 text-sm leading-7 text-zinc-600 dark:text-zinc-400">
                    Many students want to help the environment but do not always know where to begin. Eco Track bridges that gap by translating transport, food, and gadget habits into clear carbon insights that feel relevant to daily life in the Philippines.
                </p>
                <p class="mt-4 text-sm leading-7 text-zinc-600 dark:text-zinc-400">
                    We believe sustainability grows faster when it is visible, measurable, and shared with a community. That is why Eco Track combines personal tracking, class participation, and friendly competition in one experience.
                </p>
            </article>

            <article class="border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-950 dark:shadow-none" style="border-radius: 0.35rem !important;">
                <p class="text-xs font-bold uppercase tracking-[0.24em] text-emerald-700">What We Value</p>
                <div class="mt-4 grid gap-4">
                    <div class="bg-emerald-50 p-4 dark:bg-emerald-950/20" style="border-radius: 0.35rem !important;">
                        <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-100">Clarity</h3>
                        <p class="mt-2 text-sm leading-6 text-zinc-600 dark:text-zinc-400">We simplify carbon data so students can act on it with confidence.</p>
                    </div>
                    <div class="bg-emerald-50 p-4 dark:bg-emerald-950/20" style="border-radius: 0.35rem !important;">
                        <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-100">Community</h3>
                        <p class="mt-2 text-sm leading-6 text-zinc-600 dark:text-zinc-400">We encourage classrooms to learn, improve, and celebrate progress together.</p>
                    </div>
                    <div class="bg-emerald-50 p-4 dark:bg-emerald-950/20" style="border-radius: 0.35rem !important;">
                        <h3 class="text-base font-bold text-zinc-900 dark:text-zinc-100">Action</h3>
                        <p class="mt-2 text-sm leading-6 text-zinc-600 dark:text-zinc-400">We focus on small daily habits that create meaningful long-term impact.</p>
                    </div>
                </div>
            </article>
        </section>

        <section class="grid gap-6 md:grid-cols-3">
            <article class="border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-950 dark:shadow-none" style="border-radius: 0.35rem !important;">
                <p class="text-xs font-bold uppercase tracking-[0.24em] text-emerald-700">Track</p>
                <h3 class="mt-3 text-xl font-black text-zinc-900 dark:text-zinc-100">Daily habits</h3>
                <p class="mt-3 text-sm leading-7 text-zinc-600 dark:text-zinc-400">Log transport, diet, and gadget use to understand the footprint behind everyday routines.</p>
            </article>
            <article class="border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-950 dark:shadow-none" style="border-radius: 0.35rem !important;">
                <p class="text-xs font-bold uppercase tracking-[0.24em] text-emerald-700">Compare</p>
                <h3 class="mt-3 text-xl font-black text-zinc-900 dark:text-zinc-100">Class progress</h3>
                <p class="mt-3 text-sm leading-7 text-zinc-600 dark:text-zinc-400">Use leaderboards and shared goals to keep climate action visible and motivating.</p>
            </article>
            <article class="border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-950 dark:shadow-none" style="border-radius: 0.35rem !important;">
                <p class="text-xs font-bold uppercase tracking-[0.24em] text-emerald-700">Improve</p>
                <h3 class="mt-3 text-xl font-black text-zinc-900 dark:text-zinc-100">Greener routines</h3>
                <p class="mt-3 text-sm leading-7 text-zinc-600 dark:text-zinc-400">Build awareness over time and turn eco-friendly actions into repeatable habits.</p>
            </article>
        </section>
    </div>
</x-layouts::app>
