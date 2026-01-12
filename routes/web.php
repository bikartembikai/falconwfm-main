<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FacilitatorController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\RecommendationController;

Route::get('/', function () {
    return redirect()->route('events.index');
});

// Auth Routes (Placeholder if using Breeze/Jetstream, otherwise simple)
Route::middleware(['web'])->group(function () {
    // Events
    Route::resource('events', EventController::class);
    Route::post('/events/{id}/apply', [EventController::class, 'apply'])->name('events.apply');

    // Facilitator Dashboard & Profile
    Route::get('/dashboard', [FacilitatorController::class, 'dashboard'])->name('facilitator.dashboard');
    Route::get('/profile/edit', [FacilitatorController::class, 'edit'])->name('facilitator.edit');
    Route::post('/profile/update', [FacilitatorController::class, 'update'])->name('facilitator.update');
    Route::get('/facilitator/{id}', [FacilitatorController::class, 'show'])->name('facilitator.show');

    // Attendance
    Route::post('/attendance/clock-in', [AttendanceController::class, 'store'])->name('attendance.clockIn');
    Route::put('/attendance/{id}/clock-out', [AttendanceController::class, 'update'])->name('attendance.clockOut');

    // Assignments
    Route::delete('/assignment/{id}', [AssignmentController::class, 'destroy'])->name('assignment.destroy');

    // Reviews
    Route::post('/facilitator/{id}/review', [ReviewController::class, 'store'])->name('reviews.store');

    // Recommendations (Legacy/Debug specific routes if needed, otherwise integrated in dashboard)
    Route::get('/recommender/debug', [RecommendationController::class, 'dashboard']);

    // Admin
    Route::get('/admin/payments', [PaymentController::class, 'index'])->name('admin.payments');
});
