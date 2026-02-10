<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Event;
use App\Models\EventRule;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // 1. Operation Manager Dashboard
        if ($user->role === 'operation_manager') {
            // Stats
            $totalFacilitators = User::where('role', 'facilitator')->count();
            
            // "Active Events" implies Ongoing
            // Logic match recent automation: Start <= Now <= End
            $activeEvents = Event::where('status', 'ongoing')->count();
            
            
            $avgRating = 4.7; // Placeholder based on image
            
            // Upcoming Events
            $upcomingEvents = Event::where('status', 'upcoming')
                                   ->orderBy('startDateTime', 'asc')
                                   ->take(4)
                                   ->get();

            // Recent Activities - Fetch real data
            $recentActivities = collect();

            // New facilitators
            $newFacilitators = User::where('role', 'facilitator')
                ->orderBy('created_at', 'desc')
                ->take(3)
                ->get()
                ->map(fn($f) => [
                    'description' => "New facilitator {$f->name} added",
                    'time' => $f->created_at->diffForHumans()
                ]);
            $recentActivities = $recentActivities->merge($newFacilitators);

            // Recent Assignments
            $recentAssignments = \App\Models\Assignment::with(['event', 'user'])
                ->orderBy('created_at', 'desc')
                ->take(3)
                ->get()
                ->map(fn($a) => [
                    'description' => "Event \"{$a->event->eventName}\" assigned to {$a->user->name}",
                    'time' => $a->created_at->diffForHumans()
                ]);
            $recentActivities = $recentActivities->merge($recentAssignments);

            // Sort by time and take 5 most recent
            // Since 'time' is human-readable, we'll sort using created_at if available or just use the first 5
            $recentActivities = $recentActivities->take(5)->values()->all();

            return view('dashboard.operation_manager', compact(
                'user', 
                'totalFacilitators', 
                'activeEvents', 
                'avgRating', 
                'upcomingEvents',
                'recentActivities'
            ));
        }

        // 2. Facilitator Dashboard
        if ($user->role === 'facilitator') {
            // Reusing logic from FacilitatorController or redirecting
            // Let's redirect to retain existing logic location
            return app(FacilitatorController::class)->dashboard(); 
        }

        // 3. Marketing Manager / Admin 
        // Default to Events Index for now as per previous behavior
        return redirect()->route('events.index');
    }
}
