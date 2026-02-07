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
            'facilitator_id' => $facilitatorId, // The Reviewee
            'reviewer_id' => auth()->id(), // The Reviewer
            'event_id' => $request->event_id,
            'rating' => $request->rating,
            'feedback_comments' => $request->feedback_comments,
            'date_submitted' => now(),
        ]);

        // Update Average Rating
        $user = \App\Models\User::findOrFail($facilitatorId);
        // Note: PerformanceReview model 'user' relationship points to reviewee (facilitator_id)
        // We can query using where('facilitator_id', $facilitatorId)
        $avg = PerformanceReview::where('facilitator_id', $facilitatorId)->avg('rating');
        $user->update(['averageRating' => $avg]);

        return back()->with('success', 'Review submitted.');
    }
}
