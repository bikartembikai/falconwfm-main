<?php

use App\Services\RecommendationService;
use App\Models\Event;
use App\Models\Facilitator;

// Load App
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$service = new RecommendationService();

echo "=== RULE-BASED RECOMMENDATION SYSTEM VERIFICATION ===\n\n";

// 1. Verify Event->Facilitator
echo "--- 1. Recommending Facilitators for specific Events ---\n";
$events = Event::whereIn('event_category', ['TEAM BUILDING', 'CERAMAH', 'KEM'])->take(3)->get();

foreach ($events as $event) {
    echo "\n[Event] ID: {$event->id} | Name: {$event->event_name} | Category: {$event->event_category}\n";
    $recommendations = $service->recommend($event);
    
    if (empty($recommendations)) {
        echo "   No matches found.\n";
    } else {
        foreach ($recommendations as $rec) {
            echo "   -> [{$rec['match_score']} Matches] Facilitator: {$rec['name']} (Matched: {$rec['matched_keywords']})\n";
        }
    }
}

// 2. Verify Facilitator->Event
echo "\n\n--- 2. Recommending Events for specific Facilitators ---\n";
$facilitators = Facilitator::with('user')->take(3)->get();

foreach ($facilitators as $facil) {
    echo "\n[Facilitator] Name: {$facil->user->name} | Skills: {$facil->skills}\n";
    $recommendations = $service->recommendEvents($facil->id);

    if (empty($recommendations)) {
        echo "   No relevant events found.\n";
    } else {
        foreach ($recommendations as $rec) {
            echo "   -> [{$rec['match_score']} Matches] Event: {$rec['name']} ({$rec['category']}) (Matched: {$rec['matched_keywords']})\n";
        }
    }
}
