<?php

namespace App\Services;

use Carbon\Carbon;
use Kreait\Firebase\Contract\Firestore;

class PredictionInsightsService
{
    protected const TRANSPORT_FACTORS = [
        'jeepney' => 0.15,
        'bus' => 0.12,
        'tricycle' => 0.10,
        'car' => 0.20,
        'walking' => 0.00,
    ];

    protected const DIET_FACTORS = [
        'meat' => 5.0,
        'average' => 3.5,
        'vegetarian' => 2.0,
        'plant_based' => 1.5,
    ];

    public function __construct(
        protected Firestore $firestore,
    ) {
    }

    public function buildForUser($user): array
    {
        $logs = $this->getUserLogs($user);
        $dailyTotals = $this->calculateDailyTotals($logs, 30);
        $activeDays = count(array_filter($dailyTotals, fn (array $day): bool => $day['total'] > 0));

        if ($activeDays < 3) {
            return [
                'ready' => false,
                'message' => 'Not enough data to make predictions. Please log at least 3 days of carbon data.',
            ];
        }

        $dailyBreakdown = $this->calculateDailyBreakdown($logs, 7);
        $prediction = $this->generatePrediction($dailyTotals);

        return [
            'ready' => true,
            'prediction' => $prediction,
            'recommendations' => $this->generateRecommendations($dailyBreakdown, $dailyTotals),
            'sparkline' => $this->generateSparklineData($dailyTotals, $prediction),
            'today_emission' => $this->getTodayEmission($dailyTotals),
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
                'transport_type' => (string) ($data['transport_type'] ?? 'unknown'),
                'diet_type' => (string) ($data['diet_type'] ?? 'unknown'),
                'gadget_hours' => (float) ($data['gadget_hours'] ?? 0),
                'distance' => (float) ($data['distance'] ?? 0),
                'date' => $date,
            ];
        }

        usort($logs, fn (array $a, array $b) => $a['date']->lessThan($b['date']) ? -1 : 1);

        return $logs;
    }

    protected function calculateDailyTotals(array $logs, int $days): array
    {
        $now = now();
        $startDate = $now->copy()->subDays($days - 1)->startOfDay();

        $dailyTotals = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $day = $now->copy()->subDays($i)->startOfDay();
            $key = $day->format('Y-m-d');
            $dailyTotals[$key] = [
                'date' => $key,
                'label' => $day->format('M j'),
                'total' => 0.0,
                'transport' => 0.0,
                'diet' => 0.0,
                'gadget' => 0.0,
            ];
        }

        foreach ($logs as $log) {
            if ($log['date']->lessThan($startDate)) {
                continue;
            }

            $key = $log['date']->format('Y-m-d');
            if (! isset($dailyTotals[$key])) {
                continue;
            }

            $dailyTotals[$key]['total'] += $log['total_emission'];
            $dailyTotals[$key]['transport'] += $log['transport_emission'];
            $dailyTotals[$key]['diet'] += $log['diet_emission'];
            $dailyTotals[$key]['gadget'] += $log['gadget_emission'];
        }

        foreach ($dailyTotals as &$day) {
            $day['total'] = round($day['total'], 2);
            $day['transport'] = round($day['transport'], 2);
            $day['diet'] = round($day['diet'], 2);
            $day['gadget'] = round($day['gadget'], 2);
        }
        unset($day);

        return array_values($dailyTotals);
    }

    protected function calculateDailyBreakdown(array $logs, int $days): array
    {
        $now = now();
        $startDate = $now->copy()->subDays($days - 1)->startOfDay();

        $breakdown = [
            'active_days' => 0,
            'transport' => ['total' => 0.0, 'types' => []],
            'diet' => ['total' => 0.0, 'types' => []],
            'gadget' => ['total' => 0.0, 'hours' => 0.0],
        ];

        $activeDayMap = [];

        foreach ($logs as $log) {
            if ($log['date']->lessThan($startDate)) {
                continue;
            }

            $activeDayMap[$log['date']->format('Y-m-d')] = true;

            $breakdown['transport']['total'] += $log['transport_emission'];
            $breakdown['diet']['total'] += $log['diet_emission'];
            $breakdown['gadget']['total'] += $log['gadget_emission'];
            $breakdown['gadget']['hours'] += $log['gadget_hours'];

            $transportType = $log['transport_type'];
            if (! isset($breakdown['transport']['types'][$transportType])) {
                $breakdown['transport']['types'][$transportType] = [
                    'count' => 0,
                    'total_emission' => 0.0,
                    'total_distance' => 0.0,
                ];
            }

            $breakdown['transport']['types'][$transportType]['count']++;
            $breakdown['transport']['types'][$transportType]['total_emission'] += $log['transport_emission'];
            $breakdown['transport']['types'][$transportType]['total_distance'] += $log['distance'];

            $dietType = $log['diet_type'];
            if (! isset($breakdown['diet']['types'][$dietType])) {
                $breakdown['diet']['types'][$dietType] = [
                    'count' => 0,
                    'total_emission' => 0.0,
                ];
            }

            $breakdown['diet']['types'][$dietType]['count']++;
            $breakdown['diet']['types'][$dietType]['total_emission'] += $log['diet_emission'];
        }

        $breakdown['active_days'] = count($activeDayMap);
        $breakdown['transport']['total'] = round($breakdown['transport']['total'], 2);
        $breakdown['diet']['total'] = round($breakdown['diet']['total'], 2);
        $breakdown['gadget']['total'] = round($breakdown['gadget']['total'], 2);
        $breakdown['gadget']['hours'] = round($breakdown['gadget']['hours'], 2);

        foreach ($breakdown['transport']['types'] as &$type) {
            $type['total_emission'] = round($type['total_emission'], 2);
            $type['total_distance'] = round($type['total_distance'], 2);
        }
        unset($type);

        foreach ($breakdown['diet']['types'] as &$type) {
            $type['total_emission'] = round($type['total_emission'], 2);
        }
        unset($type);

        return $breakdown;
    }

    protected function generatePrediction(array $dailyTotals): array
    {
        $activeDays = array_values(array_filter($dailyTotals, fn (array $day): bool => $day['total'] > 0));
        $values = array_column($activeDays, 'total');

        $recentValues = array_slice($values, -3);
        $recentAverage = array_sum($recentValues) / count($recentValues);

        $sevenDayValues = array_slice($values, -7);
        $sevenDayAverage = array_sum($sevenDayValues) / count($sevenDayValues);

        $previousValues = array_slice($values, -6, 3);
        $previousAverage = count($previousValues) > 0
            ? array_sum($previousValues) / count($previousValues)
            : $sevenDayAverage;

        $trendPercent = $previousAverage > 0
            ? (($recentAverage - $previousAverage) / $previousAverage) * 100
            : 0.0;

        $trendFactor = ($recentAverage - $sevenDayAverage) * 0.1;
        $predictedEmission = round(max(0, $recentAverage + $trendFactor), 2);
        $confidence = $this->calculateConfidence($values);

        $trendDirection = 'neutral';
        if ($trendPercent > 3) {
            $trendDirection = 'up';
        } elseif ($trendPercent < -3) {
            $trendDirection = 'down';
        }

        $latestActual = end($values);

        return [
            'predicted_emission' => $predictedEmission,
            'trend' => round($trendPercent, 1),
            'trend_direction' => $trendDirection,
            'confidence' => $confidence,
            'confidence_label' => $confidence >= 80 ? 'High' : ($confidence >= 60 ? 'Medium' : 'Low'),
            'based_on_days' => count($values),
            'recent_average' => round($recentAverage, 2),
            'seven_day_average' => round($sevenDayAverage, 2),
            'latest_actual' => round((float) $latestActual, 2),
            'change_from_latest' => round($predictedEmission - (float) $latestActual, 2),
        ];
    }

    protected function calculateConfidence(array $values): int
    {
        $n = count($values);
        if ($n < 3) {
            return 30;
        }

        $mean = array_sum($values) / $n;
        if ($mean == 0.0) {
            return 0;
        }

        $variance = 0.0;
        foreach ($values as $value) {
            $variance += ($value - $mean) ** 2;
        }

        $variance /= $n;
        $stdDev = sqrt($variance);
        $coefficientOfVariation = ($stdDev / $mean) * 100;
        $confidence = max(0, min(100, 100 - $coefficientOfVariation));
        $dataBonus = min(20, ($n - 3) * 2);

        return (int) round(min(100, $confidence + $dataBonus));
    }

    protected function generateRecommendations(array $breakdown, array $dailyTotals): array
    {
        $recommendations = [];

        if ($breakdown['transport']['total'] > 0) {
            $transportRecommendation = $this->getTransportRecommendation($breakdown['transport']);
            if ($transportRecommendation) {
                $recommendations[] = $transportRecommendation;
            }
        }

        if ($breakdown['diet']['total'] > 0) {
            $dietRecommendation = $this->getDietRecommendation($breakdown['diet']);
            if ($dietRecommendation) {
                $recommendations[] = $dietRecommendation;
            }
        }

        if ($breakdown['gadget']['total'] > 0) {
            $gadgetRecommendation = $this->getGadgetRecommendation($breakdown['gadget'], $breakdown['active_days']);
            if ($gadgetRecommendation) {
                $recommendations[] = $gadgetRecommendation;
            }
        }

        $generalRecommendation = $this->getGeneralRecommendation($dailyTotals);
        if ($generalRecommendation) {
            $recommendations[] = $generalRecommendation;
        }

        usort($recommendations, fn (array $a, array $b) => $b['potential_savings'] <=> $a['potential_savings']);

        return array_slice($recommendations, 0, 3);
    }

    protected function getTransportRecommendation(array $transportData): ?array
    {
        if ($transportData['types'] === []) {
            return null;
        }

        $type = $this->getTopTypeByEmission($transportData['types']);
        if (! $type) {
            return null;
        }

        $name = $type['name'];
        $stats = $type['stats'];
        $distance = $stats['total_distance'];
        $count = $stats['count'];
        $emission = $stats['total_emission'];

        return match ($name) {
            'car' => [
                'action' => 'Replace part of your car distance with bus travel.',
                'detail' => 'In your last 7 days, car logs covered '.number_format($distance, 1).' km across '.$count.' entries and produced '.number_format($emission, 2).' kg CO2. Moving half of that distance to bus would save about '.number_format(($distance * 0.5) * (self::TRANSPORT_FACTORS['car'] - self::TRANSPORT_FACTORS['bus']), 2).' kg CO2.',
                'potential_savings' => round(($distance * 0.5) * (self::TRANSPORT_FACTORS['car'] - self::TRANSPORT_FACTORS['bus']), 2),
                'difficulty' => 'Medium',
                'category' => 'transport',
            ],
            'jeepney', 'bus', 'tricycle' => $this->buildSharedTransportRecommendation($name, $stats),
            'walking' => [
                'action' => 'Keep your walking trips consistent.',
                'detail' => 'Walking was your top transport pattern in the last 7 days, so transport emissions are already low in your recorded data.',
                'potential_savings' => 0.0,
                'difficulty' => 'Easy',
                'category' => 'transport',
            ],
            default => [
                'action' => 'Reduce total travel distance where possible.',
                'detail' => 'Your recorded transport logs produced '.number_format($transportData['total'], 2).' kg CO2 in the last 7 days, so shorter combined trips would create the clearest savings.',
                'potential_savings' => round($transportData['total'] * 0.15, 2),
                'difficulty' => 'Easy',
                'category' => 'transport',
            ],
        };
    }

    protected function buildSharedTransportRecommendation(string $name, array $stats): array
    {
        $distance = $stats['total_distance'];
        $count = $stats['count'];
        $emission = $stats['total_emission'];
        $averageDistance = $count > 0 ? $distance / $count : 0.0;

        if ($averageDistance <= 2.0) {
            $savedDistance = $distance * 0.5;
            $potentialSavings = round($savedDistance * self::TRANSPORT_FACTORS[$name], 2);

            return [
                'action' => 'Walk some of your shortest '.$this->labelForTransport($name).' trips.',
                'detail' => 'Your '.$this->labelForTransport($name).' logs averaged '.number_format($averageDistance, 1).' km over '.$count.' entries in the last 7 days. Walking half of those short trips would save about '.number_format($potentialSavings, 2).' kg CO2 from the recorded '.number_format($emission, 2).' kg CO2.',
                'potential_savings' => $potentialSavings,
                'difficulty' => 'Easy',
                'category' => 'transport',
            ];
        }

        $potentialSavings = round($emission * 0.15, 2);

        return [
            'action' => 'Combine '.$this->labelForTransport($name).' trips to cut distance.',
            'detail' => 'You logged '.$count.' '.$this->labelForTransport($name).' entries totaling '.number_format($distance, 1).' km and '.number_format($emission, 2).' kg CO2 in the last 7 days. Cutting that distance by 15% would save about '.number_format($potentialSavings, 2).' kg CO2.',
            'potential_savings' => $potentialSavings,
            'difficulty' => 'Easy',
            'category' => 'transport',
        ];
    }

    protected function getDietRecommendation(array $dietData): ?array
    {
        if ($dietData['types'] === []) {
            return null;
        }

        $type = $this->getTopTypeByEmission($dietData['types']);
        if (! $type) {
            return null;
        }

        $name = $type['name'];
        $stats = $type['stats'];
        $currentFactor = self::DIET_FACTORS[$name] ?? null;

        if ($currentFactor === null) {
            return null;
        }

        $target = match ($name) {
            'meat' => 'vegetarian',
            'average' => 'vegetarian',
            'vegetarian' => 'plant_based',
            'plant_based' => null,
            default => null,
        };

        if ($target === null) {
            return [
                'action' => 'Keep your lower-carbon meal choices consistent.',
                'detail' => 'Your recorded diet logs are already centered on '.$this->labelForDiet($name).' meals, which is the lowest-emission diet pattern in this app.',
                'potential_savings' => 0.0,
                'difficulty' => 'Easy',
                'category' => 'diet',
            ];
        }

        $targetFactor = self::DIET_FACTORS[$target];
        $potentialSavings = round(max(0, $currentFactor - $targetFactor), 2);

        return [
            'action' => 'Swap one '.$this->labelForDiet($name).' meal for a '.$this->labelForDiet($target).' option.',
            'detail' => 'In the last 7 days, '.$this->labelForDiet($name).' meals appeared '.$stats['count'].' times and contributed '.number_format($stats['total_emission'], 2).' kg CO2. Replacing one of those meals with '.$this->labelForDiet($target).' would save about '.number_format($potentialSavings, 2).' kg CO2 based on your recorded factors.',
            'potential_savings' => $potentialSavings,
            'difficulty' => $name === 'meat' ? 'Medium' : 'Easy',
            'category' => 'diet',
        ];
    }

    protected function getGadgetRecommendation(array $gadgetData, int $activeDays): ?array
    {
        if ($activeDays < 1 || $gadgetData['hours'] <= 0) {
            return null;
        }

        $averageHours = $gadgetData['hours'] / $activeDays;
        $reductionHours = $averageHours >= 6 ? 1.5 : 1.0;
        $potentialSavings = round($reductionHours * $activeDays * 0.05, 2);

        return [
            'action' => 'Trim your gadget use by '.rtrim(rtrim(number_format($reductionHours, 1), '0'), '.').' hour'.($reductionHours > 1 ? 's' : '').' per logged day.',
            'detail' => 'Your last 7 days show '.number_format($gadgetData['hours'], 1).' total gadget hours across '.$activeDays.' active day'.($activeDays === 1 ? '' : 's').', averaging '.number_format($averageHours, 1).' hours a day and '.number_format($gadgetData['total'], 2).' kg CO2. That reduction would save about '.number_format($potentialSavings, 2).' kg CO2.',
            'potential_savings' => $potentialSavings,
            'difficulty' => $averageHours >= 6 ? 'Medium' : 'Easy',
            'category' => 'gadget',
        ];
    }

    protected function getGeneralRecommendation(array $dailyTotals): ?array
    {
        $activeDays = array_values(array_filter($dailyTotals, fn (array $day): bool => $day['total'] > 0));
        if ($activeDays === []) {
            return null;
        }

        $bestDay = array_reduce($activeDays, function (?array $carry, array $day): array {
            if ($carry === null || $day['total'] < $carry['total']) {
                return $day;
            }

            return $carry;
        });

        $averageEmission = array_sum(array_column($activeDays, 'total')) / count($activeDays);
        $potentialSavings = round(max(0, $averageEmission - $bestDay['total']), 2);

        if ($potentialSavings <= 0) {
            return null;
        }

        return [
            'action' => 'Use your lowest-emission day as tomorrow\'s target.',
            'detail' => 'Your average logged day is '.number_format($averageEmission, 2).' kg CO2, while '.$bestDay['label'].' came in at '.number_format($bestDay['total'], 2).' kg CO2. Matching that recorded day again would save about '.number_format($potentialSavings, 2).' kg CO2.',
            'potential_savings' => $potentialSavings,
            'difficulty' => 'Medium',
            'category' => 'general',
        ];
    }

    protected function generateSparklineData(array $dailyTotals, array $prediction): array
    {
        $last7Days = array_slice($dailyTotals, -7);
        $labels = array_column($last7Days, 'label');
        $values = array_column($last7Days, 'total');

        $labels[] = 'Tomorrow';
        $values[] = $prediction['predicted_emission'];

        return [
            'labels' => $labels,
            'values' => $values,
            'is_prediction' => array_merge(array_fill(0, count($last7Days), false), [true]),
        ];
    }

    protected function getTodayEmission(array $dailyTotals): float
    {
        $today = now()->format('Y-m-d');

        foreach ($dailyTotals as $day) {
            if ($day['date'] === $today) {
                return $day['total'];
            }
        }

        return 0.0;
    }

    protected function getTopTypeByEmission(array $types): ?array
    {
        if ($types === []) {
            return null;
        }

        uasort($types, fn (array $a, array $b): int => ($b['total_emission'] ?? 0) <=> ($a['total_emission'] ?? 0));

        $topName = null;
        $topStats = null;

        foreach ($types as $name => $stats) {
            if (($stats['total_emission'] ?? 0) <= 0) {
                continue;
            }

            $topName = $name;
            $topStats = $stats;
            break;
        }

        return $topName === null ? null : ['name' => $topName, 'stats' => $topStats];
    }

    protected function labelForTransport(string $type): string
    {
        return match ($type) {
            'car' => 'car',
            'bus' => 'bus',
            'jeepney' => 'jeepney',
            'tricycle' => 'tricycle',
            'walking' => 'walking',
            default => 'transport',
        };
    }

    protected function labelForDiet(string $type): string
    {
        return match ($type) {
            'meat' => 'meat-based',
            'average' => 'average',
            'vegetarian' => 'vegetarian',
            'plant_based' => 'plant-based',
            default => 'lower-carbon',
        };
    }
}
