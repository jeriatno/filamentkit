<?php

use Filament\Http\Controllers\Auth\EmailVerificationController;
use Illuminate\Support\Facades\Route;

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

Route::get('/admin/login', function () {
    return redirect()->route('filament.admin.auth.login');
})->name('login');

Route::redirect('/dashboard', '/');

Route::get('/email/verify/{id}/{hash}', EmailVerificationController::class)
    ->name('auth.email.verify');

