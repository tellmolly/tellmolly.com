<?php

use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::group(['middleware' => ['auth']], function () {
    Route::post('tags', [\App\Http\Controllers\API\QuickTagController::class, 'store']);

    Route::get('days', [\App\Http\Controllers\API\QuickDayController::class, 'index']);

    Route::post('days/exists', [\App\Http\Controllers\API\QuickDayController::class, 'exists']);
});
