<?php

use App\Services\InferenceEngine;
use App\Models\Event;
use App\Models\Facilitator;
use App\Models\Assignment;
use App\Models\Leave;
use Carbon\Carbon;

// Load App
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$engine = new InferenceEngine();

echo "\n=== DECISION SUPPORT SYSTEM (INFERENCE ENGINE) VERIFICATION ===\n";

// ---------------------------------------------------------
// TEST CASE 1: AVAILABILITY CONSTRAINT (Assignment Conflict)
// ---------------------------------------------------------
echo "\n--- Test Case 1: Availability Constraint (Assignment Conflict) ---\n";
$event1 = Event::first(); // Assume 'Mega Team Building'
$facilitator = Facilitator::first(); 

if ($event1 && $facilitator) {
    echo "Testing Facilitator: " . $facilitator->user->name . "\n";
    echo "Event: " . $event1->event_name . " (" . $event1->start_date_time . " to " . $event1->end_date_time . ")\n";

    // 1. Assign to Event 1
    Assignment::create([
        'event_id' => $event1->id,
        'user_id' => $facilitator->user_id, // Facilitator is User
        'role' => 'Leader',
        'date_assigned' => now()
    ]);
    echo "-> Assigned {$facilitator->user->name} to {$event1->event_name}.\n";

    // 2. Create Overlapping Event
    $event2 = $event1->replicate();
    $event2->event_name = "Clash Event";
    $event2->save();
    echo "-> Created overlapping event: {$event2->event_name}\n";

    // 3. Ask Recommendation for Event 2
    $recommendations = $engine->recommend($event2);
    
    // 4. Verify Exclusion
    $found = false;
    foreach ($recommendations as $rec) {
        if ($rec['id'] == $facilitator->id) {
            $found = true;
            break;
        }
    }

    if (!$found) {
        echo "✅ PASS: Facilitator was correctly EXCLUDED due to assignment conflict.\n";
    } else {
        echo "❌ FAIL: Facilitator was RECOMMENDED despite conflict!\n";
    }

    // Cleanup
    Assignment::where('event_id', $event1->id)->delete();
    $event2->delete();
} else {
    echo "Skipping: Missing seed data.\n";
}

// ---------------------------------------------------------
// TEST CASE 2: LEAVE CONSTRAINT
// ---------------------------------------------------------
echo "\n--- Test Case 2: Availability Constraint (On Leave) ---\n";
if ($event1 && $facilitator) {
    // 1. Create Leave overlapping Event 1
    Leave::create([
        'user_id' => $facilitator->user_id,
        'start_date' => Carbon::parse($event1->start_date_time)->subDay(),
        'end_date' => Carbon::parse($event1->end_date_time)->addDay(),
        'status' => 'approved',
        'reason' => 'Sick'
    ]);
    echo "-> Put {$facilitator->user->name} on Approved Leave during event.\n";

    // 2. Recommend
    $recommendations = $engine->recommend($event1);

    // 3. Verify
    $found = false;
    foreach ($recommendations as $rec) {
        if ($rec['id'] == $facilitator->id) {
            $found = true;
            break;
        }
    }

    if (!$found) {
        echo "✅ PASS: Facilitator was correctly EXCLUDED due to Leave.\n";
    } else {
        echo "❌ FAIL: Facilitator was RECOMMENDED despite being on Leave!\n";
    }

    // Cleanup
    Leave::truncate();
}

// ---------------------------------------------------------
// TEST CASE 3: EXPERIENCE CONSTRAINT (High Risk)
// ---------------------------------------------------------
echo "\n--- Test Case 3: Experience Constraint (High Risk Event) ---\n";
// Create High Risk Event (CAMP)
$campEvent = new Event([
    'event_name' => 'Survival Camp',
    'event_category' => 'CAMP',
    'start_date_time' => now()->addMonth(),
    'end_date_time' => now()->addMonth()->addDays(2)
]);

// Create Junior Facilitator
$userJunior = \App\Models\User::factory()->create(['name' => 'Junior Staff']);
$jrFacil = Facilitator::create([
    'user_id' => $userJunior->id,
    'join_date' => now()->subMonth(), // 1 Month Experience
    'skills' => 'Survival Camping', // Has Skills
    'experience' => 'Newbie'
]);

$recs = $engine->recommend($campEvent);
$found = false;
foreach ($recs as $rec) {
    if ($rec['id'] == $jrFacil->id) $found = true;
}

if (!$found) {
    echo "✅ PASS: Junior Facilitator EXCLUDED from High Risk Camp (Tenure < 2 Years).\n";
} else {
    echo "❌ FAIL: Junior Facilitator incorrectly recommended for High Risk Camp.\n";
}

// Cleanup
$jrFacil->delete();
$userJunior->delete();

// ---------------------------------------------------------
// TEST CASE 4: RANKING (Suitability)
// ---------------------------------------------------------
echo "\n--- Test Case 4: Suitability Ranking ---\n";
$recs = $engine->recommend($event1);
echo "Make sure the list is sorted by Score (Rating + Skills):\n";
foreach ($recs as $rec) {
    echo "   -> [Score: {$rec['match_score']}] {$rec['name']} (Rating: {$rec['rating']})\n";
}
