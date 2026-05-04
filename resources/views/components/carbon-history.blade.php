<section
    x-data="carbonHistory({
        logsUrl: '{{ url('/api/user-logs') }}',
        csrfToken: '{{ csrf_token() }}',
    })"
    x-init="init()"
    class="eco-page-palette w-full px-2 py-2"
>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    <div class="overflow-hidden" style="border-radius: 0.35rem !important;">
        <div class="pb-4">
            <p class="text-sm font-semibold uppercase tracking-[0.28em] text-emerald-700">Carbon History</p>
            <h2 class="mt-1 text-2xl font-black text-zinc-900 dark:text-zinc-100">See your carbon footprint over time.</h2>
            <p class="mt-1 max-w-2xl text-sm text-zinc-600 dark:text-zinc-400">
                Review past logs, track your total emissions, export your history, and spot trends in your daily habits.
            </p>
        </div>

        <div class="space-y-4">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <!-- Tabs Component (shadcn-inspired) -->
                <div class="inline-flex items-center rounded-lg bg-zinc-100 p-1 dark:bg-zinc-800">
                    <button
                        type="button"
                        @click="setFilter('7')"
                        class="inline-flex items-center justify-center whitespace-nowrap rounded-md px-4 py-2 text-sm font-medium transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50"
                        :class="filter === '7'
                            ? 'bg-white text-emerald-700 shadow-sm dark:bg-zinc-950 dark:text-emerald-400'
                            : 'text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100'"
                    >
                        Last 7 days
                    </button>
                    <button
                        type="button"
                        @click="setFilter('30')"
                        class="inline-flex items-center justify-center whitespace-nowrap rounded-md px-4 py-2 text-sm font-medium transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50"
                        :class="filter === '30'
                            ? 'bg-white text-emerald-700 shadow-sm dark:bg-zinc-950 dark:text-emerald-400'
                            : 'text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100'"
                    >
                        Last 30 days
                    </button>
                    <button
                        type="button"
                        @click="setFilter('all')"
                        class="inline-flex items-center justify-center whitespace-nowrap rounded-md px-4 py-2 text-sm font-medium transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50"
                        :class="filter === 'all'
                            ? 'bg-white text-emerald-700 shadow-sm dark:bg-zinc-950 dark:text-emerald-400'
                            : 'text-zinc-600 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-zinc-100'"
                    >
                        All time
                    </button>
                </div>

                <div>
                    <button
                        type="button"
                        @click="exportCsv"
                        class="inline-flex items-center justify-center bg-zinc-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-zinc-800 disabled:cursor-not-allowed disabled:opacity-50"
                        style="border-radius: 0.35rem !important;"
                        :disabled="filteredLogs.length === 0"
                    >
                        Export CSV
                    </button>
                </div>
            </div>

            <template x-if="error">
                <div class="border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700" style="border-radius: 0.35rem !important;" x-text="error"></div>
            </template>

            <div class="eco-page-card eco-page-card--emerald border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-950" style="border-radius: 0.30rem !important;">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800">
                        <thead class="bg-emerald-100 dark:bg-emerald-950/40">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-[0.22em] text-zinc-500">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-[0.22em] text-zinc-500">Transport</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-[0.22em] text-zinc-500">Distance</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-[0.22em] text-zinc-500">Diet</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-[0.22em] text-zinc-500">Gadget Hours</th>
                                <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-[0.22em] text-zinc-500">Total CO2</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                            <template x-if="filteredLogs.length === 0 && !loading">
                                <tr>
                                    <td colspan="6" class="px-4 py-10 text-center text-sm text-zinc-500 dark:text-zinc-400">
                                        No carbon logs found for this time range yet.
                                    </td>
                                </tr>
                            </template>

                            <template x-for="log in filteredLogs" :key="log.id">
                                <tr class="hover:bg-emerald-50/80 dark:hover:bg-emerald-950/30">
                                    <td class="whitespace-nowrap px-4 py-4 text-sm text-zinc-700 dark:text-zinc-300" x-text="formatDate(log.created_at)"></td>
                                    <td class="whitespace-nowrap px-4 py-4 text-sm font-medium text-zinc-900 dark:text-zinc-100" x-text="formatLabel(log.transport_type)"></td>
                                    <td class="whitespace-nowrap px-4 py-4 text-sm text-zinc-700 dark:text-zinc-300" x-text="`${log.distance} km`"></td>
                                    <td class="whitespace-nowrap px-4 py-4 text-sm text-zinc-700 dark:text-zinc-300" x-text="formatDiet(log.diet_type)"></td>
                                    <td class="whitespace-nowrap px-4 py-4 text-sm text-zinc-700 dark:text-zinc-300" x-text="`${log.gadget_hours} hrs`"></td>
                                    <td class="whitespace-nowrap px-4 py-4 text-sm font-semibold text-emerald-700 dark:text-emerald-400" x-text="`${Number(log.total_emission).toFixed(2)} kg`"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="eco-page-card eco-page-card--teal border border-emerald-200 bg-emerald-50 p-3 dark:border-emerald-900/40 dark:bg-zinc-900" style="border-radius: 0.30rem !important;">
                <div class="mb-4 flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.24em] text-emerald-700">Emission Trend</p>
                        <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">Line graph of your saved carbon logs.</p>
                    </div>
                    <div x-show="loading" x-cloak class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Loading...</div>
                </div>
                <div class="h-80 bg-white p-2 dark:bg-zinc-950" style="border-radius: 0.35rem !important;">
                    <canvas x-ref="chartCanvas"></canvas>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    function carbonHistory({ logsUrl, csrfToken }) {
        return {
            logs: [],
            filteredLogs: [],
            filter: 'all',
            loading: false,
            error: '',
            totalEmission: '0.00',
            chart: null,

            async init() {
                window.addEventListener('carbon-log-saved', async () => {
                    await this.fetchLogs();
                });

                await this.fetchLogs();
            },

            async fetchLogs() {
                this.loading = true;
                this.error = '';

                try {
                    const response = await fetch(logsUrl, {
                        method: 'GET',
                        credentials: 'same-origin',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        this.error = data?.message ?? 'Unable to fetch your carbon history right now.';
                        return;
                    }

                    this.logs = (data.logs ?? []).map((log) => ({
                        ...log,
                        distance: Number(log.distance ?? 0),
                        gadget_hours: Number(log.gadget_hours ?? 0),
                        total_emission: Number(log.total_emission ?? 0),
                    }));

                    this.applyFilter();
                } catch (error) {
                    this.error = 'Something went wrong while loading your carbon history.';
                } finally {
                    this.loading = false;
                }
            },

            setFilter(filter) {
                this.filter = filter;
                this.applyFilter();
            },

            applyFilter() {
                const now = new Date();

                this.filteredLogs = this.logs
                    .filter((log) => {
                        if (this.filter === 'all') {
                            return true;
                        }

                        const days = Number(this.filter);
                        const logDate = new Date(log.created_at);

                        if (Number.isNaN(logDate.getTime())) {
                            return false;
                        }

                        const diffInDays = (now - logDate) / (1000 * 60 * 60 * 24);

                        return diffInDays <= days;
                    })
                    .sort((a, b) => new Date(b.created_at) - new Date(a.created_at));

                this.totalEmission = this.filteredLogs
                    .reduce((sum, log) => sum + Number(log.total_emission), 0)
                    .toFixed(2);

                this.renderChart();
            },

            renderChart() {
                // Wait for DOM to be ready
                this.$nextTick(() => {
                    if (!this.$refs.chartCanvas || typeof Chart === 'undefined') {
                        return;
                    }

                    // Aggregate logs by date (daily totals)
                    const dailyData = this.aggregateByDate(this.filteredLogs);

                    const labels = dailyData.map(d => d.label);
                    const values = dailyData.map(d => d.total);
                    const chartData = dailyData; // Store for tooltip

                    // Destroy existing chart first
                    if (this.chart) {
                        this.chart.destroy();
                        this.chart = null;
                    }

                    // Handle empty data case
                    if (values.length === 0) {
                        return;
                    }

                    const shouldRotate = labels.length > 7;

                    this.chart = new Chart(this.$refs.chartCanvas, {
                        type: 'line',
                        data: {
                            labels,
                            datasets: [{
                                label: 'Total CO2',
                                data: values,
                                borderColor: '#059669',
                                backgroundColor: 'transparent',
                                fill: false,
                                tension: 0.4,
                                pointBackgroundColor: 'transparent',
                                pointBorderColor: 'transparent',
                                pointRadius: 0,
                                pointHoverRadius: 0,
                                borderWidth: 3,
                                borderJoinStyle: 'round',
                            }],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            animation: {
                                duration: 800,
                                easing: 'easeOutQuart'
                            },
                            plugins: {
                                legend: {
                                    display: false,
                                },
                                tooltip: {
                                    enabled: true,
                                    backgroundColor: document.documentElement.classList.contains('dark') ? 'rgba(15, 23, 42, 0.96)' : 'rgba(5, 150, 105, 0.95)',
                                    titleColor: '#fff',
                                    bodyColor: '#fff',
                                    padding: 12,
                                    cornerRadius: 8,
                                    displayColors: false,
                                    callbacks: {
                                        title: function(context) {
                                            const idx = context[0].dataIndex;
                                            return 'Date: ' + chartData[idx].label;
                                        },
                                        label: function(context) {
                                            const idx = context.dataIndex;
                                            const entry = chartData[idx];
                                            return [
                                                'Total: ' + entry.total.toFixed(2) + ' kg CO2',
                                                'Logs: ' + entry.count + ' entri' + (entry.count !== 1 ? 'es' : '')
                                            ];
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    grid: {
                                        display: true,
                                        color: document.documentElement.classList.contains('dark') ? 'rgba(148, 163, 184, 0.12)' : 'rgba(113, 113, 122, 0.06)',
                                        drawBorder: false,
                                    },
                                    ticks: {
                                        color: document.documentElement.classList.contains('dark') ? '#94a3b8' : '#71717a',
                                        font: {
                                            size: 11
                                        },
                                        maxRotation: shouldRotate ? 45 : 0,
                                        minRotation: shouldRotate ? 45 : 0,
                                    },
                                },
                                y: {
                                    min: 0,
                                    max: 100,
                                    title: {
                                        display: true,
                                        text: 'kg CO2',
                                        color: document.documentElement.classList.contains('dark') ? '#94a3b8' : '#71717a',
                                        font: {
                                            size: 12,
                                            weight: '500'
                                        }
                                    },
                                    ticks: {
                                        stepSize: 20,
                                        color: document.documentElement.classList.contains('dark') ? '#94a3b8' : '#71717a',
                                        font: {
                                            size: 11
                                        }
                                    },
                                    grid: {
                                        color: document.documentElement.classList.contains('dark') ? 'rgba(148, 163, 184, 0.12)' : 'rgba(113, 113, 122, 0.06)',
                                        drawBorder: false,
                                    },
                                },
                            },
                        },
                        plugins: [{
                            id: 'glowEffect',
                            beforeDraw: (chart) => {
                                const ctx = chart.ctx;
                                const meta = chart.getDatasetMeta(0);

                                if (!meta.data || meta.data.length === 0) return;

                                // Draw glow effect
                                ctx.save();
                                ctx.shadowColor = 'rgba(5, 150, 105, 0.4)';
                                ctx.shadowBlur = 15;
                                ctx.shadowOffsetX = 0;
                                ctx.shadowOffsetY = 0;

                                // Draw the line with glow
                                ctx.beginPath();
                                meta.data.forEach((point, index) => {
                                    const {x, y} = point.getProps(['x', 'y']);
                                    if (index === 0) {
                                        ctx.moveTo(x, y);
                                    } else {
                                        const prevPoint = meta.data[index - 1];
                                        const prevX = prevPoint.x;
                                        const prevY = prevPoint.y;
                                        const cp1x = prevX + (x - prevX) * 0.4;
                                        const cp1y = prevY;
                                        const cp2x = prevX + (x - prevX) * 0.6;
                                        const cp2y = y;
                                        ctx.bezierCurveTo(cp1x, cp1y, cp2x, cp2y, x, y);
                                    }
                                });

                                ctx.strokeStyle = '#059669';
                                ctx.lineWidth = 3;
                                ctx.lineJoin = 'round';
                                ctx.lineCap = 'round';
                                ctx.stroke();
                                ctx.restore();
                            }
                        }]
                    });
                });
            },

            aggregateByDate(logs) {
                const dailyMap = {};

                // Group by date (only dates that have logs)
                logs.forEach(log => {
                    const date = new Date(log.created_at);
                    const dateKey = date.toISOString().split('T')[0];

                    if (!dailyMap[dateKey]) {
                        dailyMap[dateKey] = {
                            date: dateKey,
                            label: date.toLocaleDateString('en', { month: 'short', day: 'numeric' }),
                            total: 0,
                            count: 0,
                            jsDate: date
                        };
                    }

                    dailyMap[dateKey].total += Number(log.total_emission || 0);
                    dailyMap[dateKey].count++;
                });

                // Convert to array and sort chronologically
                // Only include dates that have actual log data (no filling missing dates)
                return Object.values(dailyMap).sort((a, b) => a.jsDate - b.jsDate);
            },

            exportCsv() {
                const headers = ['Date', 'Transport', 'Distance (km)', 'Diet', 'Gadget Hours', 'Total CO2 (kg)'];
                const rows = this.filteredLogs.map((log) => [
                    this.formatDate(log.created_at),
                    this.formatLabel(log.transport_type),
                    log.distance,
                    this.formatDiet(log.diet_type),
                    log.gadget_hours,
                    Number(log.total_emission).toFixed(2),
                ]);

                const csv = [headers, ...rows]
                    .map((row) => row.map((value) => `"${String(value).replace(/"/g, '""')}"`).join(','))
                    .join('\n');

                const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                const url = URL.createObjectURL(blob);
                const link = document.createElement('a');

                link.href = url;
                link.setAttribute('download', `carbon-history-${this.filter}.csv`);
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                URL.revokeObjectURL(url);
            },

            formatDate(value) {
                if (!value) {
                    return '-';
                }

                return new Date(value).toLocaleDateString(undefined, {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric',
                });
            },

            formatDayAbbr(value) {
                if (!value) {
                    return '';
                }

                const date = new Date(value);
                const day = date.getDay();
                const days = ['Su', 'M', 'T', 'W', 'Th', 'F', 'Sa'];
                return days[day];
            },

            formatLabel(value) {
                if (!value) {
                    return '-';
                }

                return value
                    .split('_')
                    .map((part) => part.charAt(0).toUpperCase() + part.slice(1))
                    .join(' ');
            },

            formatDiet(value) {
                const labels = {
                    meat: 'Meat-heavy',
                    average: 'Average',
                    vegetarian: 'Vegetarian',
                    plant_based: 'Plant-based',
                };

                return labels[value] ?? this.formatLabel(value);
            },
        };
    }
</script>
