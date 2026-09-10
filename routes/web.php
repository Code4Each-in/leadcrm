<?php

use App\Http\Controllers\AgencyController;
use App\Http\Controllers\LeadController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompaniesHouseController;
use App\Http\Controllers\LeadActivityController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;


Route::get('/', [AuthController::class, 'showLogin']);
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::get('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware(['auth', 'active','session.timeout'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});
Route::get('/session-expired', function () {
    return view('auth.session-expired');
})->name('session.expired');

// OTP Login
Route::get('/login/otp', [AuthController::class, 'showOtpLogin'])
    ->name('otp.login');

Route::post('/login/otp/send', [AuthController::class, 'sendOtp'])
    ->name('otp.send');

Route::get('/login/otp/verify', [AuthController::class, 'showVerifyOtp'])
    ->name('otp.verify');

Route::post('/login/otp/verify', [AuthController::class, 'verifyOtp'])
    ->name('otp.verify.submit');

Route::post('/login/otp/resend', [AuthController::class, 'resendOtp'])
    ->name('otp.resend');

Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])
    ->name('password.email');
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])
    ->name('password.request');
Route::post('/users/toggle-otp/{id}', [UserController::class, 'toggleOtp'])
    ->name('users.toggleOtp');
// Reset Password
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])
    ->name('password.reset');

Route::post('/reset-password', [AuthController::class, 'resetPassword'])
    ->name('password.update');
Route::middleware(['auth', 'active'])->group(function () {
Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::post('/users/store', [UserController::class, 'store'])->name('users.store');
Route::post('/users/update/{id}', [UserController::class, 'update'])->name('users.update');
Route::get('/users/delete/{id}', [UserController::class, 'destroy'])->name('users.delete');
});
Route::middleware(['auth','active'])->group(function () {
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::post('/roles/store', [RoleController::class, 'store'])->name('roles.store');
    Route::post('/roles/update/{id}', [RoleController::class, 'update'])->name('roles.update');
    Route::get('/roles/delete/{id}', [RoleController::class, 'destroy'])->name('roles.delete');
});
Route::post('users/toggle-status/{id}', [UserController::class, 'toggleStatus'])->name('users.toggleStatus');
Route::middleware(['auth','active'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});
Route::middleware(['auth','active'])->group(function () {
    Route::get('/agencies', [AgencyController::class, 'index'])->name('agencies.index');
    Route::post('/agencies/store', [AgencyController::class, 'store'])->name('agencies.store');
    Route::post('/agencies/update/{id}', [AgencyController::class, 'update'])->name('agencies.update');
    Route::get('/agencies/delete/{id}', [AgencyController::class, 'destroy'])->name('agencies.delete');
    Route::get('/agencies/show', [AgencyController::class, 'showAgency'])->name('agency.show');
    Route::post('/agency/detailUpdate', [AgencyController::class, 'detailUpdate'])->name('agency.detailUpdate');
});


    // routes/web.php
Route::post('/reminders/{reminder}/dismiss', [DashboardController::class, 'dismissReminder']);
Route::get('/leads/{lead}/activities', [LeadActivityController::class, 'index'])->name('leads.activities');
Route::post('/leads/{lead}/activities', [LeadActivityController::class, 'store'])->name('leads.activities.store');
Route::put('/lead-activities/{activity}', [LeadActivityController::class, 'update'])->name('lead-activities.update');
Route::delete('/lead-activities/{activity}', [LeadActivityController::class, 'destroy'])->name('lead-activities.destroy');
Route::get('/leads', [LeadController::class, 'index'])
    ->name('leads.index');

Route::get('/leads/create', [LeadController::class, 'create'])
    ->name('leads.create');

Route::post('/leads', [LeadController::class, 'store'])
    ->name('leads.store');

Route::get('/leads/{lead}', [LeadController::class, 'show'])
    ->name('leads.show');

Route::put('/leads/{lead}', [LeadController::class, 'update'])
    ->name('leads.update');

Route::delete('/leads/{lead}', [LeadController::class, 'destroy'])
    ->name('leads.destroy');
Route::get('/leads/{lead}/edit',[LeadController::class, 'edit'])->name('leads.edit');
Route::get('/companies-house/search', [
    CompaniesHouseController::class,
    'search'
])->name('companies.house.search');

Route::get('/companies-house/{companyNumber}', [
    CompaniesHouseController::class,
    'show'
]);
Route::post(
    '/leads/{lead}/reminders',
    [LeadController::class, 'storeReminder']
)->name('leads.reminders.store');

Route::get(
    '/leads/{lead}/reminders',
    [LeadController::class, 'reminders']
)->name('leads.reminders');

Route::delete(
    '/lead-reminders/{reminder}',
    [LeadController::class, 'destroyReminder']
)->name('leads.reminders.destroy');
