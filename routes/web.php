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
    return view('welcome');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Events Management for Managers
    Route::resource('events', EventController::class);

    // Facilitator Specific Routes
    Route::get('/facilitator/dashboard', [FacilitatorController::class, 'dashboard'])->name('facilitator.dashboard');
    Route::get('/facilitator/reviews', [FacilitatorController::class, 'reviews'])->name('facilitator.reviews');
    Route::get('/facilitator/history', [FacilitatorController::class, 'history'])->name('facilitator.history');
    
    // Profile Management
    Route::get('/profile', [FacilitatorController::class, 'profile'])->name('facilitator.profile');
    Route::get('/profile/edit', [FacilitatorController::class, 'editProfile'])->name('facilitator.editProfile');
    Route::put('/profile', [FacilitatorController::class, 'updateProfile'])->name('facilitator.updateProfile');

    // Attendance
    Route::get('/attendance/record', [AttendanceController::class, 'clockin_view'])->name('attendance.clockin_view');
    Route::post('/attendance/clock-in', [AttendanceController::class, 'store'])->name('attendance.clockIn');
    Route::put('/attendance/{id}/clock-out', [AttendanceController::class, 'update'])->name('attendance.clockOut');

    // Allowances & Leaves
    Route::resource('allowances', App\Http\Controllers\AllowanceController::class);
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
