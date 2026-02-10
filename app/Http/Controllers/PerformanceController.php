<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PerformanceReview;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class PerformanceController extends Controller
{
    // Admin View: List facilitators and manage reviews
    public function index()
    {
        $facilitators = User::where('role', 'facilitator')
                            ->with('reviews')
                            ->orderBy('name')
                            ->get();
        
        return view('admin.performance', compact('facilitators'));
    }

    // Admin: Submit a review
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,userID',
            'rating' => 'required|integer|min:1|max:5',
            'comments' => 'nullable|string',
        ]);

        PerformanceReview::create([
            'userID' => $request->user_id,
            'rating' => $request->rating,
            'comments' => $request->comments,
            'dateSubmitted' => now(),
        ]);

        return back()->with('success', 'Performance review submitted successfully.');
    }

    // Facilitator View: My Reviews
    public function myReviews()
    {
        $user = Auth::user();
        $reviews = $user->reviews()->orderBy('created_at', 'desc')->get();
        $averageRating = $user->averageRating;

        return view('facilitator.reviews', compact('reviews', 'averageRating'));
    }
}
