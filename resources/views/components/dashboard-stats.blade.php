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

    $totalFootprint = $transportTotal + $dietTotal + $gadgetTotal;

    $recentHistory = collect($trendLabels)
        ->zip($trendValues)
        ->reverse()
        ->filter(fn ($item) => (float) ($item[1] ?? 0) > 0)
        ->take(3)
        ->map(fn ($item) => [
            'label' => $item[0] ?? 'Recent log',
            'value' => (float) ($item[1] ?? 0),
        ])
        ->values();

    $comparisonPercent = $lastWeek > 0
        ? (int) round(max(0, min(100, (($lastWeek - $thisWeek) / $lastWeek) * 100)))
        : ($thisWeek <= 0 ? 100 : 0);

    $progressAngle = max(8, min(360, ($comparisonPercent / 100) * 360));

    $goalItems = collect([
        [
            'label' => 'Lower commute emissions',
            'checked' => $transportTotal <= 5,
        ],
        [
            'label' => 'Choose a lighter meal mix',
            'checked' => $dietTotal <= 4,
        ],
        [
            'label' => 'Keep device usage efficient',
            'checked' => $gadgetTotal <= 3,
        ],
    ]);

    $monthlyLabel = 'This Month';
@endphp

@once
    <style>
        .dashboard-side-grid {
            display: grid;
            gap: 1rem;
        }

        .dashboard-overview-card {
            overflow: hidden;
            border: 1px solid rgba(120, 157, 112, 0.18);
            border-radius: 0.35rem;
            background: linear-gradient(180deg, #fffdf8 0%, #ffffff 100%);
        }

        .dashboard-overview-card__inner {
            display: flex;
            flex-direction: column;
            gap: 1.2rem;
            padding: 1.25rem;
        }

        .dashboard-overview-card__head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .dashboard-overview-card__title {
            margin: 0;
            color: #1f2f20;
            font-size: 1.15rem;
            font-weight: 800;
            letter-spacing: -0.035em;
        }

        .dashboard-overview-card__period {
            color: #6b816d;
            font-size: 0.84rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .dashboard-overview-card__period::after {
            content: '⌄';
            margin-left: 0.3rem;
            font-size: 0.85em;
        }

        .dashboard-overview-card__body {
            display: grid;
            gap: 1rem;
            align-items: center;
        }

        .dashboard-ring {
            --progress-angle: 180deg;
            position: relative;
            width: 7.4rem;
            height: 7.4rem;
            border-radius: 999px;
            background: conic-gradient(#4f8a58 0 var(--progress-angle), #e8e2d6 var(--progress-angle) 360deg);
            margin-inline: auto;
        }

        .dashboard-ring::after {
            content: '';
            position: absolute;
            inset: 0.72rem;
            border-radius: 999px;
            background: #fffdf8;
        }

        .dashboard-ring__content {
            position: absolute;
            inset: 0;
            z-index: 1;
            display: grid;
            place-items: center;
            text-align: center;
            padding: 1rem;
        }

        .dashboard-ring__value {
            color: #202f22;
            font-size: 2rem;
            font-weight: 800;
            line-height: 1;
            letter-spacing: -0.05em;
        }

        .dashboard-ring__label {
            color: #657462;
            font-size: 0.82rem;
            font-weight: 700;
            line-height: 1.2;
            margin-top: 0.2rem;
        }

        .dashboard-overview-metric {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.8rem;
        }

        .dashboard-overview-metric__value {
            color: #4b8b56;
            font-size: 2.6rem;
            font-weight: 800;
            line-height: 1;
            letter-spacing: -0.06em;
        }

        .dashboard-overview-metric__badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 2.25rem;
            height: 2.25rem;
            border-radius: 0.7rem;
            background: #e7f2e5;
            color: #4b8b56;
        }

        .dashboard-mini-grid {
            display: grid;
            gap: 1rem;
        }

        .dashboard-mini-card {
            overflow: hidden;
            border: 1px solid rgba(120, 157, 112, 0.18);
            border-radius: 0.35rem;
            background: linear-gradient(180deg, #ffffff 0%, #fefcf7 100%);
            padding: 1.15rem;
        }

        .dashboard-mini-card__title {
            margin: 0;
            color: #1f2f20;
            font-size: 1rem;
            font-weight: 800;
            letter-spacing: -0.03em;
        }

        .dashboard-mini-card__subtitle {
            margin: 0.28rem 0 0;
            color: #6b7f6c;
            font-size: 0.82rem;
            line-height: 1.45;
        }

        .dashboard-activity-list,
        .dashboard-goal-list {
            display: grid;
            gap: 0.75rem;
            margin-top: 1rem;
        }

        .dashboard-activity-item,
        .dashboard-goal-item {
            display: grid;
            grid-template-columns: auto minmax(0, 1fr);
            gap: 0.7rem;
            align-items: center;
        }

        .dashboard-activity-icon,
        .dashboard-goal-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 2rem;
            height: 2rem;
            border-radius: 999px;
            background: #ecf3e5;
            color: #3f6d43;
            font-size: 0.8rem;
            font-weight: 800;
        }

        .dashboard-activity-label,
        .dashboard-goal-label {
            color: #243425;
            font-size: 0.92rem;
            font-weight: 700;
            line-height: 1.35;
        }

        .dashboard-activity-meta {
            color: #6c7a69;
            font-size: 0.78rem;
            line-height: 1.3;
            margin-top: 0.1rem;
        }

        .dashboard-goal-icon.is-active {
            background: #2d6138;
            color: #f8fff4;
        }

        .dashboard-goal-icon.is-inactive {
            background: #eef0ea;
            color: #8a9487;
        }

        .dashboard-mini-card__footer {
            margin-top: 1rem;
        }

        .dashboard-mini-card__link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 2.5rem;
            width: 100%;
            border: 1px solid #d7dfd2;
            border-radius: 0.35rem;
            background: #ffffff;
            color: #456947;
            font-size: 0.88rem;
            font-weight: 800;
            text-decoration: none;
        }

        .dashboard-breakdown-inline {
            display: grid;
            gap: 0.65rem;
            margin-top: 1rem;
        }

        .dashboard-breakdown-inline__row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            color: #394c3b;
            font-size: 0.88rem;
            font-weight: 700;
        }

        .dashboard-breakdown-inline__bar {
            overflow: hidden;
            height: 0.45rem;
            border-radius: 999px;
            background: #ece6da;
            margin-top: 0.35rem;
        }

        .dashboard-breakdown-inline__fill {
            display: block;
            height: 100%;
            border-radius: inherit;
            background: linear-gradient(90deg, #709b69 0%, #3f6f4d 100%);
        }

        .dark .dashboard-overview-card,
        .dark .dashboard-mini-card {
            border-color: rgba(130, 170, 123, 0.14);
            background: linear-gradient(180deg, #121813 0%, #101511 100%);
        }

        .dark .dashboard-overview-card__title,
        .dark .dashboard-ring__value,
        .dark .dashboard-mini-card__title,
        .dark .dashboard-activity-label,
        .dark .dashboard-goal-label,
        .dark .dashboard-overview-metric__value,
        .dark .dashboard-breakdown-inline__row {
            color: #eef4eb;
        }

        .dark .dashboard-overview-card__period,
        .dark .dashboard-ring__label,
        .dark .dashboard-mini-card__subtitle,
        .dark .dashboard-activity-meta {
            color: #9cad9a;
        }

        .dark .dashboard-ring {
            background: conic-gradient(#70a871 0 var(--progress-angle), #334033 var(--progress-angle) 360deg);
        }

        .dark .dashboard-ring::after {
            background: #121813;
        }

        .dark .dashboard-overview-metric__badge,
        .dark .dashboard-activity-icon {
            background: #1d2a1f;
            color: #b3d0ad;
        }

        .dark .dashboard-goal-icon.is-active {
            background: #2c6037;
            color: #f6fff2;
        }

        .dark .dashboard-goal-icon.is-inactive {
            background: #1b231c;
            color: #869283;
        }

        .dark .dashboard-mini-card__link {
            border-color: #2f3d30;
            background: #151d16;
            color: #b8d7b2;
        }

        .dark .dashboard-breakdown-inline__bar {
            background: #293229;
        }

        @media (min-width: 640px) {
            .dashboard-overview-card__body {
                grid-template-columns: auto minmax(0, 1fr);
            }

            .dashboard-mini-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
    </style>
@endonce

<section class="dashboard-side-grid" wire:poll.30s="refreshStats">
    <article class="dashboard-overview-card">
        <div class="dashboard-overview-card__inner">
            <div class="dashboard-overview-card__head">
                <h3 class="dashboard-overview-card__title">Overall Carbon Footprint</h3>
                <span class="dashboard-overview-card__period">{{ $monthlyLabel }}</span>
            </div>

            <div class="dashboard-overview-card__body">
                <div class="dashboard-ring" style="--progress-angle: {{ $progressAngle }}deg;">
                    <div class="dashboard-ring__content">
                        <div>
                            <div class="dashboard-ring__value">{{ number_format($totalFootprint, 1) }}</div>
                            <div class="dashboard-ring__label">kg CO2e</div>
                        </div>
                    </div>
                </div>

                <div class="dashboard-overview-metric">
                    <div class="dashboard-overview-metric__value">{{ $comparisonPercent }}%</div>

                    <div class="dashboard-overview-metric__badge" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h10v10" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="m7 17 10-10" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </article>

    <div class="dashboard-mini-grid">
        <article class="dashboard-mini-card">
            <h3 class="dashboard-mini-card__title">Recent Footprint Logs</h3>
            <p class="dashboard-mini-card__subtitle">{{ $insightTitle }}</p>

            <div class="dashboard-activity-list">
                @forelse ($recentHistory as $historyItem)
                    <div class="dashboard-activity-item">
                        <div class="dashboard-activity-icon">
                            {{ strtoupper(substr($historyItem['label'], 0, 1)) }}
                        </div>

                        <div class="min-w-0">
                            <div class="dashboard-activity-label">{{ $historyItem['label'] }}</div>
                            <div class="dashboard-activity-meta">{{ number_format($historyItem['value'], 2) }} kg logged</div>
                        </div>
                    </div>
                @empty
                    <div class="dashboard-activity-item">
                        <div class="dashboard-activity-icon">i</div>
                        <div class="min-w-0">
                            <div class="dashboard-activity-label">No recent logs yet</div>
                            <div class="dashboard-activity-meta">{{ $insightBody }}</div>
                        </div>
                    </div>
                @endforelse
            </div>
        </article>

        <article class="dashboard-mini-card">
            <h3 class="dashboard-mini-card__title">Daily Goals</h3>
            <p class="dashboard-mini-card__subtitle">Small targets based on your current dashboard totals.</p>

            <div class="dashboard-goal-list">
                @foreach ($goalItems as $goal)
                    <div class="dashboard-goal-item">
                        <div class="dashboard-goal-icon {{ $goal['checked'] ? 'is-active' : 'is-inactive' }}">
                            {{ $goal['checked'] ? '✓' : '•' }}
                        </div>

                        <div class="dashboard-goal-label">{{ $goal['label'] }}</div>
                    </div>
                @endforeach
            </div>

            <div class="dashboard-mini-card__footer">
                <a href="{{ route('carbon.history') }}" class="dashboard-mini-card__link" wire:navigate>See All</a>
            </div>
        </article>
    </div>

    <article class="dashboard-mini-card">
        <h3 class="dashboard-mini-card__title">Emission Breakdown</h3>
        <p class="dashboard-mini-card__subtitle">Your current footprint mix across the three tracked categories.</p>

        <div class="dashboard-breakdown-inline">
            @foreach ([
                ['label' => 'Transport', 'value' => $transportTotal],
                ['label' => 'Diet', 'value' => $dietTotal],
                ['label' => 'Gadgets', 'value' => $gadgetTotal],
            ] as $item)
                @php
                    $width = $totalFootprint > 0 ? max(8, min(100, ($item['value'] / $totalFootprint) * 100)) : 8;
                @endphp

                <div>
                    <div class="dashboard-breakdown-inline__row">
                        <span>{{ $item['label'] }}</span>
                        <span>{{ number_format($item['value'], 2) }} kg</span>
                    </div>

                    <div class="dashboard-breakdown-inline__bar">
                        <span class="dashboard-breakdown-inline__fill" style="width: {{ $width }}%;"></span>
                    </div>
                </div>
            @endforeach
        </div>
    </article>
</section>
