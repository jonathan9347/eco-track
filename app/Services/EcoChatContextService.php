<?php

namespace App\Services;

use Carbon\Carbon;
use Kreait\Firebase\Contract\Firestore;

class EcoChatContextService
{
    public function __construct(
        protected Firestore $firestore,
    ) {
    }

    public function buildForUser($user): string
    {
        $logs = $this->getUserLogs($user);

        $featureSummary = implode("\n", [
            'Eco Track product scope:',
            '- Dashboard: overview of emissions and trends.',
            '- Carbon Log / My Carbon Logs: records transport, diet, gadget usage, and total emissions.',
            '- AI Predictions: forecasts based on recorded carbon logs and shows recommendations tied to saved data.',
            '- Classroom Leaderboard: compares students and classrooms by emissions.',
            '- Achievements: progress and challenge tracking based on user activity.',
            '- Carbon calculation factors in this app: car 0.20 kg/km, bus 0.12 kg/km, jeepney 0.15 kg/km, tricycle 0.10 kg/km, walking 0.00 kg/km, gadget use 0.05 kg/hour, diet factors meat 5.0 kg, average 3.5 kg, vegetarian 2.0 kg, vegan 1.5 kg.',
        ]);

        if ($logs === []) {
            return $featureSummary."\n\nUser-specific activity summary:\n- This user has no saved carbon logs yet.";
        }

        $now = now();
        $recentLogs = array_values(array_filter(
            $logs,
            fn (array $log): bool => $log['date']->greaterThanOrEqualTo($now->copy()->subDays(13)->startOfDay())
        ));

        $thisWeekLogs = array_values(array_filter(
            $logs,
            fn (array $log): bool => $log['date']->greaterThanOrEqualTo($now->copy()->startOfWeek())
        ));

        $totals = [
            'transport' => round(array_sum(array_column($logs, 'transport_emission')), 2),
            'diet' => round(array_sum(array_column($logs, 'diet_emission')), 2),
            'gadget' => round(array_sum(array_column($logs, 'gadget_emission')), 2),
            'overall' => round(array_sum(array_column($logs, 'total_emission')), 2),
            'recent_14' => round(array_sum(array_column($recentLogs, 'total_emission')), 2),
            'this_week' => round(array_sum(array_column($thisWeekLogs, 'total_emission')), 2),
        ];

        $todayTotal = round(array_sum(array_map(
            fn (array $log): float => $log['date']->isToday() ? $log['total_emission'] : 0.0,
            $logs
        )), 2);

        $topCategory = collect([
            'transport' => $totals['transport'],
            'diet' => $totals['diet'],
            'gadget' => $totals['gadget'],
        ])->sortDesc()->keys()->first();

        $transportCounts = [];
        $dietCounts = [];

        foreach ($logs as $log) {
            $transportCounts[$log['transport_type']] = ($transportCounts[$log['transport_type']] ?? 0) + 1;
            $dietCounts[$log['diet_type']] = ($dietCounts[$log['diet_type']] ?? 0) + 1;
        }

        arsort($transportCounts);
        arsort($dietCounts);

        $topTransport = array_key_first($transportCounts) ?? 'unknown';
        $topDiet = array_key_first($dietCounts) ?? 'unknown';
        $latestLog = end($logs) ?: null;

        $latestEntries = array_slice(array_reverse($logs), 0, 5);
        $latestLines = array_map(function (array $log): string {
            return sprintf(
                '- %s: total %.2f kg CO2 (transport %.2f, diet %.2f, gadget %.2f), transport %s, distance %.1f km, diet %s, gadget %.1f h',
                $log['date']->format('Y-m-d'),
                $log['total_emission'],
                $log['transport_emission'],
                $log['diet_emission'],
                $log['gadget_emission'],
                $log['transport_type'],
                $log['distance'],
                $log['diet_type'],
                $log['gadget_hours']
            );
        }, $latestEntries);

        $userSummary = [
            'User-specific activity summary:',
            '- Total saved logs: '.count($logs),
            '- Total recorded emissions: '.number_format($totals['overall'], 2).' kg CO2',
            '- Emissions in the last 14 days: '.number_format($totals['recent_14'], 2).' kg CO2',
            '- Emissions this week: '.number_format($totals['this_week'], 2).' kg CO2',
            '- Emissions logged today: '.number_format($todayTotal, 2).' kg CO2',
            '- Highest lifetime category so far: '.$topCategory,
            '- Most frequent transport type: '.$topTransport,
            '- Most frequent diet type: '.$topDiet,
        ];

        if ($latestLog) {
            $userSummary[] = '- Latest log date: '.$latestLog['date']->format('Y-m-d');
            $userSummary[] = '- Latest log total: '.number_format($latestLog['total_emission'], 2).' kg CO2';
        }

        $userSummary[] = 'Recent saved logs:';

        return $featureSummary."\n\n".implode("\n", array_merge($userSummary, $latestLines));
    }

    protected function getUserLogs($user): array
    {
        $documents = $this->firestore
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
                'date' => $date,
                'transport_type' => (string) ($data['transport_type'] ?? 'unknown'),
                'distance' => (float) ($data['distance'] ?? 0),
                'diet_type' => (string) ($data['diet_type'] ?? 'unknown'),
                'gadget_hours' => (float) ($data['gadget_hours'] ?? 0),
                'transport_emission' => (float) ($data['transport_emission'] ?? 0),
                'diet_emission' => (float) ($data['diet_emission'] ?? 0),
                'gadget_emission' => (float) ($data['gadget_emission'] ?? 0),
                'total_emission' => (float) ($data['total_emission'] ?? 0),
            ];
        }

        usort($logs, fn (array $a, array $b) => $a['date']->lessThan($b['date']) ? -1 : 1);

        return $logs;
    }
}
