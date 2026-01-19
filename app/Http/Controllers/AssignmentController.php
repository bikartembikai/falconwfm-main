<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Assignment;
use App\Models\Facilitator;
use App\Models\Event;

class AssignmentController extends Controller
{
    /**
     * Store a new assignment (Admin Assign)
     */
    public function store(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'facilitator_id' => 'required|exists:facilitators,id',
        ]);

        $facilitator = Facilitator::find($request->facilitator_id);
        $event = Event::find($request->event_id);

        // Check if already assigned
        if ($event->assignments()->where('user_id', $facilitator->user_id)->exists()) {
            return back()->with('error', 'Facilitator is already assigned.');
        }

        // Check quota (Optional: Admin might want to override, but let's keep it for now)
        if ($event->assignments()->count() >= $event->quota) {
            return back()->with('error', 'Event quota is full.');
        }

        Assignment::create([
            'event_id' => $event->id,
            'user_id' => $facilitator->user_id,
            'role' => 'Facilitator', // Default role
            'date_assigned' => now(),
        ]);

        return back()->with('success', "Assigned {$facilitator->user->name} successfully.");
    }

    public function destroy($id)
    {
        $assignment = Assignment::findOrFail($id);
        $assignment->delete();
        return back()->with('success', 'Assignment removed.');
    }
}
