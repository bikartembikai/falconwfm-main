<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PerformanceReview;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class PerformanceController extends Controller
{
    // Admin View: List events with facilitators needing reviews
    public function index()
    {
        $events = \App\Models\Event::with(['assignments.user', 'assignments.reviews'])
                    ->whereHas('assignments')
                    ->orderBy('startDateTime', 'desc')
                    ->get();
        
        // Calculate stats
        $totalReviews = PerformanceReview::count();
        $pendingReviews = \App\Models\Assignment::whereDoesntHave('reviews')
                            ->where('status', 'accepted')
                            ->count();
        $completedReviews = PerformanceReview::whereNotNull('comments')->count();
        
        return view('admin.performance', compact('events', 'totalReviews', 'pendingReviews', 'completedReviews'));
    }

    // Admin: Submit a review
    public function store(Request $request)
    {
        $request->validate([
            'assignment_id' => 'required|exists:assignments,assignmentID',
            'rating' => 'required|integer|min:1|max:5',
            'comments' => 'nullable|string',
        ]);

        $assignment = \App\Models\Assignment::findOrFail($request->assignment_id);

        PerformanceReview::create([
            'assignmentID' => $request->assignment_id,
            'facilitatorID' => $assignment->userID,
            'rating' => $request->rating,
            'comments' => $request->comments,
            'reviewDate' => now(),
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

    // Facilitator View: Performance Reviews (Peer Reviews)
    public function facilitatorPerformance()
    {
        $user = Auth::user();
        
        // Get facilitator's completed events
        $completedEvents = \App\Models\Event::whereHas('assignments', function($q) use ($user) {
                $q->where('userID', $user->userID)
                  ->where('status', 'accepted');
            })
            ->where('status', 'completed')
            ->with(['assignments' => function($q) use ($user) {
                // Get co-facilitators (other facilitators in same event)
                $q->where('userID', '!=', $user->userID)
                  ->where('status', 'accepted')
                  ->with(['user', 'reviews' => function($r) use ($user) {
                      // Check if current user already reviewed this facilitator
                      $r->where('reviewerID', $user->userID);
                  }]);
            }])
            ->orderBy('startDateTime', 'desc')
            ->get();
        
        // Calculate stats
        $totalReviews = \App\Models\PerformanceReview::where('reviewerID', $user->userID)->count();
        
        // Count pending reviews (co-facilitators not yet reviewed)
        $pendingReviews = 0;
        foreach ($completedEvents as $event) {
            foreach ($event->assignments as $assignment) {
                if ($assignment->reviews->isEmpty()) {
                    $pendingReviews++;
                }
            }
        }
        
        $completedReviews = $totalReviews;
        
        return view('facilitator.performance', compact('completedEvents', 'totalReviews', 'pendingReviews', 'completedReviews'));
    }

    // Facilitator: Submit Peer Review
    public function submitPeerReview(Request $request)
    {
        $request->validate([
            'assignment_id' => 'required|exists:assignments,assignmentID',
            'rating' => 'required|integer|min:1|max:5',
            'comments' => 'required|string',
        ]);

        $user = Auth::user();
        $assignment = \App\Models\Assignment::findOrFail($request->assignment_id);
        $event = $assignment->event;

        // Validate: User must have completed the same event
        $userAssignment = \App\Models\Assignment::where('eventID', $event->eventID)
            ->where('userID', $user->userID)
            ->where('status', 'accepted')
            ->first();

        if (!$userAssignment) {
            return back()->with('error', 'You can only review facilitators from events you participated in.');
        }

        // Validate: Event must be completed
        if ($event->status !== 'completed') {
            return back()->with('error', 'You can only review facilitators after the event is completed.');
        }

        // Validate: Cannot review yourself
        if ($assignment->userID === $user->userID) {
            return back()->with('error', 'You cannot review yourself.');
        }

        // Check if already reviewed
        $existingReview = PerformanceReview::where('assignmentID', $request->assignment_id)
            ->where('reviewerID', $user->userID)
            ->first();

        if ($existingReview) {
            return back()->with('error', 'You have already reviewed this facilitator for this event.');
        }

        // Create review
        PerformanceReview::create([
            'assignmentID' => $request->assignment_id,
            'facilitatorID' => $assignment->userID,
            'reviewerID' => $user->userID,
            'rating' => $request->rating,
            'comments' => $request->comments,
            'reviewDate' => now(),
        ]);

        return back()->with('success', 'Performance review submitted successfully.');
    }
}
