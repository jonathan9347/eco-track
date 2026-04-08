<div class="rounded-lg border border-zinc-200 bg-white p-6 dark:border-zinc-700 dark:bg-zinc-800">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">AI Predictions</h3>
            <p class="mt-1 text-sm text-zinc-500">Built from your saved carbon logs so the forecast and recommendations stay tied to recorded activity.</p>
        </div>
        <button wire:click="refreshPredictions" class="rounded-md p-2 text-zinc-400 hover:bg-zinc-100 hover:text-zinc-600 dark:hover:bg-zinc-700">
            <svg class="h-5 w-5 {{ $loading ? 'animate-spin' : '' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
            </svg>
        </button>
    </div>

    @if($loading)
        <div class="flex items-center justify-center py-12">
            <div class="h-8 w-8 animate-spin rounded-full border-4 border-zinc-200 border-t-emerald-500"></div>
        </div>
    @elseif($noData)
        <div class="rounded-lg bg-zinc-50 p-6 text-center dark:bg-zinc-700/50">
            <svg class="mx-auto h-12 w-12 text-zinc-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
            </svg>
            <p class="mt-4 text-sm text-zinc-500">Not enough recorded days yet. Log at least 3 days of activity to generate a forecast.</p>
        </div>
    @elseif($error)
        <div class="rounded-lg bg-red-50 p-4 dark:bg-red-900/20">
            <p class="text-sm text-red-600 dark:text-red-400">{{ $error }}</p>
        </div>
    @elseif($prediction)
        <div class="mb-6 grid gap-4 md:grid-cols-4">
            <div class="rounded-lg bg-zinc-50 p-4 dark:bg-zinc-700/50">
                <p class="text-sm text-zinc-500">Predicted Tomorrow</p>
                <p class="mt-1 text-2xl font-bold text-zinc-900 dark:text-white">{{ number_format($prediction['predicted_emission'], 2) }} kg CO2</p>
            </div>
            <div class="rounded-lg bg-zinc-50 p-4 dark:bg-zinc-700/50">
                <p class="text-sm text-zinc-500">Today Logged</p>
                <p class="mt-1 text-2xl font-bold text-zinc-900 dark:text-white">{{ number_format($todayEmission, 2) }} kg CO2</p>
            </div>
            <div class="rounded-lg bg-zinc-50 p-4 dark:bg-zinc-700/50">
                <p class="text-sm text-zinc-500">Recent Trend</p>
                <p class="mt-1 text-2xl font-bold {{ $this->trendColor() }}">{{ $this->trendIcon() }} {{ number_format(abs($prediction['trend']), 1) }}%</p>
                <p class="mt-1 text-xs text-zinc-500">Compared with the previous 3 logged days</p>
            </div>
            <div class="rounded-lg bg-zinc-50 p-4 dark:bg-zinc-700/50">
                <p class="text-sm text-zinc-500">Confidence</p>
                <div class="mt-2 flex items-center gap-2">
                    <div class="h-3 w-16 overflow-hidden rounded-full bg-zinc-200">
                        <div class="h-full {{ $this->confidenceColor() }}" style="width: {{ $prediction['confidence'] }}%"></div>
                    </div>
                    <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ $prediction['confidence_label'] }}</span>
                </div>
                <p class="mt-1 text-xs text-zinc-500">Based on {{ $prediction['based_on_days'] }} logged day{{ $prediction['based_on_days'] === 1 ? '' : 's' }}</p>
            </div>
        </div>

        <div class="mb-6 rounded-lg bg-zinc-50 p-4 dark:bg-zinc-700/50">
            <p class="text-sm font-medium text-zinc-500">Forecast Basis</p>
            @php($changeDirection = $prediction['change_from_latest'] > 0 ? 'higher' : ($prediction['change_from_latest'] < 0 ? 'lower' : 'the same'))
            <p class="mt-2 text-sm text-zinc-700 dark:text-zinc-300">
                Your latest logged day was {{ number_format($prediction['latest_actual'], 2) }} kg CO2.
                The recent 3-day average is {{ number_format($prediction['recent_average'], 2) }} kg CO2,
                compared with a 7-day average of {{ number_format($prediction['seven_day_average'], 2) }} kg CO2.
                Tomorrow's forecast is
                @if($changeDirection === 'the same')
                    the same as your latest logged day.
                @else
                    {{ number_format(abs($prediction['change_from_latest']), 2) }} kg CO2 {{ $changeDirection }} than your latest logged day.
                @endif
            </p>
        </div>

        @if($sparkline)
            @php($maxValue = max(max($sparkline['values']), 1))
            <div class="mb-6 rounded-lg bg-zinc-50 p-4 dark:bg-zinc-700/50">
                <p class="mb-3 text-sm font-medium text-zinc-500">Last 7 Days and Forecast</p>
                <div class="flex items-end justify-between gap-1" style="height: 80px;">
                    @foreach($sparkline['values'] as $index => $value)
                        <div class="flex flex-col items-center gap-1">
                            <div class="w-6 rounded-t {{ $sparkline['is_prediction'][$index] ? 'bg-emerald-400' : 'bg-emerald-600' }}" style="height: {{ ($value / $maxValue) * 100 }}%"></div>
                            <span class="text-[10px] text-zinc-400">{{ $sparkline['labels'][$index] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if(count($recommendations) > 0)
            <div>
                <p class="mb-3 text-sm font-medium text-zinc-500">Data-Backed Recommendations</p>
                <div class="space-y-3">
                    @foreach($recommendations as $rec)
                        <div class="flex items-start gap-3 rounded-lg border border-zinc-100 p-3 dark:border-zinc-700">
                            <div class="flex-shrink-0 rounded-full p-2 {{ $this->difficultyColor($rec['difficulty']) }}">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-zinc-800 dark:text-zinc-200">{{ $rec['action'] }}</p>
                                @if(!empty($rec['detail']))
                                    <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">{{ $rec['detail'] }}</p>
                                @endif
                                <div class="mt-2 flex items-center gap-2">
                                    <span class="rounded px-2 py-0.5 text-xs {{ $this->difficultyColor($rec['difficulty']) }}">{{ $rec['difficulty'] }}</span>
                                    <span class="text-xs text-emerald-600 dark:text-emerald-400">Potential savings: {{ number_format($rec['potential_savings'], 2) }} kg CO2</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endif
</div>
