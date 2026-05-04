<x-layouts::app :title="__('Reports')">
    <div class="eco-page-palette space-y-8">
        <section class="eco-page-hero eco-page-hero--subtle flex flex-col gap-4 px-6 py-7 sm:flex-row sm:items-end sm:justify-between">
            <div class="min-w-0">
                <p class="text-sm font-semibold uppercase tracking-[0.28em] text-emerald-700">Resources</p>
                <h1 class="mt-1 text-2xl font-black text-zinc-900 sm:text-3xl">Reports</h1>
                <p class="mt-1 max-w-3xl text-sm text-zinc-600">
                    Review your Eco Track activity in a simple report view and export your recorded data for documentation, reflection, or class reporting.
                </p>
            </div>

            <div class="flex flex-wrap gap-3">
                <a
                    href="{{ route('reports.export.csv') }}"
                    class="inline-flex items-center justify-center rounded-xl border border-zinc-300 bg-white px-4 py-2.5 text-sm font-semibold text-zinc-700 shadow-sm transition hover:border-emerald-200 hover:bg-emerald-50 hover:text-zinc-900"
                >
                    Export CSV
                </a>
                <a
                    href="{{ route('reports.export.json') }}"
                    class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-700"
                >
                    Export JSON
                </a>
            </div>
        </section>

        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <article class="eco-page-card eco-page-card--emerald rounded-[1.1rem] border border-zinc-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-zinc-500">Saved Logs</p>
                <p class="mt-3 text-3xl font-black text-zinc-900">{{ number_format($summary['log_count']) }}</p>
                <p class="mt-2 text-sm text-zinc-600">Total records available in your current Eco Track history.</p>
            </article>

            <article class="eco-page-card eco-page-card--amber rounded-[1.1rem] border border-zinc-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-zinc-500">Total Emission</p>
                <p class="mt-3 text-3xl font-black text-zinc-900">{{ number_format($summary['total_emission'], 2) }} kg</p>
                <p class="mt-2 text-sm text-zinc-600">Combined emissions from all saved logs.</p>
            </article>

            <article class="eco-page-card eco-page-card--lime rounded-[1.1rem] border border-zinc-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-zinc-500">Average Per Log</p>
                <p class="mt-3 text-3xl font-black text-zinc-900">{{ number_format($summary['average_emission'], 2) }} kg</p>
                <p class="mt-2 text-sm text-zinc-600">Average emission value across your saved entries.</p>
            </article>

            <article class="eco-page-card eco-page-card--teal rounded-[1.1rem] border border-zinc-200 bg-white p-5 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-zinc-500">Report Range</p>
                <p class="mt-3 text-sm font-semibold text-zinc-900">
                    {{ $summary['first_log_date'] ? \Carbon\Carbon::parse($summary['first_log_date'])->format('M j, Y') : 'No data' }}
                    @if($summary['latest_log_date'])
                        to {{ \Carbon\Carbon::parse($summary['latest_log_date'])->format('M j, Y') }}
                    @endif
                </p>
                <p class="mt-2 text-sm text-zinc-600">Time span covered by this export-ready report.</p>
            </article>
        </section>

        <section class="grid gap-4 lg:grid-cols-[minmax(0,1.2fr)_minmax(300px,0.9fr)]">
            <article class="eco-page-card eco-page-card--emerald rounded-[1.2rem] border border-zinc-200 bg-white p-6 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-emerald-700">Emission Breakdown</p>
                <h2 class="mt-2 text-xl font-bold text-zinc-900">Where your report comes from</h2>

                <div class="mt-6 space-y-4">
                    <div class="eco-page-soft rounded-[1rem] border border-zinc-200 bg-zinc-50 p-4">
                        <div class="flex items-center justify-between gap-4">
                            <p class="text-sm font-semibold text-zinc-900">Transport</p>
                            <p class="text-sm font-bold text-zinc-900">{{ number_format($summary['transport_total'], 2) }} kg</p>
                        </div>
                        <p class="mt-2 text-sm text-zinc-600">Travel emissions based on recorded transport type and distance.</p>
                    </div>

                    <div class="eco-page-soft rounded-[1rem] border border-zinc-200 bg-zinc-50 p-4">
                        <div class="flex items-center justify-between gap-4">
                            <p class="text-sm font-semibold text-zinc-900">Diet</p>
                            <p class="text-sm font-bold text-zinc-900">{{ number_format($summary['diet_total'], 2) }} kg</p>
                        </div>
                        <p class="mt-2 text-sm text-zinc-600">Food-related emissions from the diet category recorded in your logs.</p>
                    </div>

                    <div class="eco-page-soft rounded-[1rem] border border-zinc-200 bg-zinc-50 p-4">
                        <div class="flex items-center justify-between gap-4">
                            <p class="text-sm font-semibold text-zinc-900">Gadgets</p>
                            <p class="text-sm font-bold text-zinc-900">{{ number_format($summary['gadget_total'], 2) }} kg</p>
                        </div>
                        <p class="mt-2 text-sm text-zinc-600">Electricity-related impact estimated from gadget use hours.</p>
                    </div>
                </div>
            </article>

            <article class="eco-page-card eco-page-card--amber rounded-[1.2rem] border border-zinc-200 bg-white p-6 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-zinc-500">Export Notes</p>
                <ul class="mt-4 space-y-3 text-sm leading-6 text-zinc-600">
                    <li>CSV export is useful for spreadsheets and simple printed reporting.</li>
                    <li>JSON export is useful for archiving structured data or future integrations.</li>
                    <li>All exports are based on the same carbon logs saved in your Eco Track account.</li>
                    <li>This page is intentionally simple so it can serve as a clean documentation and export screen.</li>
                </ul>
            </article>
        </section>

        <section class="eco-page-card eco-page-card--teal rounded-[1.2rem] border border-zinc-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-emerald-700">Monthly Summary</p>
                    <h2 class="mt-2 text-xl font-bold text-zinc-900">Activity by month</h2>
                </div>
                <p class="max-w-xl text-sm text-zinc-500">A lightweight monthly overview of your saved reporting data.</p>
            </div>

            @if(count($monthly) > 0)
                <div class="mt-6 overflow-hidden rounded-[1rem] border border-zinc-200">
                    <table class="min-w-full divide-y divide-zinc-200">
                        <thead class="bg-zinc-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.16em] text-zinc-500">Month</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.16em] text-zinc-500">Logs</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.16em] text-zinc-500">Total Emission</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-200 bg-white">
                            @foreach($monthly as $month)
                                <tr>
                                    <td class="px-4 py-3 text-sm font-medium text-zinc-900">{{ $month['month'] }}</td>
                                    <td class="px-4 py-3 text-sm text-zinc-600">{{ number_format($month['log_count']) }}</td>
                                    <td class="px-4 py-3 text-sm text-zinc-600">{{ number_format($month['total_emission'], 2) }} kg</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="mt-6 rounded-[1rem] border border-dashed border-zinc-300 bg-zinc-50 px-5 py-10 text-center text-sm text-zinc-500">
                    No report data available yet. Start saving carbon logs to generate exportable reports.
                </div>
            @endif
        </section>
    </div>
</x-layouts::app>
