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
        $query = Event::query();

        if ($request->has('category')) {
            $query->where('event_category', $request->category);
        }

        if ($request->has('search')) {
            $query->where('event_name', 'like', '%' . $request->search . '%');
        }

        $events = $query->where('status', 'upcoming')->orderBy('start_date_time', 'asc')->get();

        return view('events.index', compact('events'));
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
