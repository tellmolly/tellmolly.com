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


Route::middleware('guest')->group(function () {
    Route::post('login', [\App\Http\Controllers\API\LoginController::class, 'store']);
    Route::post('register', [\App\Http\Controllers\API\RegisterController::class, 'store']);
    Route::post('forgot-password', [\App\Http\Controllers\API\ForgotPasswordController::class, 'store']);
});

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::resource('tags', \App\Http\Controllers\API\TagController::class, ['as' => 'api'])->except(['show', 'create']);
    Route::get('days/calendar', [\App\Http\Controllers\API\QuickDayController::class, 'index']);
    Route::post('days/exists', [\App\Http\Controllers\API\QuickDayController::class, 'exists']);
    Route::resource('days', \App\Http\Controllers\API\DayController::class, ['as' => 'api'])->except(['show']);
    Route::post('days/jump', [\App\Http\Controllers\API\DayController::class, 'jump']);
    Route::get('days/{day}/previous', [\App\Http\Controllers\API\DayController::class, 'previous']);
    Route::get('days/{day}/next', [\App\Http\Controllers\API\DayController::class, 'next']);
    Route::get('today', [\App\Http\Controllers\API\DayController::class, 'today']);
    Route::get('dashboard', [\App\Http\Controllers\API\StatisticController::class, 'index']);

    Route::get('/profile', [\App\Http\Controllers\API\ProfileController::class, 'edit']);
    Route::patch('/profile', [\App\Http\Controllers\API\ProfileController::class, 'update']);
    Route::delete('/profile', [\App\Http\Controllers\API\ProfileController::class, 'destroy']);

    Route::get('year/months/{year?}', [\App\Http\Controllers\API\YearMonthController::class, 'index']);
    Route::get('year/{year?}', [\App\Http\Controllers\API\YearController::class, 'index']);

    Route::get('year-in-review', [\App\Http\Controllers\API\YearInReviewController::class, 'show']);

    Route::post('export', \App\Http\Controllers\ExportController::class);
});
