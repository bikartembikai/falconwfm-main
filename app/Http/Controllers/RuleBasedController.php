<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\User;
use App\Services\RecommendationService;

class RuleBasedController extends Controller
{
    protected $recommender;

    // Inject the Service automatically
    public function __construct(RecommendationService $recommender)
    {
        $this->recommender = $recommender;
    }

    // Show recommendations for a specific event
    public function show($eventId)
    {
        $event = Event::findOrFail($eventId);
        
        // Pass the event object directly as the service now expects an object with specific properties
        $recommendations = $this->recommender->recommend($event);

        return view('recommendations.index', [
            'event' => $event,
            'recommendations' => $recommendations
        ]);
    }

    // Admin/Manager Matching Dashboard
    public function dashboard()
    {
        // 1. Get Upcoming Events and their Facilitator matches
        $events = Event::where('status', 'upcoming')->get();
        $eventData = [];

        foreach ($events as $event) {
            $matches = $this->recommender->recommend($event, 3);
            $eventData[] = ['event' => $event, 'matches' => $matches];
        }

        // 2. Get Facilitators and their Event matches
        $facilitators = User::where('role', 'facilitator')->take(5)->get();
        $facilitatorData = [];
        
        foreach ($facilitators as $facil) {
            $matches = $this->recommender->recommendEvents($facil->userID, 3);
            $facilitatorData[] = ['facilitator' => $facil, 'matches' => $matches];
        }

        return view('recommendations.dashboard', [
            'dashboardData' => $eventData, 
            'facilitatorData' => $facilitatorData
        ]);
    }
}
