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

Route::get('/', 'CalendarController@index');
Route::get('year/{year?}', 'YearController@index')->name('year.index');

Auth::routes([
    'register' => config('calendar.actions.register'),
    'reset' => config('calendar.actions.reset')
]);

Route::group(['middleware' => ['auth']], function () {
    Route::get('/home', 'HomeController@index')->name('home');

    Route::resource('categories', 'CategoryController');
    Route::resource('tags', 'TagController');
    Route::resource('days', 'DayController');
});
