<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Facilitator;
use App\Services\RecommendationService; // Import your new Service

class RecommendationController extends Controller
{
    protected $recommender;

    // Inject the Service automatically
    public function __construct(RecommendationService $recommender)
    {
        $this->recommender = $recommender;
    }

    public function show($eventId)
    {
        $event = Event::findOrFail($eventId);
        
        // Combine text for the query
        $requirements = $event->event_name . ' ' . $event->required_skill_tag;

        // Call the PHP Service
        $recommendations = $this->recommender->recommend($requirements);

        return view('recommendations.index', [
            'event' => $event,
            'recommendations' => $recommendations
        ]);
    }

    public function dashboard()
    {
        // 1. Get Events and their Facilitator matches
        $events = Event::all();
        $eventData = [];

        foreach ($events as $event) {
            $requirements = $event->event_name . ' ' . $event->required_skill_tag;
            $matches = $this->recommender->recommend($requirements, 3);
            $eventData[] = ['event' => $event, 'matches' => $matches];
        }

        // 2. Get Facilitators and their Event matches (New Feature)
        $facilitators = Facilitator::with('user')->take(5)->get(); // Limit to 5 for demo
        $facilitatorData = [];
        
        foreach ($facilitators as $facil) {
            $matches = $this->recommender->recommendEvents($facil->id, 3);
            $facilitatorData[] = ['facilitator' => $facil, 'matches' => $matches];
        }

        return view('recommendations.dashboard', [
            'dashboardData' => $eventData, 
            'facilitatorData' => $facilitatorData
        ]);
    }
}