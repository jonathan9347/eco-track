<?php

namespace App\Livewire;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Kreait\Firebase\Contract\Firestore;

class DashboardStats extends Component
{
    public float $transportTotal = 0.0;

    public float $dietTotal = 0.0;

    public float $gadgetTotal = 0.0;

    public float $thisWeekEmission = 0.0;

    public float $lastWeekEmission = 0.0;

    public string $insightTitle = 'Eco-Insight';

    public string $insightBody = 'Your recent activity will appear here once more logs are saved.';

    public array $trendLabels = [];

    public array $trendValues = [];

    public array $trendSeries = [];

    public function mount(): void
    {
        $this->refreshStats();
    }

    public function refreshStats(): void
    {
        $user = auth()->user();

        if (! $user) {
            $this->resetStats();

            return;
        }

        $documents = app(Firestore::class)
            ->database()
            ->collection('carbon_logs')
            ->documents();

        $logs = [];

        foreach ($documents as $document) {
            if (! $document->exists()) {
                continue;
            }

            $data = $document->data();

            if ((string) ($data['user_id'] ?? '') !== (string) $user->id) {
                continue;
            }

            $createdAt = $data['created_at'] ?? null;

            if (! $createdAt) {
                continue;
            }

            try {
                $date = Carbon::parse((string) $createdAt);
            } catch (\Throwable) {
                continue;
            }

            $logs[] = [
                'transport_emission' => (float) ($data['transport_emission'] ?? 0),
                'diet_emission' => (float) ($data['diet_emission'] ?? 0),
                'gadget_emission' => (float) ($data['gadget_emission'] ?? 0),
                'total_emission' => (float) ($data['total_emission'] ?? 0),
                'date' => $date,
            ];
        }

        if ($logs === []) {
            $this->resetStats();

            return;
        }

        usort($logs, fn (array $a, array $b) => $a['date']->lessThan($b['date']) ? -1 : 1);

        $this->transportTotal = round(array_sum(array_column($logs, 'transport_emission')), 2);
        $this->dietTotal = round(array_sum(array_column($logs, 'diet_emission')), 2);
        $this->gadgetTotal = round(array_sum(array_column($logs, 'gadget_emission')), 2);

        $now = now();
        $startOfThisWeek = $now->copy()->startOfWeek();
        $startOfLastWeek = $startOfThisWeek->copy()->subWeek();
        $endOfLastWeek = $startOfThisWeek->copy()->subSecond();

        $this->thisWeekEmission = round(collect($logs)
            ->filter(fn (array $log) => $log['date']->greaterThanOrEqualTo($startOfThisWeek))
            ->sum('total_emission'), 2);

        $this->lastWeekEmission = round(collect($logs)
            ->filter(fn (array $log) => $log['date']->between($startOfLastWeek, $endOfLastWeek))
            ->sum('total_emission'), 2);

        if ($this->lastWeekEmission <= 0 && $this->thisWeekEmission > 0) {
            $this->insightTitle = 'Your footprint is higher than last week.';
            $this->insightBody = 'You do not have enough data from last week yet, but your current week logs are now being tracked for comparison.';
        } elseif ($this->thisWeekEmission < $this->lastWeekEmission) {
            $this->insightTitle = 'Nice work, you are improving.';
            $this->insightBody = 'Your emissions this week are lower than last week. Keep going with the greener habits that are working.';
        } elseif ($this->thisWeekEmission > $this->lastWeekEmission) {
            $this->insightTitle = 'Your footprint is higher this week.';
            $this->insightBody = 'Your emissions increased compared with last week. A few lower-carbon choices can help bring it back down.';
        } else {
            $this->insightTitle = 'Your footprint is steady.';
            $this->insightBody = 'This week and last week are very similar so far. Small changes can still make a visible difference.';
        }

        $dailyTotals = collect($logs)
            ->filter(fn (array $log) => $log['date']->greaterThanOrEqualTo($now->copy()->subDays(29)->startOfDay()))
            ->groupBy(fn (array $log) => $log['date']->format('Y-m-d'))
            ->map(fn ($items) => round(collect($items)->sum('total_emission'), 2));

        $labels = [];
        $values = [];
        $series = [];

        for ($i = 29; $i >= 0; $i--) {
            $day = $now->copy()->subDays($i);
            $key = $day->format('Y-m-d');
            $value = (float) ($dailyTotals[$key] ?? 0);

            $labels[] = $day->format('M j');
            $values[] = $value;
            $series[] = [
                'date' => $key,
                'label' => $day->format('M j'),
                'emission' => $value,
            ];
        }

        $this->trendLabels = $labels;
        $this->trendValues = $values;
        $this->trendSeries = $series;
    }

    public function render(): View
    {
        return view('components.dashboard-stats');
    }

    protected function resetStats(): void
    {
        $this->transportTotal = 0.0;
        $this->dietTotal = 0.0;
        $this->gadgetTotal = 0.0;
        $this->thisWeekEmission = 0.0;
        $this->lastWeekEmission = 0.0;
        $this->insightTitle = 'Eco-Insight';
        $this->insightBody = 'Your recent activity will appear here once more logs are saved.';
        $this->trendLabels = [];
        $this->trendValues = [];
        $this->trendSeries = [];
    }
}
