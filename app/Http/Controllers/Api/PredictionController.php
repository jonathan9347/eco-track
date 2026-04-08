<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PredictionInsightsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PredictionController extends Controller
{
    public function __construct(
        protected PredictionInsightsService $predictionInsights,
    ) {
    }

    public function predict(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $insights = $this->predictionInsights->buildForUser($user);

        if (! $insights['ready']) {
            return response()->json([
                'message' => $insights['message'],
            ], 400);
        }

        return response()->json([
            'prediction' => $insights['prediction'],
            'recommendations' => $insights['recommendations'],
            'sparkline' => $insights['sparkline'],
            'today_emission' => $insights['today_emission'],
        ]);
    }
}
