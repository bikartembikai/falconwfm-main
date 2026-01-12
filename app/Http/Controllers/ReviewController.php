<?php

namespace App\Http\Controllers;

use App\Models\PerformanceReview;
use App\Models\Facilitator;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, $facilitatorId)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'feedback_comments' => 'nullable|string',
        ]);

        PerformanceReview::create([
            'facilitator_id' => $facilitatorId,
            'rating' => $request->rating,
            'feedback_comments' => $request->feedback_comments,
            'date_submitted' => now(),
        ]);

        // Update Average Rating
        $facilitator = Facilitator::findOrFail($facilitatorId);
        $avg = $facilitator->reviews()->avg('rating');
        $facilitator->update(['average_rating' => $avg]);

        return back()->with('success', 'Review submitted.');
    }
}
