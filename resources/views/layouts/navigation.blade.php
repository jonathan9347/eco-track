<nav class="flex items-center gap-4">
    <a
        href="{{ url('/carbon-history') }}"
        class="inline-flex items-center rounded-full px-4 py-2 text-sm font-medium text-zinc-700 transition hover:bg-emerald-50 hover:text-emerald-700"
    >
        &#x1F4CA; My Carbon Logs
    </a>
    <a
        href="{{ url('/achievements') }}"
        class="inline-flex items-center rounded-full px-4 py-2 text-sm font-medium text-zinc-700 transition hover:bg-emerald-50 hover:text-emerald-700"
    >
        &#x1F3C5; Achievements
    </a>
    @if (auth()->user()?->is_admin)
        <a
            href="{{ url('/admin') }}"
            class="inline-flex items-center rounded-full px-4 py-2 text-sm font-medium text-zinc-700 transition hover:bg-emerald-50 hover:text-emerald-700"
        >
            &#x2699;&#xFE0F; Admin Panel
        </a>
    @endif
</nav>
