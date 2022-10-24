<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [\App\Http\Controllers\WelcomeController::class, 'index'])->name('welcome');
Route::get('/calendar', [\App\Http\Controllers\CalendarController::class, 'index'])->name('calendar');
Route::get('year/{year?}', [\App\Http\Controllers\YearController::class, 'index'])->name('year.index');

Auth::routes([
    'register' => config('calendar.actions.register'),
    'reset' => config('calendar.actions.reset')
]);

Route::group(['middleware' => ['auth']], function () {
    Route::resource('tags', \App\Http\Controllers\TagController::class)->except(['show']);
    Route::resource('days', \App\Http\Controllers\DayController::class)->except(['show']);
    Route::post('days/jump', [\App\Http\Controllers\DayController::class, 'jump'])->name('days.jump');
    Route::get('days/{day}/previous', [\App\Http\Controllers\DayController::class, 'previous'])->name('days.previous');
    Route::get('days/{day}/next', [\App\Http\Controllers\DayController::class, 'next'])->name('days.next');
});
