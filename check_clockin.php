<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Assignment;
use Carbon\Carbon;

echo "=== Facilitators Who Need to Clock In ===\n\n";
echo "Current Date/Time: " . now()->format('Y-m-d H:i:s') . "\n\n";

// Get assignments that need clock-in
$assignments = Assignment::where('status', 'accepted')
    ->where('attendanceStatus', 'pending')
    ->with(['user', 'event'])
    ->get()
    ->filter(function($assignment) {
        return $assignment->event 
            && $assignment->event->startDateTime <= now()
            && $assignment->event->status != 'Completed';
    });

if ($assignments->isEmpty()) {
    echo "No facilitators currently need to clock in for ongoing events.\n";
} else {
    echo "Total: " . $assignments->count() . " facilitators need to clock in\n\n";
    
    foreach ($assignments as $assignment) {
        echo "• " . $assignment->user->name . "\n";
        echo "  Event: " . $assignment->event->eventName . "\n";
        echo "  Start: " . $assignment->event->startDateTime->format('Y-m-d H:i') . "\n";
        echo "  Status: " . $assignment->event->status . "\n";
        echo "  Attendance: " . $assignment->attendanceStatus . "\n\n";
    }
}

// Show upcoming events summary
echo "\n=== Upcoming Events (Next 7 Days) ===\n\n";
$upcomingAssignments = Assignment::where('status', 'accepted')
    ->with(['user', 'event'])
    ->get()
    ->filter(function($assignment) {
        return $assignment->event 
            && $assignment->event->startDateTime > now()
            && $assignment->event->startDateTime <= now()->addDays(7);
    })
    ->groupBy(function($assignment) {
        return $assignment->event->eventName;
    });

foreach ($upcomingAssignments as $eventName => $assignments) {
    $firstAssignment = $assignments->first();
    echo "Event: " . $eventName . "\n";
    echo "Start: " . $firstAssignment->event->startDateTime->format('Y-m-d H:i') . "\n";
    echo "Facilitators (" . $assignments->count() . "): " . $assignments->pluck('user.name')->implode(', ') . "\n\n";
}
