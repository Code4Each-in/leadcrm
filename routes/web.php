<?php

use App\Http\Controllers\AgencyController;
use App\Http\Controllers\ApplicationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompaniesHouseController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\LeadDocumentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LeadImportController;
use App\Http\Controllers\LeadNoteController;

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
Route::get('/roles', [RoleController::class, 'index']);
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

Route::middleware(['auth','active'])->group(function () {
    Route::get('/leads',[LeadController::class, 'index'])->name('leads.index');
    Route::post('/leads',[LeadController::class, 'store'])->name('leads.store');
    Route::post('/leads/{id}/update',[LeadController::class, 'update'])->name('leads.update');
    Route::get('/leads/{id}/delete',[LeadController::class, 'destroy'])->name('leads.delete');
    Route::get('/leads/template', [LeadController::class, 'downloadTemplate'])->name('leads.template');

    Route::get('/leads/{leadId}', [LeadController::class, 'showLead'])->name('leads.show');

});

Route::post('/leads/{id}/status', [LeadController::class, 'updateStatus'])->name('leads.updateStatus');
Route::post('/import', [LeadImportController::class, 'import'])->name('import');
Route::post('/set-agency', [AgencyController::class, 'setAgency'])
    ->name('set.agency');
/*
| NOTES
*/
Route::post('/notes', [LeadNoteController::class, 'store'])
    ->name('notes.store');

Route::put('/notes/{id}', [LeadNoteController::class, 'update'])
    ->name('notes.update');

/*
| DOCUMENTS
*/
Route::post('/documents', [LeadDocumentController::class, 'store'])
    ->name('documents.store');

Route::delete('/documents/{id}', [LeadDocumentController::class, 'destroy'])
    ->name('documents.destroy');


Route::post('/reminders', [LeadController::class, 'storeReminder'])->name('reminders.store');
Route::get('/reminder/delete/{id}', [LeadController::class, 'destroyReminder'])
    ->name('reminders.delete');
    Route::post('/lead/{id}/move-to-qa', [LeadController::class, 'moveToQA'])
    ->name('lead.move-to-qa');

Route::post('/lead/{id}/move-to-manager', [LeadController::class, 'moveToManager'])
    ->name('lead.move-to-manager');

Route::post('/lead/{id}/return-ae', [LeadController::class, 'returnToAE'])
    ->name('lead.return-ae');

Route::post('/lead/{id}/complete', [LeadController::class, 'markComplete'])
    ->name('lead.complete');

Route::post('/lead/{id}/lost', [LeadController::class, 'markLost'])
    ->name('lead.lost');
    // routes/web.php
Route::post('/reminders/{reminder}/dismiss', [DashboardController::class, 'dismissReminder']);


Route::get('/applications/create', [ApplicationController::class, 'create'])
    ->name('applications.create');

Route::post('/applications', [ApplicationController::class, 'store'])
    ->name('applications.store');
    
Route::get('/companies-house/search', [
    CompaniesHouseController::class,
    'search'
])->name('companies.house.search');

Route::get('/companies-house/{companyNumber}', [
    CompaniesHouseController::class,
    'show'
]);
