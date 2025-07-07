<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\RecommendationsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/orders', [OrderController::class, 'store']);
Route::get('/analytics', [AnalyticsController::class, 'index']);
Route::get('/analytics/recent-orders', [AnalyticsController::class, 'recentOrders']);
Route::get('/recommendations', [RecommendationsController::class, 'index']);
