<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FacilitatorController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ReviewController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Events Management for Managers
    Route::resource('events', EventController::class);

    // Facilitator Specific Routes
    Route::get('/facilitator/dashboard', [FacilitatorController::class, 'dashboard'])->name('facilitator.dashboard');

    Route::get('/facilitator/history', [FacilitatorController::class, 'history'])->name('facilitator.history');
    
    // Profile Management
    Route::get('/profile', [FacilitatorController::class, 'profile'])->name('facilitator.profile');
    Route::get('/profile/edit', [FacilitatorController::class, 'editProfile'])->name('facilitator.editProfile');
    Route::put('/profile', [FacilitatorController::class, 'updateProfile'])->name('facilitator.updateProfile');

    // Attendance
    Route::get('/attendance/record', [AttendanceController::class, 'clockin_view'])->name('attendance.clockin_view');
    Route::post('/attendance/clock-in', [AttendanceController::class, 'store'])->name('attendance.clockIn');
    Route::put('/attendance/{id}/clock-out', [AttendanceController::class, 'update'])->name('attendance.clockOut');
    Route::post('/attendance/{id}/upload-proof', [AttendanceController::class, 'uploadProof'])->name('attendance.uploadProof');

    // Admin Attendance Management
    Route::get('/admin/attendance', [AttendanceController::class, 'adminIndex'])->name('admin.attendance');
    Route::put('/admin/attendance/{id}', [AttendanceController::class, 'adminUpdate'])->name('admin.attendance.update');

    // Facilitator Management (Admin)
    Route::get('/admin/facilitators', [App\Http\Controllers\AdminFacilitatorController::class, 'index'])->name('facilitators.index');
    Route::post('/admin/facilitators', [App\Http\Controllers\AdminFacilitatorController::class, 'store'])->name('facilitators.store');
    Route::get('/admin/facilitators/{id}/edit', [App\Http\Controllers\AdminFacilitatorController::class, 'edit'])->name('facilitators.edit');
    Route::put('/admin/facilitators/{id}', [App\Http\Controllers\AdminFacilitatorController::class, 'update'])->name('facilitators.update');
    Route::delete('/admin/facilitators/{id}', [App\Http\Controllers\AdminFacilitatorController::class, 'destroy'])->name('facilitators.destroy');

    // Payroll Management (Admin)
    Route::get('/admin/payroll', [App\Http\Controllers\PaymentController::class, 'payrollIndex'])->name('admin.payroll');

    // Admin Payments
    Route::get('/admin/payments', [App\Http\Controllers\PaymentController::class, 'index'])->name('admin.payments');
    Route::put('/admin/payments/{id}', [App\Http\Controllers\PaymentController::class, 'update'])->name('payments.update');

    // Payments (Facilitator)
    Route::get('/facilitator/payments', [App\Http\Controllers\PaymentController::class, 'facilitatorIndex'])->name('facilitator.payments');
    Route::post('/facilitator/payments', [App\Http\Controllers\PaymentController::class, 'store'])->name('facilitator.payments.store');

    // Performance Routes
    Route::resource('performance', App\Http\Controllers\PerformanceController::class)->only(['index', 'store']);
    Route::get('/facilitator/reviews', [App\Http\Controllers\PerformanceController::class, 'myReviews'])->name('facilitator.reviews');
    
    // Admin Leave Management
    Route::get('/admin/leaves', [App\Http\Controllers\LeaveController::class, 'adminIndex'])->name('admin.leaves');

    Route::resource('leaves', App\Http\Controllers\LeaveController::class);

    // Assignments
    Route::get('/events/{event}/assign', [AssignmentController::class, 'create'])->name('assignments.create');
    Route::resource('assignments', AssignmentController::class)->only(['index', 'store', 'destroy']);
    Route::post('/assignments/{assignment}/accept', [AssignmentController::class, 'accept'])->name('assignments.accept');
    Route::post('/assignments/{assignment}/decline', [AssignmentController::class, 'decline'])->name('assignments.decline');

    // Reviews
    Route::post('/facilitator/{id}/review', [ReviewController::class, 'store'])->name('reviews.store');
    
    // Public Profile (for managers to see facilitator details)
    Route::get('/facilitator/{id}', [FacilitatorController::class, 'show'])->name('facilitator.show');
});
