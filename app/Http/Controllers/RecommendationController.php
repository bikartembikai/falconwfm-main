<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
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
        $requirements = $event->event_name . ' ' . $event->event_description . ' ' . $event->skills;

        // Call the PHP Service
        $recommendations = $this->recommender->recommend($requirements);

        return view('recommendations.index', [
            'event' => $event,
            'recommendations' => $recommendations
        ]);
    }

    public function dashboard()
    {
        $events = Event::all();
        $data = [];

        foreach ($events as $event) {
            $requirements = $event->event_name . ' ' . $event->event_description . ' ' . $event->skills;
            // Get top 3 recommendations
            $matches = $this->recommender->recommend($requirements, 3);
            
            $data[] = [
                'event' => $event,
                'matches' => $matches
            ];
        }

        return view('recommendations.dashboard', ['dashboardData' => $data]);
    }
}