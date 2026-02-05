<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FacilitatorController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\EventRuleController;

use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Auth Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Auth Routes (Placeholder if using Breeze/Jetstream, otherwise simple)
Route::middleware(['web'])->group(function () {
    // Events
    Route::resource('events', EventController::class);
    Route::resource('event-rules', EventRuleController::class);
    // REMOVED 'apply' route as request by User to use Admin Assignment instead
    
    // Facilitator Dashboard & Profile
    Route::get('/dashboard', [FacilitatorController::class, 'dashboard'])->name('facilitator.dashboard');
    Route::get('/profile/edit', [FacilitatorController::class, 'edit'])->name('facilitator.edit');
    Route::post('/profile/update', [FacilitatorController::class, 'update'])->name('facilitator.update');
    Route::get('/facilitator/{id}', [FacilitatorController::class, 'show'])->name('facilitator.show');

    // Attendance
    Route::get('/attendance/record', [AttendanceController::class, 'clockin_view'])->name('attendance.clockin_view');
    Route::post('/attendance/clock-in', [AttendanceController::class, 'store'])->name('attendance.clockIn');
    Route::put('/attendance/{id}/clock-out', [AttendanceController::class, 'update'])->name('attendance.clockOut');

    // Assignments
    Route::get('/events/{event}/assign', [AssignmentController::class, 'create'])->name('assignments.create');
    Route::resource('assignments', AssignmentController::class)->only(['index', 'store', 'destroy']);

    // Reviews
    Route::post('/facilitator/{id}/review', [ReviewController::class, 'store'])->name('reviews.store');

    // Admin
    Route::get('/admin/payments', [PaymentController::class, 'index'])->name('admin.payments');
    Route::put('/admin/payments/{id}', [PaymentController::class, 'update'])->name('payments.update');
});
