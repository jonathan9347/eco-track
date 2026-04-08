<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Kreait\Firebase\Contract\Firestore;
use Livewire\Component;

class BadgesAndChallenges extends Component
{
    public array $badges = [];

    public ?array $activeChallenge = null;

    public array $challengeLeaderboard = [];

    public int $userPoints = 0;

    public ?string $challengeMessage = null;

    public function mount(): void
    {
        $this->refreshData();
    }

    public function completeChallenge(): void
    {
        if (! auth()->check() || ! $this->activeChallenge || ($this->activeChallenge['completed'] ?? false)) {
            return;
        }

        $challengeId = $this->activeChallenge['id'];
        $database = app(Firestore::class)->database();
        $user = auth()->user();

        $database
            ->collection('challenge_completions')
            ->document($challengeId.'_'.$user->id)
            ->set([
                'challenge_id' => $challengeId,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'classroom' => $user->classroom,
                'points' => 100,
                'completed_at' => now()->toISOString(),
            ]);

        $this->challengeMessage = 'Challenge completed! You earned 100 points.';
        $this->refreshData();
    }

    public function refreshData(): void
    {
        if (! auth()->check()) {
            $this->badges = [];
            $this->activeChallenge = null;
            $this->challengeLeaderboard = [];
            $this->userPoints = 0;

            return;
        }

        $database = app(Firestore::class)->database();
        $user = auth()->user();
        $logs = $this->fetchUserLogs($database, $user->id);

        $this->badges = $this->buildBadges($logs);
        $this->activeChallenge = $this->buildActiveChallenge($database, $logs, $user->id);

        [$leaderboard, $points] = $this->buildChallengeLeaderboard($database, $user->id);

        $this->challengeLeaderboard = $leaderboard;
        $this->userPoints = $points;
    }

    public function render(): View
    {
        return view('components.badges-and-challenges');
    }

    protected function fetchUserLogs($database, int $userId): Collection
    {
        $documents = $database
            ->collection('carbon_logs')
            ->where('user_id', '=', $userId)
            ->documents();

        $logs = [];

        foreach ($documents as $document) {
            if (! $document->exists()) {
                continue;
            }

            $logs[] = [
                'id' => $document->id(),
                ...$document->data(),
            ];
        }

        return collect($logs)
            ->map(function (array $log): array {
                $log['distance'] = (float) ($log['distance'] ?? 0);
                $log['gadget_hours'] = (float) ($log['gadget_hours'] ?? 0);
                $log['total_emission'] = (float) ($log['total_emission'] ?? 0);
                $log['created_at'] = $log['created_at'] ?? null;

                return $log;
            })
            ->sortBy('created_at')
            ->values();
    }

    protected function buildBadges(Collection $logs): array
    {
        $walkingDays = $logs
            ->where('transport_type', 'walking')
            ->pluck('created_at')
            ->filter()
            ->map(fn ($date) => substr((string) $date, 0, 10))
            ->unique()
            ->count();

        $veganMeals = $logs->where('diet_type', 'vegan')->count();

        $energySaverDays = $logs
            ->filter(fn (array $log) => $log['gadget_hours'] < 2)
            ->pluck('created_at')
            ->filter()
            ->map(fn ($date) => substr((string) $date, 0, 10))
            ->unique()
            ->count();

        $greenSavings = $logs->reduce(function (float $carry, array $log): float {
            // Assumes 5 kg CO2 as a simple daily baseline to estimate savings.
            return $carry + max(0, 5 - $log['total_emission']);
        }, 0);

        $streak = $this->calculateLongestStreak(
            $logs->pluck('created_at')
                ->filter()
                ->map(fn ($date) => substr((string) $date, 0, 10))
                ->unique()
                ->values()
                ->all()
        );

        return [
            $this->makeBadge('Walking Hero', 'Walk 5 days', $walkingDays, 5),
            $this->makeBadge('Vegan Champion', 'Log 10 vegan meals', $veganMeals, 10),
            $this->makeBadge('Energy Saver', 'Stay below 2 gadget hours for 7 days', $energySaverDays, 7),
            $this->makeBadge('Green Warrior', 'Save 100 total CO2', $greenSavings, 100, 'kg saved'),
            $this->makeBadge('Streak Master', 'Log 30 consecutive days', $streak, 30),
        ];
    }

    protected function buildActiveChallenge($database, Collection $logs, int $userId): ?array
    {
        $documents = $database
            ->collection('weekly_challenges')
            ->where('is_active', '=', true)
            ->documents();

        $activeChallenge = null;

        foreach ($documents as $document) {
            if (! $document->exists()) {
                continue;
            }

            $activeChallenge = [
                'id' => $document->id(),
                ...$document->data(),
            ];
            break;
        }

        if (! $activeChallenge) {
            return [
                'id' => null,
                'title' => 'No active weekly challenge yet',
                'description' => 'Add a document in the weekly_challenges collection with is_active = true to launch one.',
                'target' => 1,
                'progress' => 0,
                'percentage' => 0,
                'points' => 100,
                'completed' => false,
            ];
        }

        $target = (float) ($activeChallenge['target'] ?? $activeChallenge['target_value'] ?? 1);
        $metric = (string) ($activeChallenge['metric'] ?? 'log_days');
        $progress = $this->challengeProgress($metric, $activeChallenge, $logs);
        $percentage = $target > 0 ? min(100, (int) round(($progress / $target) * 100)) : 0;

        $completion = $database
            ->collection('challenge_completions')
            ->document($activeChallenge['id'].'_'.$userId)
            ->snapshot();

        return [
            'id' => $activeChallenge['id'],
            'title' => $activeChallenge['title'] ?? 'Weekly Challenge',
            'description' => $activeChallenge['description'] ?? 'Complete this week\'s sustainability goal.',
            'target' => $target,
            'progress' => $progress,
            'percentage' => $percentage,
            'points' => 100,
            'completed' => $completion->exists(),
        ];
    }

    protected function buildChallengeLeaderboard($database, int $userId): array
    {
        $documents = $database
            ->collection('challenge_completions')
            ->documents();

        $scores = [];

        foreach ($documents as $document) {
            if (! $document->exists()) {
                continue;
            }

            $data = $document->data();
            $key = (string) ($data['user_id'] ?? 'unknown');

            if (! isset($scores[$key])) {
                $scores[$key] = [
                    'user_id' => $data['user_id'] ?? null,
                    'user_name' => $data['user_name'] ?? 'Unknown student',
                    'classroom' => $data['classroom'] ?? 'Unassigned',
                    'points' => 0,
                    'completed_challenges' => 0,
                ];
            }

            $scores[$key]['points'] += (int) ($data['points'] ?? 0);
            $scores[$key]['completed_challenges']++;
        }

        $leaderboard = collect($scores)
            ->sortByDesc('points')
            ->values()
            ->map(function (array $entry, int $index): array {
                $entry['rank'] = $index + 1;

                return $entry;
            })
            ->all();

        $currentUserPoints = collect($leaderboard)
            ->firstWhere('user_id', $userId)['points'] ?? 0;

        return [$leaderboard, $currentUserPoints];
    }

    protected function challengeProgress(string $metric, array $challenge, Collection $logs): float|int
    {
        return match ($metric) {
            'walking_days' => $logs
                ->where('transport_type', 'walking')
                ->pluck('created_at')
                ->filter()
                ->map(fn ($date) => substr((string) $date, 0, 10))
                ->unique()
                ->count(),
            'vegan_meals' => $logs->where('diet_type', 'vegan')->count(),
            'energy_saver_days' => $logs
                ->filter(fn (array $log) => $log['gadget_hours'] < ((float) ($challenge['max_gadget_hours'] ?? 2)))
                ->pluck('created_at')
                ->filter()
                ->map(fn ($date) => substr((string) $date, 0, 10))
                ->unique()
                ->count(),
            'co2_saved' => $logs->reduce(fn (float $carry, array $log): float => $carry + max(0, 5 - $log['total_emission']), 0),
            'streak_days' => $this->calculateLongestStreak(
                $logs->pluck('created_at')
                    ->filter()
                    ->map(fn ($date) => substr((string) $date, 0, 10))
                    ->unique()
                    ->values()
                    ->all()
            ),
            default => $logs
                ->pluck('created_at')
                ->filter()
                ->map(fn ($date) => substr((string) $date, 0, 10))
                ->unique()
                ->count(),
        };
    }

    protected function makeBadge(
        string $title,
        string $description,
        float|int $progress,
        float|int $target,
        string $suffix = 'days'
    ): array {
        $progress = round((float) $progress, 2);
        $target = round((float) $target, 2);
        $percentage = $target > 0 ? min(100, (int) round(($progress / $target) * 100)) : 0;

        return [
            'title' => $title,
            'description' => $description,
            'progress' => $progress,
            'target' => $target,
            'percentage' => $percentage,
            'earned' => $progress >= $target,
            'suffix' => $suffix,
        ];
    }

    protected function calculateLongestStreak(array $dates): int
    {
        if (count($dates) === 0) {
            return 0;
        }

        sort($dates);

        $longest = 1;
        $current = 1;

        for ($index = 1; $index < count($dates); $index++) {
            $previous = strtotime($dates[$index - 1]);
            $currentDate = strtotime($dates[$index]);
            $difference = (int) (($currentDate - $previous) / 86400);

            if ($difference === 1) {
                $current++;
                $longest = max($longest, $current);
            } elseif ($difference > 1) {
                $current = 1;
            }
        }

        return $longest;
    }
}
