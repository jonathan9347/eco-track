@php
    $transportTotal = (float) (data_get($this, 'transportTotal')
        ?? data_get($this, 'breakdown.transport')
        ?? 0);

    $dietTotal = (float) (data_get($this, 'dietTotal')
        ?? data_get($this, 'breakdown.diet')
        ?? 0);

    $gadgetTotal = (float) (data_get($this, 'gadgetTotal')
        ?? data_get($this, 'breakdown.gadgets')
        ?? data_get($this, 'breakdown.gadget')
        ?? 0);

    $insightTitle = data_get($this, 'insightTitle')
        ?? data_get($this, 'ecoInsight.title')
        ?? 'Eco-Insight';

    $insightBody = data_get($this, 'insightBody')
        ?? data_get($this, 'ecoInsight.message')
        ?? data_get($this, 'insightMessage')
        ?? 'Your recent activity will appear here once more logs are saved.';

    $thisWeek = (float) (data_get($this, 'thisWeekEmission')
        ?? data_get($this, 'ecoInsight.this_week')
        ?? 0);

    $lastWeek = (float) (data_get($this, 'lastWeekEmission')
        ?? data_get($this, 'ecoInsight.last_week')
        ?? 0);

    $trendLabels = data_get($this, 'trendLabels')
        ?? collect(data_get($this, 'dailyTrend', []))->pluck('date')->values()->all()
        ?? [];

    $trendValues = data_get($this, 'trendValues')
        ?? collect(data_get($this, 'dailyTrend', []))->pluck('total')->values()->all()
        ?? [];

    $trendSeries = data_get($this, 'trendSeries')
        ?? collect($trendLabels)->zip($trendValues)->map(fn ($item) => [
            'date' => null,
            'label' => $item[0] ?? '',
            'emission' => (float) ($item[1] ?? 0),
        ])->values()->all();

    $hasTrendData = collect($trendValues)->filter(fn ($value) => (float) $value > 0)->isNotEmpty();
    $todayLabel = $trendLabels !== [] ? end($trendLabels) : 'Today';
    $yesterdayLabel = count($trendLabels) > 1 ? $trendLabels[count($trendLabels) - 2] : 'Yesterday';
    $todayValue = $trendValues !== [] ? (float) end($trendValues) : 0;
    $yesterdayValue = count($trendValues) > 1 ? (float) $trendValues[count($trendValues) - 2] : 0;
    $totalFootprint = $transportTotal + $dietTotal + $gadgetTotal;
    $recentHistory = collect($trendLabels)
        ->zip($trendValues)
        ->reverse()
        ->filter(fn ($item) => (float) ($item[1] ?? 0) > 0)
        ->take(2)
        ->map(fn ($item) => [
            'label' => $item[0] ?? 'Recent log',
            'value' => (float) ($item[1] ?? 0),
        ])
        ->values();
@endphp

@once
    <style>
        .dashboard-stats-grid {
            display: grid;
            gap: 1.25rem;
        }

        .dashboard-stats-section {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .dashboard-stats-heading {
            padding-bottom: 0.25rem;
        }

        .dashboard-trend-section {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .dashboard-trend-heading {
            padding-top: 0.2rem;
        }

        .dashboard-trend-card {
            overflow: hidden;
            border: 1px solid #dbe7f3;
            border-radius: 0.35rem;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        }

        .dashboard-trend-card__inner {
            padding: 1.5rem;
        }

        .dashboard-trend-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .dashboard-trend-nav {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.2rem;
            border: 1px solid #cfe6da;
            border-radius: 0.35rem;
            background: #ffffff;
        }

        .dashboard-trend-nav__button {
            border: none;
            border-radius: 0.25rem;
            background: transparent;
            color: #475569;
            font-size: 0.8rem;
            font-weight: 500;
            line-height: 1;
            padding: 0.45rem 0.7rem;
            cursor: pointer;
        }

        .dashboard-trend-nav__button.is-active {
            background: #059669;
            color: #ffffff;
        }

        .dashboard-trend-viewport {
            position: relative;
            min-height: 20rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.35rem;
            background:
                radial-gradient(circle at top, rgba(186, 230, 253, 0.22), transparent 45%),
                linear-gradient(180deg, #fcfeff 0%, #f5f9ff 100%);
            padding: 1rem;
        }

        .dashboard-trend-shell {
            display: grid;
            grid-template-columns: 3rem minmax(0, 1fr);
            gap: 0.75rem;
            align-items: stretch;
        }

        .dashboard-trend-y-axis {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 20rem;
            padding: 0.2rem 0 1.9rem;
        }

        .dashboard-trend-y-axis__tick {
            color: #64748b;
            font-size: 0.76rem;
            line-height: 1;
        }

        .dashboard-trend-svg {
            display: block;
            width: 100%;
            height: 18rem;
        }

        .dashboard-trend-grid-line {
            stroke: rgba(148, 163, 184, 0.18);
            stroke-width: 1;
        }

        .dashboard-trend-axis-line {
            stroke: rgba(148, 163, 184, 0.28);
            stroke-width: 1;
        }

        .dashboard-trend-area {
            fill: rgba(16, 185, 129, 0.16);
        }

        .dashboard-trend-line {
            fill: none;
            stroke: #059669;
            stroke-width: 1.75;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .dashboard-trend-point {
            fill: #ffffff;
            stroke: #059669;
            stroke-width: 2;
            cursor: pointer;
        }

        .dashboard-trend-tooltip {
            position: absolute;
            z-index: 10;
            min-width: 8rem;
            padding: 0.6rem 0.75rem;
            border: 1px solid rgba(5, 150, 105, 0.18);
            border-radius: 0.35rem;
            background: #ffffff;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
            pointer-events: none;
        }

        .dashboard-trend-tooltip__label {
            color: #64748b;
            font-size: 0.76rem;
            line-height: 1.3;
        }

        .dashboard-trend-tooltip__value {
            color: #0f172a;
            font-size: 0.88rem;
            font-weight: 600;
            line-height: 1.35;
            margin-top: 0.2rem;
        }

        .dashboard-trend-axis {
            display: grid;
            grid-template-columns: repeat(var(--trend-ticks, 1), minmax(0, 1fr));
            gap: 0.5rem;
            margin-top: 0.65rem;
        }

        .dashboard-trend-axis__tick {
            color: #64748b;
            font-size: 0.76rem;
            line-height: 1.25;
        }

        .dashboard-trend-axis__tick--center {
            text-align: center;
        }

        .dashboard-trend-axis__tick--end {
            text-align: right;
        }

        .dashboard-stat-card {
            display: flex;
            height: 100%;
            flex-direction: column;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            border-radius: 0.35rem;
            background: #fff;
        }

        .dashboard-stat-card__header {
            padding: 1.25rem;
            background: linear-gradient(90deg, #bbf7d0 0%, #dcfce7 55%, #ecfccb 100%);
        }

        .dashboard-stat-card__title {
            color: #0f172a;
            font-size: 1.65rem;
            font-weight: 600;
            letter-spacing: -0.025em;
            line-height: 1.15;
        }

        .dashboard-stat-card__body {
            flex: 1 1 auto;
            padding: 1rem 1.25rem 1.25rem;
        }

        .dashboard-stat-card--soft {
            border: 1px solid rgba(5, 150, 105, 0.28);
            border-radius: 0.35rem;
            background: #f4fbf7;
        }

        .dashboard-stat-card--soft .dashboard-stat-card__header {
            padding: 1.1rem 1.1rem 0.45rem;
            background: transparent;
        }

        .dashboard-stat-card--soft .dashboard-stat-card__body {
            padding: 0.25rem 0.9rem 0.95rem;
        }

        .dashboard-stat-card--soft .dashboard-stat-card__title {
            font-size: 1.45rem;
            font-weight: 500;
            letter-spacing: 0;
            line-height: 1.2;
        }

        .dashboard-breakdown-summary {
            padding: 0 0.2rem 0.9rem;
        }

        .dashboard-breakdown-total {
            color: #111827;
            font-size: 1.95rem;
            font-weight: 500;
            line-height: 1.1;
        }

        .dashboard-breakdown-subtitle {
            color: #64748b;
            font-size: 0.88rem;
            font-weight: 400;
            line-height: 1.4;
            margin-top: 0.15rem;
        }

        .dashboard-breakdown-list {
            overflow: hidden;
            border: 1px solid rgba(5, 150, 105, 0.28);
            border-radius: 0.35rem;
            background: #ffffff;
        }

        .dashboard-breakdown-row {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 1rem;
            align-items: center;
            padding: 0.95rem 1rem;
            border-bottom: 1px solid rgba(5, 150, 105, 0.28);
        }

        .dashboard-breakdown-row:last-child {
            border-bottom: none;
        }

        .dashboard-breakdown-row__title {
            color: #0f172a;
            font-size: 1rem;
            line-height: 1.35;
            font-weight: 400;
        }

        .dashboard-breakdown-row__meta {
            color: #6b7280;
            font-size: 0.78rem;
            line-height: 1.35;
            margin-top: 0.15rem;
        }

        .dashboard-breakdown-row__value {
            text-align: right;
            white-space: nowrap;
            color: #111827;
            font-size: 0.98rem;
            font-weight: 400;
        }

        .dashboard-insight-body {
            display: flex;
            flex: 1 1 auto;
            flex-direction: column;
            gap: 0.85rem;
        }

        .dashboard-insight-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
        }

        .dashboard-insight-kicker {
            color: #111827;
            font-size: 1.3rem;
            font-weight: 500;
            line-height: 1;
        }

        .dashboard-insight-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 2.2rem;
            padding: 0.45rem 0.9rem;
            border-radius: 0.35rem;
            background: #059669;
            color: #ffffff;
            font-size: 0.82rem;
            font-weight: 500;
            line-height: 1;
            text-decoration: none;
        }

        .dashboard-insight-action:hover {
            background: #047857;
        }

        .dashboard-insight-grid {
            display: grid;
            gap: 0.5rem;
        }

        .dashboard-insight-panel {
            border: 1px solid rgba(5, 150, 105, 0.28);
            border-radius: 0.35rem;
            background: #ffffff;
            padding: 0.9rem 0.9rem 0.85rem;
        }

        .dashboard-insight-panel__eyebrow {
            color: #6b7280;
            font-size: 0.75rem;
            line-height: 1.3;
        }

        .dashboard-insight-panel__title {
            color: #111827;
            font-size: 1rem;
            font-weight: 400;
            line-height: 1.35;
            margin-top: 0.4rem;
        }

        .dashboard-insight-panel__value {
            margin-top: 0.35rem;
            color: #1f7a46;
            font-size: 1rem;
            font-weight: 500;
            line-height: 1.2;
        }

        .dashboard-insight-history {
            border-radius: 0.35rem;
            border: 1px solid rgba(5, 150, 105, 0.28);
            background: #ffffff;
            overflow: hidden;
        }

        .dashboard-insight-history__item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 0.75rem 0.9rem;
            border-bottom: 1px solid rgba(5, 150, 105, 0.18);
        }

        .dashboard-insight-history__item:last-child {
            border-bottom: none;
        }

        .dashboard-insight-history__label {
            color: #475569;
            font-size: 0.82rem;
            line-height: 1.35;
        }

        .dashboard-insight-history__value {
            color: #0f172a;
            font-size: 0.86rem;
            line-height: 1.35;
            white-space: nowrap;
        }

        .dashboard-insight-history__empty {
            padding: 0.8rem 0.9rem;
            color: #64748b;
            font-size: 0.82rem;
            line-height: 1.4;
        }

        .dark .dashboard-trend-card {
            border-color: #2f3a46;
            background: linear-gradient(180deg, #111827 0%, #0f172a 100%);
        }

        .dark .dashboard-trend-nav {
            border-color: #334155;
            background: #111827;
        }

        .dark .dashboard-trend-nav__button {
            color: #cbd5e1;
        }

        .dark .dashboard-trend-viewport {
            border-color: #334155;
            background:
                radial-gradient(circle at top, rgba(16, 185, 129, 0.14), transparent 45%),
                linear-gradient(180deg, #0f172a 0%, #111827 100%);
        }

        .dark .dashboard-trend-y-axis__tick,
        .dark .dashboard-trend-axis__tick,
        .dark .dashboard-trend-tooltip__label,
        .dark .dashboard-breakdown-subtitle,
        .dark .dashboard-breakdown-row__meta,
        .dark .dashboard-insight-panel__eyebrow,
        .dark .dashboard-insight-history__label,
        .dark .dashboard-insight-history__empty {
            color: #94a3b8;
        }

        .dark .dashboard-trend-grid-line,
        .dark .dashboard-trend-axis-line {
            stroke: rgba(148, 163, 184, 0.18);
        }

        .dark .dashboard-trend-point {
            fill: #111827;
        }

        .dark .dashboard-trend-tooltip {
            border-color: #334155;
            background: #020617;
            box-shadow: 0 8px 24px rgba(2, 6, 23, 0.45);
        }

        .dark .dashboard-trend-tooltip__value,
        .dark .dashboard-stat-card__title,
        .dark .dashboard-breakdown-total,
        .dark .dashboard-breakdown-row__title,
        .dark .dashboard-breakdown-row__value,
        .dark .dashboard-insight-kicker,
        .dark .dashboard-insight-panel__title,
        .dark .dashboard-insight-history__value {
            color: #e5e7eb;
        }

        .dark .dashboard-stat-card {
            border-color: #334155;
            background: #0f172a;
        }

        .dark .dashboard-stat-card__header {
            background: linear-gradient(90deg, #14532d 0%, #166534 55%, #365314 100%);
        }

        .dark .dashboard-stat-card--soft {
            border-color: rgba(16, 185, 129, 0.22);
            background: #0b1220;
        }

        .dark .dashboard-breakdown-list,
        .dark .dashboard-insight-panel,
        .dark .dashboard-insight-history {
            border-color: rgba(16, 185, 129, 0.22);
            background: #111827;
        }

        .dark .dashboard-breakdown-row,
        .dark .dashboard-insight-history__item {
            border-color: rgba(16, 185, 129, 0.16);
        }

        @media (min-width: 420px) {
            .dashboard-insight-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (min-width: 640px) {
            .dashboard-stats-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                align-items: stretch;
            }

            .dashboard-trend-card__inner {
                padding: 1.75rem;
            }
        }
    </style>
@endonce

<section class="dashboard-stats-section" wire:poll.30s="refreshStats">
    <div class="dashboard-stats-grid">
        <article class="dashboard-stat-card dashboard-stat-card--soft">
            <div class="dashboard-stat-card__header">
                <h3 class="dashboard-stat-card__title">Emission Breakdown</h3>
            </div>

            <div class="dashboard-stat-card__body">
                <div class="dashboard-breakdown-summary">
                    <p class="dashboard-breakdown-total">{{ number_format($totalFootprint, 2) }} kg</p>
                    <p class="dashboard-breakdown-subtitle">Total footprint from your current dashboard breakdown.</p>
                </div>

                <div class="dashboard-breakdown-list">
                    <div class="dashboard-breakdown-row">
                        <div>
                            <p class="dashboard-breakdown-row__title">Transport</p>
                            <p class="dashboard-breakdown-row__meta">Travel use</p>
                        </div>
                        <p class="dashboard-breakdown-row__value">{{ number_format($transportTotal, 2) }} kg</p>
                    </div>

                    <div class="dashboard-breakdown-row">
                        <div>
                            <p class="dashboard-breakdown-row__title">Diet</p>
                            <p class="dashboard-breakdown-row__meta">Food intake</p>
                        </div>
                        <p class="dashboard-breakdown-row__value">{{ number_format($dietTotal, 2) }} kg</p>
                    </div>

                    <div class="dashboard-breakdown-row">
                        <div>
                            <p class="dashboard-breakdown-row__title">Gadgets</p>
                            <p class="dashboard-breakdown-row__meta">Device use</p>
                        </div>
                        <p class="dashboard-breakdown-row__value">{{ number_format($gadgetTotal, 2) }} kg</p>
                    </div>
                </div>
            </div>
        </article>

        <article class="dashboard-stat-card dashboard-stat-card--soft">
            <div class="dashboard-stat-card__header">
                <div class="dashboard-insight-head">
                    <h3 class="dashboard-stat-card__title">Eco-Insight</h3>
                    <a href="{{ route('carbon.history') }}" class="dashboard-insight-action" wire:navigate>View Log</a>
                </div>
            </div>

            <div class="dashboard-stat-card__body">
                <div class="dashboard-insight-body">
                    <p class="dashboard-insight-kicker">Highlights</p>

                    <div class="dashboard-insight-grid">
                        <div class="dashboard-insight-panel">
                            <p class="dashboard-insight-panel__eyebrow">Current insight</p>
                            <p class="dashboard-insight-panel__title">{{ $insightTitle }}</p>
                            <p class="dashboard-insight-panel__value">{{ number_format($thisWeek, 2) }} kg CO2</p>
                        </div>

                        <div class="dashboard-insight-panel">
                            <p class="dashboard-insight-panel__eyebrow">Previous period</p>
                            <p class="dashboard-insight-panel__title">Last Week</p>
                            <p class="dashboard-insight-panel__value">{{ number_format($lastWeek, 2) }} kg CO2</p>
                        </div>
                    </div>

                    <div class="dashboard-insight-history">
                        @forelse ($recentHistory as $historyItem)
                            <div class="dashboard-insight-history__item">
                                <p class="dashboard-insight-history__label">{{ $historyItem['label'] }}</p>
                                <p class="dashboard-insight-history__value">{{ number_format($historyItem['value'], 2) }} kg</p>
                            </div>
                        @empty
                            <p class="dashboard-insight-history__empty">No recent history yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </article>
    </div>
</section>

@once
    <script>
        function dashboardTrendChart(config) {
            return {
                series: config.series || [],
                range: 'month',
                hoveredPoint: null,
                tooltipX: 50,
                tooltipY: 50,
                points: [],
                linePath: '',
                areaPath: '',
                yTicks: [],
                xTicks: [],
                hasData: false,
                getLatestDate() {
                    const latest = this.series[this.series.length - 1]?.date;

                    return latest ? new Date(`${latest}T00:00:00`) : new Date();
                },
                formatDateKey(date) {
                    const year = date.getFullYear();
                    const month = `${date.getMonth() + 1}`.padStart(2, '0');
                    const day = `${date.getDate()}`.padStart(2, '0');

                    return `${year}-${month}-${day}`;
                },
                getFilteredSeries() {
                    const latestDate = this.getLatestDate();
                    const latestKey = this.formatDateKey(latestDate);
                    const latestMonth = latestDate.getMonth();
                    const latestYear = latestDate.getFullYear();
                    const startOfWeek = new Date(latestDate);
                    const dayOfWeek = (startOfWeek.getDay() + 6) % 7;
                    startOfWeek.setDate(startOfWeek.getDate() - dayOfWeek);
                    startOfWeek.setHours(0, 0, 0, 0);

                    return this.series.filter((item) => {
                        if (!item.date) {
                            return this.range === 'month';
                        }

                        const itemDate = new Date(`${item.date}T00:00:00`);

                        if (this.range === 'day') {
                            return item.date === latestKey;
                        }

                        if (this.range === 'week') {
                            return itemDate >= startOfWeek && itemDate <= latestDate;
                        }

                        return itemDate.getMonth() === latestMonth && itemDate.getFullYear() === latestYear;
                    });
                },
                formatValue(value) {
                    return Number(value || 0).toFixed(2);
                },
                buildChart() {
                    const visibleSeries = this.getFilteredSeries();
                    const seriesWithFallback = visibleSeries.length ? visibleSeries : this.series.slice(-1);
                    const values = seriesWithFallback.map((item) => Number(item.emission || 0));
                    const maxValue = Math.max(...values, 0);
                    const chartMax = maxValue > 0 ? Math.ceil(maxValue * 1.15) : 1;
                    const chartHeight = 84;
                    const pointCount = seriesWithFallback.length;

                    this.hasData = values.some((value) => value > 0);
                    this.yTicks = [0, 0.25, 0.5, 0.75, 1].map((ratio) => ({
                        value: chartMax * (1 - ratio),
                        y: ratio * chartHeight,
                    }));

                    this.points = seriesWithFallback.map((item, index) => {
                        const x = pointCount === 1 ? 50 : (index / (pointCount - 1)) * 100;
                        const y = chartHeight - ((Number(item.emission || 0) / chartMax) * chartHeight);

                        return {
                            key: `${item.date ?? index}-${index}`,
                            label: item.label,
                            value: Number(item.emission || 0),
                            x,
                            y,
                        };
                    });

                    const tickInterval = this.range === 'day'
                        ? 1
                        : this.range === 'week'
                            ? 1
                            : Math.max(1, Math.ceil(pointCount / 10));

                    this.xTicks = this.points
                        .filter((point, index) => (
                            this.range === 'day'
                            || index === 0
                            || index === pointCount - 1
                            || index % tickInterval === 0
                        ))
                        .filter((point, index, list) => list.findIndex((entry) => entry.key === point.key) === index)
                        .map((point) => ({
                            key: point.key,
                            label: point.label,
                        }));

                    if (!this.points.length) {
                        this.linePath = '';
                        this.areaPath = '';

                        return;
                    }

                    const lineCommands = this.points.map((point, index) => `${index === 0 ? 'M' : 'L'} ${point.x} ${point.y}`).join(' ');
                    this.linePath = lineCommands;
                    this.areaPath = `${lineCommands} L ${this.points[this.points.length - 1].x} ${chartHeight} L ${this.points[0].x} ${chartHeight} Z`;
                },
                setRange(range) {
                    this.range = range;
                    this.hoveredPoint = null;
                    this.buildChart();
                },
                showTooltip(point) {
                    this.hoveredPoint = point;
                    this.tooltipX = point.x;
                    this.tooltipY = point.y;
                },
                hideTooltip() {
                    this.hoveredPoint = null;
                },
                init() {
                    this.buildChart();
                },
            };
        }
    </script>
@endonce
