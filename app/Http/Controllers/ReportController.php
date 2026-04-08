<?php

namespace App\Http\Controllers;

use App\Services\UserReportService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function __construct(
        protected UserReportService $reports,
    ) {
    }

    public function index(Request $request)
    {
        $user = $request->user();

        abort_unless($user, 401);

        return view('reports', $this->reports->buildForUser($user));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $user = $request->user();

        abort_unless($user, 401);

        $report = $this->reports->buildForUser($user);
        $filename = 'eco-track-report-'.$user->id.'-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($report) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'Created At',
                'Transport Type',
                'Distance (km)',
                'Diet Type',
                'Gadget Hours',
                'Transport Emission',
                'Diet Emission',
                'Gadget Emission',
                'Total Emission',
            ]);

            foreach ($report['logs'] as $log) {
                fputcsv($handle, [
                    $log['created_at'],
                    $log['transport_type'],
                    $log['distance'],
                    $log['diet_type'],
                    $log['gadget_hours'],
                    $log['transport_emission'],
                    $log['diet_emission'],
                    $log['gadget_emission'],
                    $log['total_emission'],
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function exportJson(Request $request)
    {
        $user = $request->user();

        abort_unless($user, 401);

        $report = $this->reports->buildForUser($user);

        return response()->json([
            'generated_at' => now()->toISOString(),
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'classroom' => $user->classroom,
            ],
            'summary' => $report['summary'],
            'monthly' => $report['monthly'],
            'logs' => $report['logs'],
        ], 200, [
            'Content-Disposition' => 'attachment; filename="eco-track-report-'.$user->id.'-'.now()->format('Ymd-His').'.json"',
        ]);
    }
}
