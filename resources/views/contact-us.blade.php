<x-layouts::app :title="__('Contact Us')">
    <div class="eco-page-palette mx-auto flex w-full max-w-6xl flex-col gap-6">
        <section class="eco-page-hero overflow-hidden border border-emerald-100 px-6 py-8 text-white shadow-lg sm:px-8" style="border-radius: 0.35rem !important;">
            <p class="text-sm font-semibold uppercase tracking-[0.28em]" style="color: rgba(236, 253, 245, 0.82);">Contact Us</p>
            <h1 class="mt-3 text-3xl font-black sm:text-4xl">We would love to hear from you.</h1>
            <p class="mt-3 max-w-3xl text-sm leading-7 sm:text-base" style="color: rgba(240, 253, 244, 0.90);">
                Reach out for questions, feedback, classroom partnerships, or support related to Eco Track. We are always open to ideas that help students take stronger climate action.
            </p>
        </section>

        <section class="grid gap-6 lg:grid-cols-[0.95fr_1.05fr]">
            <article class="eco-page-card eco-page-card--emerald border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-950 dark:shadow-none" style="border-radius: 0.35rem !important;">
                <p class="text-xs font-bold uppercase tracking-[0.24em] text-emerald-700">Get In Touch</p>
                <div class="mt-5 grid gap-4">
                    <div class="eco-page-soft bg-emerald-50 p-4 dark:bg-emerald-950/20" style="border-radius: 0.35rem !important;">
                        <p class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Email</p>
                        <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">support@ecotrack.app</p>
                    </div>
                    <div class="eco-page-soft bg-emerald-50 p-4 dark:bg-emerald-950/20" style="border-radius: 0.35rem !important;">
                        <p class="text-sm font-bold text-zinc-900 dark:text-zinc-100">School Partnerships</p>
                        <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">partners@ecotrack.app</p>
                    </div>
                    <div class="eco-page-soft bg-emerald-50 p-4 dark:bg-emerald-950/20" style="border-radius: 0.35rem !important;">
                        <p class="text-sm font-bold text-zinc-900 dark:text-zinc-100">Location</p>
                        <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">Philippines</p>
                    </div>
                </div>
            </article>

            <article class="eco-page-card eco-page-card--amber border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-950 dark:shadow-none" style="border-radius: 0.35rem !important;">
                <p class="text-xs font-bold uppercase tracking-[0.24em] text-emerald-700">Send A Message</p>
                <form class="mt-5 grid gap-4">
                    <input type="text" placeholder="Your name" class="border border-zinc-200 px-4 py-3 text-sm text-zinc-900 shadow-sm outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:shadow-none dark:focus:ring-emerald-900/40" style="border-radius: 0.35rem !important;">
                    <input type="email" placeholder="Your email" class="border border-zinc-200 px-4 py-3 text-sm text-zinc-900 shadow-sm outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:shadow-none dark:focus:ring-emerald-900/40" style="border-radius: 0.35rem !important;">
                    <input type="text" placeholder="Subject" class="border border-zinc-200 px-4 py-3 text-sm text-zinc-900 shadow-sm outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:shadow-none dark:focus:ring-emerald-900/40" style="border-radius: 0.35rem !important;">
                    <textarea rows="6" placeholder="Write your message here" class="border border-zinc-200 px-4 py-3 text-sm text-zinc-900 shadow-sm outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-100 dark:shadow-none dark:focus:ring-emerald-900/40" style="border-radius: 0.35rem !important;"></textarea>
                    <button type="button" class="inline-flex items-center justify-center bg-emerald-600 px-6 py-3 text-sm font-semibold text-white transition hover:bg-emerald-700" style="border-radius: 0.35rem !important;">
                        Send Message
                    </button>
                    <p class="text-xs leading-6 text-zinc-500 dark:text-zinc-400">This is currently a static contact form layout. If you want, I can wire it to email, database storage, or Firebase next.</p>
                </form>
            </article>
        </section>
    </div>
</x-layouts::app>
