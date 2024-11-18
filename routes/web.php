<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MetricHistoryRunController;

Route::get('/metric-history-run/create', [MetricHistoryRunController::class, 'create'])->name('metric-history-run.create');
Route::post('/metric-history-run', [MetricHistoryRunController::class, 'store'])->name('metric-history-run.store');
Route::post('/metric-history-run/fetch-metrics', [MetricHistoryRunController::class, 'fetchMetrics'])->name('metric-history-run.fetch-metrics');
Route::post('/metric-history-run/save-metrics', [MetricHistoryRunController::class, 'saveMetrics'])->name('metric-history-run.save-metrics');
Route::get('/metric-history-run', [MetricHistoryRunController::class, 'index'])->name('metric-history-run.index');

Route::get('/', [MetricHistoryRunController::class, 'index'])->name('metric-history-run.index');
