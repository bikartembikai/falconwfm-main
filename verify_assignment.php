<?php

use App\Models\Event;
use App\Models\Facilitator;
use App\Models\Assignment;
use Illuminate\Http\Request;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== VERIFYING ADMIN ASSIGNMENT WORKFLOW ===\n";

// 1. Pick an Event
$event = Event::first();
echo "[Context] Event: {$event->event_name} (Quota: {$event->assignments()->count()}/{$event->quota})\n";

// 2. Pick a Facilitator (Not yet assigned)
$assignedIds = $event->assignments->pluck('user_id');
$facilitator = Facilitator::with('user')->whereNotIn('user_id', $assignedIds)->first();

if (!$facilitator) {
    echo "No available facilitator to test.\n";
    exit;
}
echo "[Context] Facilitator: {$facilitator->user->name} (ID: {$facilitator->id})\n";

// 3. Simulate Assignment Request
$controller = new \App\Http\Controllers\AssignmentController();
$request = Request::create('/assignments', 'POST', [
    'event_id' => $event->id,
    'facilitator_id' => $facilitator->id
]);

// Mock bindings to avoid session crash if possible, or just catch it
try {
    echo "[Action] Attempting to store assignment...\n";
    $controller->store($request);
} catch (\Throwable $e) {
    // We expect a Session or Redirect error because we didn't fully mock the browser
    // But if logic worked, DB should have record.
    echo "[Note] Request finished (expecting redirect error): " . $e->getMessage() . "\n";
}

// Check DB
$exists = Assignment::where('event_id', $event->id)
            ->where('user_id', $facilitator->user_id)
            ->exists();
            
if ($exists) {
    echo "[SUCCESS] Assignment created in Database!\n";
} else {
    echo "[FAILURE] Assignment NOT found in Database.\n";
}
