<?php

use App\Http\Controllers\AgencyController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\LeadController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompaniesHouseController;
use App\Http\Controllers\LeadActivityController;
use App\Http\Controllers\LoginLogController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ChatUnreadController;

/*
|--------------------------------------------------------------------------
| Public / Authentication Routes
|--------------------------------------------------------------------------
|
| These routes are accessible without authentication.
|
*/

// Home - redirect/show login page
Route::get('/', [AuthController::class, 'showLogin']);

// Login
Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.submit');

// Logout
Route::get('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| OTP Authentication Routes
|--------------------------------------------------------------------------
*/

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


/*
|--------------------------------------------------------------------------
| Forgot Password Routes
|--------------------------------------------------------------------------
*/

Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])
    ->name('password.request');

Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])
    ->name('password.email');

/*
|--------------------------------------------------------------------------
| Reset Password Routes
|--------------------------------------------------------------------------
*/

Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])
    ->name('password.reset');

Route::post('/reset-password', [AuthController::class, 'resetPassword'])
    ->name('password.update');

/*
|--------------------------------------------------------------------------
| Session Expired
|--------------------------------------------------------------------------
|
| This page should be accessible when a user's session has expired.
|
*/

Route::get('/session-expired', function () {
    return view('auth.session-expired');
})->name('session.expired');
Route::middleware(['auth', 'active','session.timeout'])->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users/store', [UserController::class, 'store'])->name('users.store');
    Route::post('/users/update/{id}', [UserController::class, 'update'])->name('users.update');
    Route::get('/users/delete/{id}', [UserController::class, 'destroy'])->name('users.delete');
    Route::post('/users/toggle-otp/{id}', [UserController::class, 'toggleOtp'])->name('users.toggleOtp');
    Route::post('/users/toggle-mobile/{id}', [UserController::class, 'toggleMobile'])->name('users.toggleMobile');
    Route::post('/users/toggle-tablet/{id}', [UserController::class, 'toggleTablet'])->name('users.toggleTablet');
    Route::post('users/toggle-status/{id}', [UserController::class, 'toggleStatus'])->name('users.toggleStatus');
});
Route::middleware(['auth','active', 'session.timeout'])->group(function () {
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::post('/roles/store', [RoleController::class, 'store'])->name('roles.store');
    Route::post('/roles/update/{id}', [RoleController::class, 'update'])->name('roles.update');
    Route::get('/roles/delete/{id}', [RoleController::class, 'destroy'])->name('roles.delete');
});
Route::middleware(['auth','active','session.timeout'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});
Route::middleware(['auth','active','session.timeout'])->group(function () {
    Route::get('/agencies', [AgencyController::class, 'index'])->name('agencies.index');
    Route::post('/agencies/store', [AgencyController::class, 'store'])->name('agencies.store');
    Route::post('/agencies/update/{id}', [AgencyController::class, 'update'])->name('agencies.update');
    Route::get('/agencies/delete/{id}', [AgencyController::class, 'destroy'])->name('agencies.delete');
    Route::get('/agencies/show', [AgencyController::class, 'showAgency'])->name('agency.show');
    Route::post('/agency/detailUpdate', [AgencyController::class, 'detailUpdate'])->name('agency.detailUpdate');
});

Route::middleware(['auth', 'active', 'session.timeout'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::post('/reminders/{reminder}/dismiss', [DashboardController::class, 'dismissReminder'])
        ->name('reminders.dismiss');


    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */

    Route::get('/profile', [ProfileController::class, 'index'])
        ->name('profile.index');

    Route::put('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    /*
    |--------------------------------------------------------------------------
    | Leads
    |--------------------------------------------------------------------------
    */

    // Lead listing
    Route::get('/leads', [LeadController::class, 'index'])
        ->name('leads.index');

    // Create lead form
    Route::get('/leads/create', [LeadController::class, 'create'])
        ->name('leads.create');

    // Store new lead
    Route::post('/leads', [LeadController::class, 'store'])
        ->name('leads.store');

    // View single lead
    Route::get('/leads/{lead}', [LeadController::class, 'show'])
        ->name('leads.show');

    // Edit lead form
    Route::get('/leads/{lead}/edit', [LeadController::class, 'edit'])
        ->name('leads.edit');

    // Update lead
    Route::put('/leads/{lead}', [LeadController::class, 'update'])
        ->name('leads.update');

    // Delete lead
    Route::delete('/leads/{lead}', [LeadController::class, 'destroy'])
        ->name('leads.destroy');

    // Update lead status
    Route::patch('/leads/{lead}/status', [LeadController::class, 'updateStatus'])
        ->name('leads.updateStatus');


    /*
    |--------------------------------------------------------------------------
    | Lead Reminders
    |--------------------------------------------------------------------------
    */

    // Create reminder
    Route::post('/leads/{lead}/reminders', [LeadController::class, 'storeReminder'])
        ->name('leads.reminders.store');

    // Get reminders for a lead
    Route::get('/leads/{lead}/reminders', [LeadController::class, 'reminders'])
        ->name('leads.reminders');

    // Delete reminder
    Route::delete('/lead-reminders/{reminder}', [LeadController::class, 'destroyReminder'])
        ->name('leads.reminders.destroy');

    // Update reminder
    Route::put('/lead-reminders/{reminder}', [LeadController::class, 'updateReminder'])
        ->name('leads.reminders.update');


    /*
    |--------------------------------------------------------------------------
    | Lead Logs
    |--------------------------------------------------------------------------
    */

    Route::get('/leads/{lead}/logs', [LeadController::class, 'logs'])
        ->name('leads.logs');


    /*
    |--------------------------------------------------------------------------
    | Lead Activities
    |--------------------------------------------------------------------------
    */

    // List activities for a lead
    Route::get('/leads/{lead}/activities', [LeadActivityController::class, 'index'])
        ->name('leads.activities');

    // Create activity
    Route::post('/leads/{lead}/activities', [LeadActivityController::class, 'store'])
        ->name('leads.activities.store');

    // Update activity
    Route::put('/lead-activities/{activity}', [LeadActivityController::class, 'update'])
        ->name('lead-activities.update');

    // Delete activity
    Route::delete('/lead-activities/{activity}', [LeadActivityController::class, 'destroy'])
        ->name('lead-activities.destroy');


    /*
    |--------------------------------------------------------------------------
    | Companies House
    |--------------------------------------------------------------------------
    */

    // Search companies
    Route::get('/companies-house/search', [CompaniesHouseController::class, 'search'])
        ->name('companies.house.search');

    // Show company details
    Route::get('/companies-house/{companyNumber}', [CompaniesHouseController::class, 'show']);


    /*
    |--------------------------------------------------------------------------
    | Attendance
    |--------------------------------------------------------------------------
    |
    | Attendance routes were previously completely public.
    | They are now protected by authentication and session middleware.
    |
    */

    // Attendance dashboard
    Route::get('/attendance', [AttendanceController::class, 'index'])
        ->name('attendance.index');

    // Current attendance status
    Route::get('/attendance/status', [AttendanceController::class, 'status'])
        ->name('attendance.status');

    // Punch in / punch out
    Route::post('/attendance/punch', [AttendanceController::class, 'punch'])
        ->name('attendance.punch');

    // Employee attendance report
    Route::get('/attendance/report', [AttendanceController::class, 'report'])
        ->name('attendance.report');


    /*
    |--------------------------------------------------------------------------
    | Attendance Admin
    |--------------------------------------------------------------------------
    |
    | These routes should only be accessible to administrators.
    |
    */

    Route::middleware('admin')->group(function () {

        // Admin attendance dashboard
        Route::get('/attendance/admin', [AttendanceController::class, 'adminIndex'])
            ->name('attendance.admin');

        // Admin attendance report
        Route::get('/attendance/admin/report', [AttendanceController::class, 'adminReport'])
            ->name('attendance.admin.report');
    });

});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| These routes require:
| - User to be authenticated
| - User account to be active
| - Session to be valid
| - User to have admin access
|
*/

Route::middleware(['auth', 'active', 'session.timeout', 'admin'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | User Management
    |--------------------------------------------------------------------------
    */

    // List users
    Route::get('/users', [UserController::class, 'index'])
        ->name('users.index');

    // Create user
    Route::post('/users/store', [UserController::class, 'store'])
        ->name('users.store');

    // Update user
    Route::post('/users/update/{id}', [UserController::class, 'update'])
        ->name('users.update');

    // Delete user
    Route::get('/users/delete/{id}', [UserController::class, 'destroy'])
        ->name('users.delete');

    // Enable / disable OTP
    Route::post('/users/toggle-otp/{id}', [UserController::class, 'toggleOtp'])
        ->name('users.toggleOtp');

    // Enable / disable mobile access
    Route::post('/users/toggle-mobile/{id}', [UserController::class, 'toggleMobile'])
        ->name('users.toggleMobile');

    // Enable / disable tablet access
    Route::post('/users/toggle-tablet/{id}', [UserController::class, 'toggleTablet'])
        ->name('users.toggleTablet');

    // Enable / disable user
    Route::post('/users/toggle-status/{id}', [UserController::class, 'toggleStatus'])
        ->name('users.toggleStatus');


    /*
    |--------------------------------------------------------------------------
    | Role Management
    |--------------------------------------------------------------------------
    */

    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/attendance/status', [AttendanceController::class, 'status'])->name('attendance.status'); // used by header widget
    Route::post('/attendance/punch', [AttendanceController::class, 'punch'])->name('attendance.punch');
    Route::get('/attendance/report', [AttendanceController::class, 'report'])->name('attendance.report');
    Route::get('/attendance/admin', [AttendanceController::class, 'adminIndex'])->name('attendance.admin');
    Route::get('/attendance/admin/report', [AttendanceController::class, 'adminReport'])->name('attendance.admin.report');
    Route::middleware(['auth', 'chat.access'])->group(function () {

        Route::get('/chat', function () {
            return redirect('/chatify');
        })->name('chat.index');

    });

    Route::middleware('auth')->group(function () {
        Route::get('/chat/unread-count', [ChatUnreadController::class, 'count'])
            ->name('chat.unread.count');
    });
    // List roles
    // Route::get('/roles', [RoleController::class, 'index'])
    //     ->name('roles.index');

    // // Create role
    // Route::post('/roles/store', [RoleController::class, 'store'])
    //     ->name('roles.store');

    // // Update role
    // Route::post('/roles/update/{id}', [RoleController::class, 'update'])
    //     ->name('roles.update');

    // // Delete role
    // Route::get('/roles/delete/{id}', [RoleController::class, 'destroy'])
        ->name('roles.delete');


    /*
    |--------------------------------------------------------------------------
    | Login Logs
    |--------------------------------------------------------------------------
    */

    Route::get('/login-logs', [LoginLogController::class, 'index'])
        ->name('login-logs.index');

});
