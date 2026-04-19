<?php

namespace App\Livewire;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Kreait\Firebase\Contract\Firestore;
use Livewire\Component;

class BadgesAndChallenges extends Component
{
    public array $badges = [];

    public function mount(): void
    {
        $this->refreshData();
    }

    public function refreshData(): void
    {
        if (! auth()->check()) {
            $this->badges = [];

            return;
        }

        $database = app(Firestore::class)->database();
        $user = auth()->user();
        $logs = $this->fetchUserLogs($database, $user->id);

        $this->badges = $this->buildBadges($logs);
    }

    public function render(): View
    {
        return view('components.badges-and-challenges')
            ->layout('layouts.app', ['title' => 'Achievements']);
    }

    protected function fetchUserLogs($database, int $userId): Collection
    {
        $documents = $database
            ->collection('carbon_logs')
            ->documents();

        $logs = [];

        foreach ($documents as $document) {
            if (! $document->exists()) {
                continue;
            }

            $data = $document->data();

            if ((string) ($data['user_id'] ?? '') !== (string) $userId) {
                continue;
            }

            $createdAt = $this->normalizeCreatedAt($data['created_at'] ?? null);

            $logs[] = [
                'id' => $document->id(),
                ...$data,
                'transport_type' => $this->normalizeTransportType($data['transport_type'] ?? null),
                'gadget_hours' => $this->normalizeNumber(
                    $data['gadget_hours']
                        ?? $data['gadget_usage']
                        ?? $data['device_hours']
                        ?? 0
                ),
                'total_emission' => $this->normalizeNumber($data['total_emission'] ?? 0),
                'created_at' => $createdAt,
                'log_date' => $createdAt?->format('Y-m-d'),
            ];
        }

        return collect($logs)
            ->sortBy(fn (array $log) => $log['created_at']?->timestamp ?? 0)
            ->values();
    }

    protected function buildBadges(Collection $logs): array
    {
        $logDates = $this->extractUniqueLogDates($logs);

        $walkingDays = $logs
            ->where('transport_type', 'walking')
            ->pluck('log_date')
            ->filter()
            ->unique()
            ->count();

        $energySaverDays = $logs
            ->filter(fn (array $log) => $log['gadget_hours'] < 2)
            ->pluck('log_date')
            ->filter()
            ->unique()
            ->count();

        $totalLogs = $logs->count();
        $streak = $this->calculateLongestStreak($logDates);

        return [
            $this->makeBadge(
                'Walking Hero',
                'Walk 5 different days to unlock this badge.',
                $walkingDays,
                5,
                'days',
                'Walk instead of using a vehicle on five separate days, then save each carbon log so the system can count your progress.',
                'M7 14c2.2 0 4-1.8 4-4S9.2 6 7 6 3 7.8 3 10s1.8 4 4 4Zm10 1c1.66 0 3 1.34 3 3v2h-2v-2a1 1 0 0 0-2 0v2h-2v-2c0-1.66 1.34-3 3-3ZM9 19l1.4-5.2 2.4 1.8V22h-2v-4.5L9.6 16 8 22H6l2.1-7.6A2 2 0 0 1 10 13h1.2l2.3 1.7'
            ),
            $this->makeBadge(
                'Energy Saver',
                'Keep gadget use below 2 hours for 7 days.',
                $energySaverDays,
                7,
                'days',
                'Log seven days where your gadget use stays under two hours. Shorter screen time entries will push this badge forward.',
                'M12 2 4 14h6l-1 8 8-12h-6l1-8Z'
            ),
            $this->makeBadge(
                'Carbon Tracker',
                'Record 20 carbon logs in total.',
                $totalLogs,
                20,
                'logs',
                'Keep adding your daily transport, diet, and gadget entries until you reach twenty saved carbon logs.',
                'M7 3h8l5 5v13a1 1 0 0 1-1 1H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2Zm7 1.5V9h4.5M9 13h6M9 17h6'
            ),
            $this->makeBadge(
                'Streak Master',
                'Log 30 consecutive days without missing a day.',
                $streak,
                30,
                'days',
                'Submit at least one carbon log every day for thirty straight days. Missing a day resets the streak count.',
                'M12 3 4 7v6c0 4.42 3.58 8 8 8s8-3.58 8-8V7l-8-4Zm0 5a3 3 0 1 1 0 6 3 3 0 0 1 0-6Z'
            ),
        ];
    }

    protected function extractUniqueLogDates(Collection $logs): array
    {
        return $logs->pluck('log_date')
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    protected function makeBadge(
        string $title,
        string $description,
        float|int $progress,
        float|int $target,
        string $suffix = 'days',
        string $instruction = '',
        string $iconPath = ''
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
            'instruction' => $instruction,
            'icon_path' => $iconPath,
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

    protected function normalizeCreatedAt(mixed $value): ?Carbon
    {
        if ($value instanceof Carbon) {
            return $value;
        }

        if ($value instanceof \DateTimeInterface) {
            return Carbon::instance($value);
        }

        if (is_string($value) && trim($value) !== '') {
            try {
                return Carbon::parse($value);
            } catch (\Throwable) {
                return null;
            }
        }

        return null;
    }

    protected function normalizeTransportType(mixed $value): string
    {
        $transport = strtolower(trim((string) $value));

        return match ($transport) {
            'walk', 'walking', 'on foot' => 'walking',
            default => $transport,
        };
    }

    protected function normalizeNumber(mixed $value): float
    {
        return is_numeric($value) ? (float) $value : 0.0;
    }
}
