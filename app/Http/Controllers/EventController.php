<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Assignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    // List all events
    public function index(Request $request)
    {
        $query = Event::with('assignments');

        // Status Filtering
        if ($request->has('status')) {
            if ($request->status == 'pending') {
                // Events where assigned count < quota
                $query->whereHas('assignments', function($q) {
                    $q->select('event_id');
                }, '<', \DB::raw('events.quota'));
            } elseif ($request->status == 'fully_assigned') {
                 // Events where assigned count >= quota
                 // Note: simpler to just fetch all and filter in collection if dataset is small, 
                 // but using has/doesntHave is better for SQL. 
                 // However, comparing count to column value in whereHas is tricky in basic Eloquent.
                 // For now, let's fetch and filter collection processing for simplicity unless performance is critical,
                 // or use raw where clause.
                 $query->whereRaw('(select count(*) from assignments where assignments.event_id = events.id) >= events.quota');
            }
        }
        
        // Also apply the pending filter using raw sql for consistency if selected
        if ($request->status == 'pending') {
             $query->whereRaw('(select count(*) from assignments where assignments.event_id = events.id) < events.quota');
        }


        if ($request->has('category') && $request->category != '') {
            $query->where('event_category', $request->category);
        }

        if ($request->has('search')) {
            $query->where('event_name', 'like', '%' . $request->search . '%');
        }

        $events = $query->where('status', 'upcoming')->orderBy('start_date_time', 'asc')->get();

        // Calculate Stats (Global, not filtered by search/category for the top cards typically, but user might want filtered stats. 
        // Desing implies global stats. Let's do global stats for upcoming events.)
        $allUpcoming = Event::where('status', 'upcoming')->withCount('assignments')->get();
        
        $totalEvents = $allUpcoming->count();
        $pendingAssignment = $allUpcoming->filter(function($e) {
            return $e->assignments_count < $e->quota;
        })->count();
        $fullyAssigned = $allUpcoming->filter(function($e) {
            return $e->assignments_count >= $e->quota;
        })->count();

        return view('events.index', compact('events', 'totalEvents', 'pendingAssignment', 'fullyAssigned'));
    }

    // Show single event details
    public function show($id)
    {
        $event = Event::with('assignments.user')->findOrFail($id);
        return view('events.show', compact('event'));
    }

    // Show create form
    public function create()
    {
        return view('events.create');
    }

    // Store new event
    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_name' => 'required|string|max:255',
            'venue' => 'required|string|max:255',
            'event_description' => 'nullable|string',
            'event_category' => 'required|string',
            'required_skill_tag' => 'nullable|string',
            'quota' => 'required|integer|min:1',
            'start_date_time' => 'required|date',
            'end_date_time' => 'nullable|date|after:start_date_time',
        ]);

        Event::create($validated);

        return redirect()->route('events.index')->with('success', 'Event created successfully.');
    }

    // Apply for an event (Facilitator)
    public function apply(Request $request, $id)
    {
        // Ideally checking if user is facilitator
        // For now basics:
        $user = Auth::user(); // Assuming auth is working or we might simulate user 1 for dev
        if (!$user) {
             // Fallback for dev without auth setup: assume user 1
             $user = \App\Models\User::first(); 
        }

        $event = Event::findOrFail($id);

        // Check quota
        if ($event->assignments()->count() >= $event->quota) {
            return back()->with('error', 'Event quota full.');
        }

        // Check if already assigned
        if ($event->assignments()->where('user_id', $user->id)->exists()) {
             return back()->with('info', 'You have already applied.');
        }

        Assignment::create([
            'event_id' => $event->id,
            'user_id' => $user->id,
            'role' => 'Facilitator', // Default role
            'date_assigned' => now(),
        ]);

        return back()->with('success', 'Application submitted!');
    }
}
