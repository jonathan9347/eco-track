<?php

namespace App\Livewire;

use App\Services\PredictionInsightsService;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class AIPredictions extends Component
{
    public ?array $prediction = null;

    public ?array $recommendations = [];

    public ?array $sparkline = null;

    public float $todayEmission = 0.0;

    public bool $loading = true;

    public ?string $error = null;

    public bool $noData = false;

    protected $listeners = ['refreshPredictions'];

    public function mount(): void
    {
        $this->fetchPredictions();
    }

    public function fetchPredictions(): void
    {
        $this->loading = true;
        $this->error = null;
        $this->noData = false;

        try {
            $user = auth()->user();

            if (! $user) {
                $this->loading = false;

                return;
            }

            $data = app(PredictionInsightsService::class)->buildForUser($user);

            if (! ($data['ready'] ?? false)) {
                $this->prediction = null;
                $this->recommendations = [];
                $this->sparkline = null;
                $this->todayEmission = 0.0;
                $this->noData = true;
            } else {
                $this->prediction = $data['prediction'] ?? null;
                $this->recommendations = $data['recommendations'] ?? [];
                $this->sparkline = $data['sparkline'] ?? null;
                $this->todayEmission = (float) ($data['today_emission'] ?? 0.0);
            }
        } catch (\Throwable) {
            $this->prediction = null;
            $this->recommendations = [];
            $this->sparkline = null;
            $this->todayEmission = 0.0;
            $this->error = 'Unable to load prediction insights from your saved logs right now.';
        }

        $this->loading = false;
    }

    public function refreshPredictions(): void
    {
        $this->fetchPredictions();
    }

    public function trendIcon(): string
    {
        if (! $this->prediction) {
            return '-';
        }

        return match ($this->prediction['trend_direction']) {
            'up' => '^',
            'down' => 'v',
            default => '-',
        };
    }

    public function trendColor(): string
    {
        if (! $this->prediction) {
            return 'text-slate-500';
        }

        return match ($this->prediction['trend_direction']) {
            'up' => 'text-red-500',
            'down' => 'text-emerald-500',
            default => 'text-slate-500',
        };
    }

    public function difficultyColor(string $difficulty): string
    {
        return match (strtolower($difficulty)) {
            'easy' => 'bg-emerald-100 text-emerald-800',
            'medium' => 'bg-amber-100 text-amber-800',
            'hard' => 'bg-red-100 text-red-800',
            default => 'bg-slate-100 text-slate-800',
        };
    }

    public function confidenceColor(): string
    {
        if (! $this->prediction) {
            return 'bg-slate-200';
        }

        $confidence = $this->prediction['confidence'];

        if ($confidence >= 80) {
            return 'bg-emerald-500';
        }

        if ($confidence >= 60) {
            return 'bg-amber-500';
        }

        return 'bg-red-500';
    }

    public function render(): View
    {
        return view('livewire.ai-predictions');
    }
}
