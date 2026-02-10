<?php

namespace App\Http\Controllers;

use App\Models\PerformanceReview;
use App\Models\User;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, $facilitatorId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'feedback_comments' => 'nullable|string',
            'event_id' => 'required|exists:events,eventID',
        ]);

        PerformanceReview::create([
            'userID' => $facilitatorId, // The Reviewee
            'reviewer_id' => auth()->id(), // The Reviewer
            'event_id' => $request->event_id,
            'rating' => $request->rating,
            'comments' => $request->feedback_comments,
            'dateSubmitted' => now(),
        ]);

        // Update Average Rating
        $user = \App\Models\User::findOrFail($facilitatorId);
        $avg = PerformanceReview::where('userID', $facilitatorId)->avg('rating');
        $user->update(['averageRating' => $avg]);

        return back()->with('success', 'Review submitted.');
    }
}
