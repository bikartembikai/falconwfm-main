<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Assignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\RecommendationService;

class EventController extends Controller
{
    // List all events
    public function index(Request $request)
    {
        $query = Event::with('assignments');

        // Status Filtering
        if ($request->has('status')) {
            if ($request->status == 'pending') {
                $query->whereRaw('(select count(*) from assignments where assignments.event_id = events.id) < events.quota');
            } elseif ($request->status == 'fully_assigned') {
                 $query->whereRaw('(select count(*) from assignments where assignments.event_id = events.id) >= events.quota');
            }
        }
        
        if ($request->has('category') && $request->category != '') {
            $query->where('event_category', $request->category);
        }

        if ($request->has('search')) {
            $query->where('event_name', 'like', '%' . $request->search . '%');
        }

        $events = $query->where('status', 'upcoming')->orderBy('start_date_time', 'asc')->get();

        // Stats
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

    // Show single event details with Recommendations
    public function show($id)
    {
        $event = Event::with('assignments.user')->findOrFail($id);
        
        // Fetch Rule-Based Recommendations
        $recommender = new RecommendationService();
        $recommendations = $recommender->recommend($event);

        return view('events.show', compact('event', 'recommendations'));
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
    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);
        
        $validated = $request->validate([
            'event_name' => 'required|string|max:255',
            'venue' => 'required|string|max:255',
            'event_description' => 'nullable|string',
            'event_category' => 'required|string',
            'quota' => 'required|integer|min:1',
            'start_date_time' => 'required|date',
            'end_date_time' => 'nullable|date|after:start_date_time',
        ]);

        $event->update($validated);

        return redirect()->route('events.index')->with('success', 'Event updated successfully.');
    }

    // Delete event
    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        $event->delete();
        
        return redirect()->route('events.index')->with('success', 'Event deleted successfully.');
    }
}
