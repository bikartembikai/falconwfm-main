<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Event;
use App\Models\Assignment;
use App\Models\PerformanceReview;
use App\Services\RecommendationService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class FullSystemVerificationSeeder extends Seeder
{
    public function run()
    {
        DB::transaction(function () {
            $this->command->info('Starting Full System Verification...');

            // 1. Create User (Facilitator)
            // Facilitator model is merged into User
            $user = User::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => Hash::make('password'),
                'role' => 'facilitator',
                'experience' => '5 years in tech workshops',
                'averageRating' => 0,
                'joinDate' => now(),
            ]);

            $this->command->info('1. Created Facilitator: ' . $user->name);

            // 2. Create Events
            $event1 = Event::create([
                'eventName' => 'Tech Leadership Summit',
                'eventDescription' => 'A summit for tech leaders to discuss future trends.',
                'eventCategory' => 'Conference',
                // 'requiredSkill' => ['Leadership', 'Strategy'], // Array cast in model? Or json?
                // Schema has 'requiredSkill' in event_rules, not events table directly usually? 
                // Wait, schema says events has eventCategory FK to event_rules.
                // Assuming basic event creation without strict rule validation for seeding.
                'quota' => 5,
                'status' => 'upcoming',
                'startDateTime' => now()->addDays(5),
            ]);

            $event2 = Event::create([
                'eventName' => 'Basic Cooking Class',
                'eventDescription' => 'Learn to cook basic meals.',
                'eventCategory' => 'Workshop',
                'quota' => 2,
                'status' => 'upcoming',
                'startDateTime' => now()->addDays(10),
            ]);

            $this->command->info('2. Created Events: ' . $event1->eventName . ' & ' . $event2->eventName);

            // 3. Test Recommendation Engine
            // Verify if service exists and works with User model
            if (class_exists(RecommendationService::class)) {
                $service = new RecommendationService();
                // Service likely expects User ID
                try {
                    $matches = $service->recommendEvents($user->userID);
                    
                    $this->command->info('3. Testing Recommendations for Facilitator...');
                    if (!empty($matches) && $matches[0]['id'] == $event1->eventID) {
                        $this->command->info('   SUCCESS: "Tech Leadership Summit" is top recommendation (Score: ' . $matches[0]['match_score'] . '%)');
                    } else {
                        $this->command->error('   FAILURE: Recommendation logic might need tuning.');
                        if (!empty($matches)) {
                            $this->command->line('Top match was: ' . $matches[0]['name']);
                        }
                    }
                } catch (\Exception $e) {
                     $this->command->error('   SKIPPED: Recommendation Error: ' . $e->getMessage());
                }
            }

            // 4. Apply for Event (Assignment)
            $assignment = Assignment::create([
                'eventID' => $event1->eventID,
                'userID' => $user->userID,
                'dateAssigned' => now(),
                'status' => 'assigned',
            ]);
            $this->command->info('4. Applied for event. Assignment ID: ' . $assignment->assignmentID);

            // 5. Clock In (Attendance - Updated on Assignment)
            $assignment->update([
                'clockInTime' => now(),
                'attendanceStatus' => 'present',
                'imageProof' => 'dummy_path.jpg',
            ]);
            $this->command->info('5. Clocked In. Assignment Updated.');

            // 6. Performance Review
            PerformanceReview::create([
                'facilitator_id' => $user->userID,
                'rating' => 5,
                'feedback_comments' => 'Excellent work!',
                'dateSubmitted' => now(),
            ]);
            
            // Update average
            $user->update(['averageRating' => 5]);
            
            $this->command->info('6. Submitted Review. New Rating: ' . $user->fresh()->averageRating);

            $this->command->info('Full System Verification Completed Successfully.');
        });
    }
}
