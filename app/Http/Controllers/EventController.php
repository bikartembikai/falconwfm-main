<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Assignment;
use App\Models\EventRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\RecommendationService;

class EventController extends Controller
{
    // List all events
    // List all events
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect('/login');
        }

        // Automatic Status Updates based on Time (Run before any queries)
        $now = now();
        
        // Mark as Ongoing if started and not ended
        Event::where('startDateTime', '<=', $now)
             ->where('endDateTime', '>=', $now)
             ->where('status', '!=', 'ongoing')
             ->update(['status' => 'ongoing']);
             
        // Mark as Completed if ended
        Event::where('endDateTime', '<', $now)
             ->where('status', '!=', 'completed')
             ->update(['status' => 'completed']);
             
        // Mark as Upcoming if in future
        Event::where('startDateTime', '>', $now)
             ->where('status', '!=', 'upcoming')
             ->update(['status' => 'upcoming']);

        // 1. Facilitator View
        if ($user->role === 'facilitator') {
            return redirect()->route('dashboard');
        }

        // 2. Manager View
        if (!in_array($user->role, ['admin', 'operation_manager', 'marketing_manager'])) {
             abort(403, 'Unauthorized action.');
        }

        $query = Event::with('assignments');

        // Search
        if ($request->has('search') && $request->search != '') {
             $query->where('eventName', 'like', '%' . $request->search . '%')
                   ->orWhere('venue', 'like', '%' . $request->search . '%');
        }

        // Category Filter
        if ($request->has('category') && $request->category != 'All Categories' && $request->category != '') {
            $query->where('eventCategory', $request->category);
        }

        // Status Filter
        if ($request->has('status') && $request->status != 'All Status' && $request->status != '') {
            if ($request->status === 'Pending Assignment') {
                // Events where accepted assignments < quota
                $query->whereRaw('(SELECT COUNT(*) FROM assignments WHERE assignments.eventID = events.eventID AND assignments.status = "accepted") < quota');
            } elseif ($request->status === 'Fully Assigned') {
                 // Events where accepted assignments >= quota
                 $query->whereRaw('(SELECT COUNT(*) FROM assignments WHERE assignments.eventID = events.eventID AND assignments.status = "accepted") >= quota');
            } else {
                $query->where('status', strtolower($request->status));
            }
        }

        $events = $query->orderBy('startDateTime', 'asc')->get();
        $categories = EventRule::pluck('eventCategory');


        // Stats Calculation
        $totalEventsCount = Event::count();
        $scheduledCount = Event::where('status', 'upcoming')->orWhere('status', 'scheduled')->count();
        $ongoingCount = Event::where('status', 'ongoing')->count();
        $completedCount = Event::where('status', 'completed')->count();

        // Operation Manager Stats (Assignment Status)
        $eventsPermStats = Event::withCount(['assignments' => function ($query) {
            $query->where('status', 'accepted');
        }])->get(['eventID', 'quota']); // Assuming primary key is eventID based on previous code usage
        
        $fullyAssignedCount = $eventsPermStats->filter(function ($event) {
            return $event->assignments_count >= $event->quota;
        })->count();
        
        $pendingAssignmentCount = $eventsPermStats->count() - $fullyAssignedCount;
        
        $pendingResponseCount = Assignment::whereIn('status', ['assigned', 'pending'])->count();

        return view('events.index', compact('events', 'categories', 'totalEventsCount', 'scheduledCount', 'ongoingCount', 'completedCount', 'fullyAssignedCount', 'pendingAssignmentCount', 'pendingResponseCount'));
    }

    // Show single event details with Recommendations
    public function show($id)
    {
        $event = Event::with('assignments.user')->findOrFail($id);
        $user = Auth::user();

        // Fetch Rule for requirements display
        $rule = EventRule::find($event->eventCategory) ?? EventRule::find(strtoupper($event->eventCategory)); // Try exact or upper

        // 1. Marketing Manager View
        if ($user->role === 'marketing_manager') {
            if (request()->ajax() || request()->query('modal')) {
                return view('events.partials.show_marketing_modal', compact('event', 'rule'));
            }
            return view('events.show_marketing', compact('event', 'rule'));
        }
        
        // 2. Operation Manager View (Detail Focused)
        if ($user->role === 'operation_manager') {
             return view('events.show_operation', compact('event'));
        }

        // 3. Standard View (Admin) - With Recommendations
        // Fetch Rule-Based Recommendations
        $recommender = new RecommendationService();
        $recommendations = $recommender->recommend($event);

        return view('events.show', compact('event', 'recommendations'));
    }

    // Show create form
    public function create()
    {
        if (Auth::user()->role !== 'marketing_manager') {
            abort(403, 'Unauthorized action.');
        }
        $categories = EventRule::pluck('eventCategory');
        // Pass all rules to view for dynamic skill display
        $eventRules = EventRule::all()->keyBy('eventCategory');
        return view('events.create', compact('categories', 'eventRules'));
    }

    // Store new event
    public function store(Request $request)
    {
        if (Auth::user()->role !== 'marketing_manager') {
            abort(403, 'Unauthorized action.');
        }
        $validated = $request->validate([
            'eventName' => 'required|string|max:255',
            'venue' => 'required|string|max:255',
            'eventDescription' => 'nullable|string',
            'eventCategory' => 'required|string',
            'quota' => 'required|integer|min:1',
            'totalParticipants' => 'required|integer|min:1', // Added as per request
            'startDateTime' => 'required|date',
            'endDateTime' => 'nullable|date|after:startDateTime',
        ]);

        // Auto-populate requiredSkills from Category Rule
        $rule = EventRule::where('eventCategory', $validated['eventCategory'])->first();

        if ($rule) {
            $validated['requiredSkills'] = $rule->requiredSkill; // Assuming Cast array
        } else {
            $validated['requiredSkills'] = [];
        }
        Event::create($validated);

        return redirect()->route('events.index')->with('success', 'Event created successfully.');
    }
    
    // Edit Event
    public function edit($id)
    {
         if (Auth::user()->role !== 'marketing_manager') {
            abort(403, 'Unauthorized action.');
        }
         $event = Event::findOrFail($id);
         $categories = EventRule::pluck('eventCategory');
         $eventRules = EventRule::all()->keyBy('eventCategory');
         
         return view('events.edit', compact('event', 'categories', 'eventRules'));
    }

    public function update(Request $request, $id)
    {
        if (Auth::user()->role !== 'marketing_manager') {
            abort(403, 'Unauthorized action.');
        }
        $event = Event::findOrFail($id);
        
        $validated = $request->validate([
            'eventName' => 'required|string|max:255',
            'venue' => 'required|string|max:255',
            'eventDescription' => 'nullable|string',
            'eventCategory' => 'required|string',
            'quota' => 'required|integer|min:1',
            'totalParticipants' => 'required|integer|min:1',
            'startDateTime' => 'required|date',
            'endDateTime' => 'nullable|date|after:startDateTime',
        ]);

        // Recalculate skills if category changed (or just always update based on rule)
        $rule = EventRule::where('eventCategory', $validated['eventCategory'])->first();
        if ($rule) {
            $validated['requiredSkills'] = $rule->requiredSkill;
        } else {
             $validated['requiredSkills'] = [];
        }

        $event->update($validated);

        return redirect()->route('events.index')->with('success', 'Event updated successfully.');
    }

    // Delete event
    public function destroy($id)
    {
        if (Auth::user()->role !== 'marketing_manager') {
            abort(403, 'Unauthorized action.');
        }
        $event = Event::findOrFail($id);
        $event->delete();
        
        return redirect()->route('events.index')->with('success', 'Event deleted successfully.');
    }
}
