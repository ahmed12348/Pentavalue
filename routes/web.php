<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AnalyticsController;
use App\Events\AnalyticsUpdated;
use App\Http\Controllers\RecommendationsController;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\OrderController;
use App\Events\OrderPlaced;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('dashboard');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::post('/orders', [OrderController::class, 'store']);
Route::get('/analytics', [AnalyticsController::class, 'index']);
Route::get('/recommendations', [RecommendationsController::class, 'index']);
