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
            
            // Total Revenue - Placeholder or Sum if payment model exists
            // Assuming no robust payment model yet, hardcode or check PaymentController?
            // Existing PaymentController exists. Let's start with static/placeholder 
            // as user didn't request dynamic payments yet, just the dashboard UI.
            $totalRevenue = 245000; // Placeholder based on image
            
            $avgRating = 4.7; // Placeholder based on image
            
            // Upcoming Events
            $upcomingEvents = Event::where('status', 'upcoming')
                                   ->orderBy('startDateTime', 'asc')
                                   ->take(4)
                                   ->get();

            // Recent Activities - Mock data as requested
            $recentActivities = [
                [
                    'description' => 'New facilitator Sarah Johnson added',
                    'time' => '2 hours ago'
                ],
                [
                    'description' => 'Event "Leadership Workshop" assigned',
                    'time' => '5 hours ago'
                ],
                [
                    'description' => 'Payment processed for Michael Chen',
                    'time' => '1 day ago'
                ],
                [
                    'description' => 'Leave request approved for Emma Davis',
                    'time' => '2 days ago'
                ]
            ];

            return view('dashboard.operation_manager', compact(
                'user', 
                'totalFacilitators', 
                'activeEvents', 
                'totalRevenue', 
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
