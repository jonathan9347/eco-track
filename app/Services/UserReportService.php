<?php

namespace App\Services;

use Carbon\Carbon;
use Kreait\Firebase\Contract\Firestore;

class UserReportService
{
    public function __construct(
        protected Firestore $firestore,
    ) {
    }

    public function buildForUser($user): array
    {
        $logs = $this->getUserLogs($user);

        $summary = [
            'log_count' => count($logs),
            'total_emission' => round(array_sum(array_column($logs, 'total_emission')), 2),
            'transport_total' => round(array_sum(array_column($logs, 'transport_emission')), 2),
            'diet_total' => round(array_sum(array_column($logs, 'diet_emission')), 2),
            'gadget_total' => round(array_sum(array_column($logs, 'gadget_emission')), 2),
            'average_emission' => count($logs) > 0
                ? round(array_sum(array_column($logs, 'total_emission')) / count($logs), 2)
                : 0.0,
            'first_log_date' => $logs !== [] ? $logs[0]['created_at'] : null,
            'latest_log_date' => $logs !== [] ? $logs[array_key_last($logs)]['created_at'] : null,
        ];

        $monthly = [];

        foreach ($logs as $log) {
            $monthKey = Carbon::parse($log['created_at'])->format('Y-m');

            if (! isset($monthly[$monthKey])) {
                $monthly[$monthKey] = [
                    'month' => Carbon::parse($log['created_at'])->format('M Y'),
                    'log_count' => 0,
                    'total_emission' => 0.0,
                ];
            }

            $monthly[$monthKey]['log_count']++;
            $monthly[$monthKey]['total_emission'] += $log['total_emission'];
        }

        foreach ($monthly as &$month) {
            $month['total_emission'] = round($month['total_emission'], 2);
        }
        unset($month);

        return [
            'logs' => $logs,
            'summary' => $summary,
            'monthly' => array_values(array_reverse($monthly)),
        ];
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

            $createdAt = (string) ($data['created_at'] ?? '');

            if ($createdAt === '') {
                continue;
            }

            $logs[] = [
                'created_at' => $createdAt,
                'transport_type' => (string) ($data['transport_type'] ?? ''),
                'distance' => (float) ($data['distance'] ?? 0),
                'diet_type' => (string) ($data['diet_type'] ?? ''),
                'gadget_hours' => (float) ($data['gadget_hours'] ?? 0),
                'transport_emission' => (float) ($data['transport_emission'] ?? 0),
                'diet_emission' => (float) ($data['diet_emission'] ?? 0),
                'gadget_emission' => (float) ($data['gadget_emission'] ?? 0),
                'total_emission' => (float) ($data['total_emission'] ?? 0),
            ];
        }

        usort($logs, fn (array $a, array $b) => strcmp($a['created_at'], $b['created_at']));

        return $logs;
    }
}
