<?php

use App\Models\Event;
use App\Models\Facilitator;
use App\Models\User;
use App\Services\RecommendationService;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- Verifying Recommendation Logic with Database Rules ---\n";

// 1. Setup Test Data
DB::transaction(function () {
    // Create Event
    $event = new Event();
    $event->event_name = 'Survival Camp 2026';
    $event->event_description = 'A tough outdoor camp.';
    $event->event_category = 'CAMP'; // Should match EventRule 'CAMP' (Spec: Outdoor Activities)
    $event->start_date_time = now()->addDays(10);
    $event->status = 'upcoming';
    $event->save();

    // Create Facilitator A (Perfect Match)
    $userA = User::factory()->create(['name' => 'Facilitator A (Perfect)']);
    $facilA = Facilitator::create([
        'user_id' => $userA->id,
        'skills' => 'Survival, Medic, Hiking', // Matches skills
        'specialization' => 'Outdoor Activities', // Matches specialization (+5)
        'average_rating' => 5,
        'experience' => '5 Years'
    ]);

    // Create Facilitator B (Weak Match)
    $userB = User::factory()->create(['name' => 'Facilitator B (Weak)']);
    $facilB = Facilitator::create([
        'user_id' => $userB->id,
        'skills' => 'Cooking, Medic', // Matches 1 skill
        'specialization' => 'Culinary Arts', // No spec match
        'average_rating' => 3,
        'experience' => '2 Years'
    ]);

    // 2. Run Recommendation
    echo "\nTesting recommend() for Event ID: {$event->id} ({$event->event_category})...\n";
    $service = new RecommendationService();
    $results = $service->recommend($event);

    foreach ($results as $params) {
        echo "Found: {$params['name']} - Score: {$params['match_score']} ({$params['matched_keywords']})\n";
    }

    // Verify
    if (count($results) > 0 && $results[0]['id'] == $facilA->id) {
        echo "\nSUCCESS: Facilitator A ranked first.\n";
    } else {
        echo "\nFAILURE: Facilitator A did not rank first.\n";
    }

    // Rollback
    // DB::rollBack(); // Keep it for manual inspection if needed, or rollback to clean up. 
    // Let's rollback to keep DB clean.
    throw new Exception("Test Complete, Rolling Back");
});
