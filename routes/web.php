<?php

use App\Http\Controllers\CalendarController;
use App\Http\Controllers\DayController;
use App\Http\Controllers\DemoController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StatisticController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\TodayController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\YearController;
use App\Http\Controllers\YearMonthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;

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

Route::get('/', [WelcomeController::class, 'index'])->name('welcome');
Route::get('/privacy-policy', [PageController::class, 'privacyPolicy'])->name('page.privacy-policy');
Route::get('/faq', [PageController::class, 'faq'])->name('page.faq');

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store'])->middleware('honeypot');

    if (config('calendar.actions.demo')) {
        Route::get('/demo', [PageController::class, 'demo'])->name('page.demo');
        Route::post('/demo', [DemoController::class, 'login'])->name('demo.login')->middleware('honeypot');
    }

    if (config('calendar.actions.register')) {
        Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
        Route::post('register', [RegisteredUserController::class, 'store'])->middleware('honeypot');
    }

    if (config('calendar.actions.reset')) {
        Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
        Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email')->middleware('honeypot');
        Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
        Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');
    }
});

Route::group(['middleware' => ['auth']], function () {
    Route::resource('tags', TagController::class)->except(['show']);
    Route::resource('days', DayController::class)->except(['show']);
    Route::post('days/jump', [DayController::class, 'jump'])->name('days.jump');
    Route::get('days/{day}/previous', [DayController::class, 'previous'])->name('days.previous');
    Route::get('days/{day}/next', [DayController::class, 'next'])->name('days.next');
    Route::get('today', TodayController::class)->name('days.today');
    Route::get('dashboard', [StatisticController::class, 'index'])->name('statistic.index');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('calendar', [CalendarController::class, 'index'])->name('calendar');
    Route::get('year/months/{year?}', [YearMonthController::class, 'index'])->name('year-month.index');
    Route::get('year/{year?}', [YearController::class, 'index'])->name('year.index');

    Route::get('verify-email', EmailVerificationPromptController::class)->name('verification.notice');
    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])->name('password.confirm');
    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);
    Route::put('password', [PasswordController::class, 'update'])->name('password.update');
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('year-in-review', [\App\Http\Controllers\YearInReviewController::class, 'show'])->name('year-in-review.show');

    Route::post('export', \App\Http\Controllers\ExportController::class)->name('export');
});
