<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RecommendationController;

Route::get('/', function () {
    return view('dashboard');
});

Route::get('/test', function () {
    return view('test');
});


// Recommender Routes
Route::get('/recommender/dashboard', [RecommendationController::class, 'dashboard']);
Route::get('/events/{event}/recommendations', [RecommendationController::class, 'show']);
