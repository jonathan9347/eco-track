<?php

use App\Livewire\AdminPanel;
use App\Livewire\BadgesAndChallenges;
use App\Livewire\CarbonHistory;
use App\Livewire\Leaderboard;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;
use Kreait\Firebase\Contract\Firestore;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return view('landing');
})->name('home');

Route::middleware('auth')
    ->get('/dashboard', function () {
        return view('dashboard');
    })
    ->name('dashboard');

Route::get('/carbon-history', CarbonHistory::class)
    ->name('carbon.history')
    ->middleware('auth');

Route::get('/leaderboard', Leaderboard::class)
    ->name('leaderboard')
    ->middleware('auth');

Route::get('/achievements', BadgesAndChallenges::class)
    ->name('achievements')
    ->middleware('auth');

Route::middleware('auth')
    ->get('/ai-predictions', function () {
        return view('ai-predictions');
    })
    ->name('ai.predictions');

Route::middleware('auth')
    ->get('/eco-chat', function () {
        return view('eco-chat');
    })
    ->name('eco.chat');

Route::middleware('auth')
    ->get('/eco-tips', function () {
        return view('eco-tips');
    })
    ->name('eco.tips');

Route::middleware('auth')->group(function () {
    Route::get('/reports', [ReportController::class, 'index'])->name('reports');
    Route::get('/reports/export/csv', [ReportController::class, 'exportCsv'])->name('reports.export.csv');
    Route::get('/reports/export/json', [ReportController::class, 'exportJson'])->name('reports.export.json');
});

Route::get('/admin', AdminPanel::class)
    ->name('admin')
    ->middleware(['auth', 'admin']);

Route::get('/firebase-check', function () {
    $credentialsPath = env('FIREBASE_CREDENTIALS');
    
    return [
        'credentials_path' => $credentialsPath,
        'file_exists' => file_exists($credentialsPath),
        'database_url' => env('FIREBASE_DATABASE_URL'),
    ];
});

Route::get('/test-firebase', function (Firestore $firestore) {
    try {
        $database = $firestore->database();
        
        $testData = [
            'test' => 'Connection successful!',
            'timestamp' => now()->toISOString(),
            'message' => 'Eco-Track is connected to Firebase'
        ];
        
        $database->collection('test_connection')->add($testData);
        
        return [
            'success' => true,
            'message' => 'Firebase is working!',
            'data' => $testData
        ];
        
    } catch (\Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
});

if (file_exists(__DIR__ . '/auth.php')) {
    require __DIR__ . '/auth.php';
}

if (file_exists(__DIR__ . '/settings.php')) {
    require __DIR__ . '/settings.php';
}
