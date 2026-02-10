<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Assignment;
use App\Models\Event;

class AssignmentController extends Controller
{
    /**
     * Display a listing of assignments for the logged-in facilitator
     */
    public function index()
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        if (!$user) return redirect('/login');

        // fetch assignments (assigned, pending, accepted)
        $assignments = Assignment::where('userID', $user->userID)
                                 ->with(['event' => function($q) {
                                     $q->withCount([
                                         'assignments as total_assigned',
                                         'assignments as accepted_count' => function($q) {
                                             $q->where('status', 'accepted');
                                         }
                                     ]);
                                 }])
                                 ->whereIn('status', ['assigned', 'pending', 'accepted'])
                                 ->orderBy('dateAssigned', 'desc')
                                 ->get();
        
        // fetch history (completed, rejected)
        // Ideally linked via Attendance for "Completed" status or Assignment status
        $history = Assignment::where('userID', $user->userID) 
                             ->with('event')
                             ->whereIn('status', ['completed', 'rejected'])
                             ->orderBy('updated_at', 'desc')
                             ->get();

        return view('facilitator.assignments', compact('assignments', 'history'));
    }

    /**
     * Store a new assignment (Admin Assign)
     */
    /**
     * Show the assignment creation form for a specific event
     */
    public function create(Event $event)
    {
        $engine = new \App\Services\InferenceEngine();
        $facilitators = $engine->analyzeFacilitators($event);

        return view('assignments.create', compact('event', 'facilitators'));
    }

    /**
     * Store a new assignment (Admin Assign)
     * Supports single or bulk assignment
     */
    public function store(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,eventID',
            'facilitator_ids' => 'required|array',
            'facilitator_ids.*' => 'exists:users,userID', // Facilitators are Users
        ]);

        $event = Event::find($request->event_id);
        $count = 0;

        foreach ($request->facilitator_ids as $facilId) {
            $facilitator = \App\Models\User::find($facilId);

            // Check if already assigned
            if ($event->assignments()->where('userID', $facilitator->userID)->exists()) {
                continue; // Skip if already assigned
            }

            // Check quota
            if ($event->assignments()->count() >= $event->quota) {
                return back()->with('warning', "Event quota filled. Assigned $count facilitators.");
            }

            Assignment::create([
                'eventID' => $event->eventID,
                'userID' => $facilitator->userID,
                'role' => 'Facilitator', // Default role
                'dateAssigned' => now(),
                'status' => 'pending', // Explicit pending status for tracking
            ]);
            $count++;
        }

        return redirect()->route('events.index')->with('success', "Successfully assigned $count facilitators.");
    }

    public function destroy($id)
    {
        $assignment = Assignment::findOrFail($id);
        $assignment->delete();
        return back()->with('success', 'Assignment removed.');
    }

    /**
     * Accept an assignment (Facilitator action)
     */
    public function accept(Assignment $assignment)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        
        // Ensure the assignment belongs to the current user
        if ($assignment->userID !== $user->userID) {
            abort(403);
        }

        $assignment->update(['status' => 'accepted']);
        return back()->with('success', 'Assignment accepted successfully.');
    }

    /**
     * Decline an assignment (Facilitator action)
     */
    public function decline(Assignment $assignment)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        
        // Ensure the assignment belongs to the current user
        if ($assignment->userID !== $user->userID) {
            abort(403);
        }

        $assignment->update(['status' => 'rejected']);
        return back()->with('success', 'Assignment declined.');
    }
}
