<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\EmissionFactorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Kreait\Firebase\Contract\Firestore;

class CarbonCalculationController extends Controller
{
    public function __construct(
        protected Firestore $firestore,
        protected EmissionFactorService $emissionFactorService,
    ) {
    }

    public function calculate(Request $request): JsonResponse
    {
        $validated = Validator::make($request->all(), $this->calculationRules())->validate();

        return response()->json($this->buildCalculationResponse($validated));
    }

    public function saveLog(Request $request): JsonResponse
    {
        $validated = Validator::make($request->all(), $this->calculationRules())->validate();

        $user = $request->user();

        if (! $user) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $calculation = $this->buildCalculationResponse($validated);

        $log = [
            'user_id' => (string) $user->id,
            'user_name' => $user->name,
            'classroom' => $user->classroom,
            'transport_type' => $validated['transport_type'],
            'distance' => (float) $validated['distance'],
            'diet_type' => $validated['diet_type'],
            'gadget_hours' => (float) $validated['gadget_hours'],
            'gadget_type' => $validated['gadget_type'] ?? 'laptop',
            'transport_emission' => $calculation['breakdown']['transport'],
            'diet_emission' => $calculation['breakdown']['diet'],
            'gadget_emission' => $calculation['breakdown']['gadgets'],
            'total_emission' => $calculation['total_emission'],
            'created_at' => now()->toISOString(),
        ];

        $document = $this->firestore
            ->database()
            ->collection('carbon_logs')
            ->add($log);

        return response()->json([
            'message' => 'Carbon log saved successfully.',
            'id' => $document->id(),
            'log' => $log,
        ], 201);
    }

    public function getUserLogs(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $documents = $this->firestore
            ->database()
            ->collection('carbon_logs')
            ->documents();

        $logs = [];

        foreach ($documents as $document) {
            if (! $document->exists()) {
                continue;
            }

            $mapped = $this->mapDocument($document);

            if ((string) ($mapped['user_id'] ?? '') !== (string) $user->id) {
                continue;
            }

            $logs[] = $mapped;
        }

        usort($logs, fn (array $a, array $b) => strcmp($b['created_at'] ?? '', $a['created_at'] ?? ''));

        return response()->json([
            'logs' => $logs,
        ]);
    }

    public function leaderboard(): JsonResponse
    {
        $documents = $this->firestore
            ->database()
            ->collection('carbon_logs')
            ->documents();

        $classrooms = [];
        $students = [];

        foreach ($documents as $document) {
            if (! $document->exists()) {
                continue;
            }

            $data = $document->data();
            $classroom = $data['classroom'] ?? 'Unassigned';
            $emission = (float) ($data['total_emission'] ?? 0);
            $userId = (string) ($data['user_id'] ?? 'unknown');
            $studentKey = $classroom.'::'.$userId;

            if (! isset($classrooms[$classroom])) {
                $classrooms[$classroom] = [
                    'classroom' => $classroom,
                    'total_emission' => 0.0,
                    'log_count' => 0,
                    'average_emission' => 0.0,
                    'classroom_savings_total' => 0.0,
                ];
            }

            if (! isset($students[$classroom])) {
                $students[$classroom] = [];
            }

            if (! isset($students[$classroom][$studentKey])) {
                $students[$classroom][$studentKey] = [
                    'user_id' => $data['user_id'] ?? null,
                    'user_name' => $data['user_name'] ?? 'Unknown student',
                    'classroom' => $classroom,
                    'total_emission' => 0.0,
                    'log_count' => 0,
                    'average_emission' => 0.0,
                ];
            }

            $classrooms[$classroom]['total_emission'] += $emission;
            $classrooms[$classroom]['log_count']++;
            $students[$classroom][$studentKey]['total_emission'] += $emission;
            $students[$classroom][$studentKey]['log_count']++;
        }

        $rankings = array_values(array_map(function (array $classroom): array {
            $classroom['total_emission'] = round($classroom['total_emission'], 2);
            $classroom['average_emission'] = $classroom['log_count'] > 0
                ? round($classroom['total_emission'] / $classroom['log_count'], 2)
                : 0.0;

            return $classroom;
        }, $classrooms));

        usort($rankings, fn (array $a, array $b) => $a['average_emission'] <=> $b['average_emission']);

        $highestAverage = collect($rankings)->max('average_emission') ?? 0;

        foreach ($rankings as $index => &$ranking) {
            $ranking['rank'] = $index + 1;
            $ranking['classroom_savings_total'] = round(
                max(0, ($highestAverage * $ranking['log_count']) - $ranking['total_emission']),
                2
            );
        }
        unset($ranking);

        $studentRankings = [];

        foreach ($students as $classroom => $classroomStudents) {
            $studentRankings[$classroom] = array_values(array_map(function (array $student): array {
                $student['total_emission'] = round($student['total_emission'], 2);
                $student['average_emission'] = $student['log_count'] > 0
                    ? round($student['total_emission'] / $student['log_count'], 2)
                    : 0.0;

                return $student;
            }, $classroomStudents));

            usort($studentRankings[$classroom], fn (array $a, array $b) => $a['average_emission'] <=> $b['average_emission']);

            foreach ($studentRankings[$classroom] as $index => &$student) {
                $student['rank'] = $index + 1;
            }
            unset($student);
        }

        return response()->json([
            'classroom_rankings' => $rankings,
            'student_rankings' => $studentRankings,
        ]);
    }

    protected function calculationRules(): array
    {
        return [
            'transport_type' => ['required', 'string', 'in:jeepney,bus,tricycle,car,walking'],
            'distance' => ['required', 'numeric', 'min:0'],
            'diet_type' => ['required', 'string', 'in:meat,average,vegetarian,plant_based'],
            'gadget_hours' => ['required', 'numeric', 'min:0'],
            'gadget_type' => ['nullable', 'string', 'in:laptop,smartphone,tablet,desktop_pc,monitor'],
        ];
    }

    protected function buildCalculationResponse(array $validated): array
    {
        $transportFactor = $this->emissionFactorService->getTransportFactor($validated['transport_type']);
        $dietFactor = $this->emissionFactorService->getDietFactor($validated['diet_type']);
        $electricityFactor = $this->emissionFactorService->getElectricityFactor();
        $deviceType = $validated['gadget_type'] ?? 'laptop';
        $deviceWattage = $this->emissionFactorService->getDeviceWattage($deviceType);

        $transportEmission = (float) $validated['distance'] * $transportFactor;
        $dietEmission = $dietFactor;
        $gadgetEmission = (float) $validated['gadget_hours'] * $deviceWattage * $electricityFactor;
        $totalEmission = $transportEmission + $dietEmission + $gadgetEmission;

        return [
            'total_emission' => round($totalEmission, 2),
            'breakdown' => [
                'transport' => round($transportEmission, 2),
                'diet' => round($dietEmission, 2),
                'gadgets' => round($gadgetEmission, 2),
            ],
            'meta' => [
                'gadget_type' => $deviceType,
                'device_wattage' => round($deviceWattage, 4),
                'electricity_factor' => round($electricityFactor, 4),
            ],
        ];
    }

    protected function mapDocument(mixed $document): array
    {
        $data = $document->data();

        return [
            'id' => $document->id(),
            'user_id' => $data['user_id'] ?? null,
            'user_name' => $data['user_name'] ?? null,
            'classroom' => $data['classroom'] ?? null,
            'transport_type' => $data['transport_type'] ?? null,
            'distance' => (float) ($data['distance'] ?? 0),
            'diet_type' => $data['diet_type'] ?? null,
            'gadget_hours' => (float) ($data['gadget_hours'] ?? 0),
            'gadget_type' => $data['gadget_type'] ?? 'laptop',
            'transport_emission' => (float) ($data['transport_emission'] ?? 0),
            'diet_emission' => (float) ($data['diet_emission'] ?? 0),
            'gadget_emission' => (float) ($data['gadget_emission'] ?? 0),
            'total_emission' => (float) ($data['total_emission'] ?? 0),
            'created_at' => (string) ($data['created_at'] ?? ''),
        ];
    }
}
