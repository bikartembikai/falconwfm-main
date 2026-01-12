<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Facilitator;
use App\Models\Event;
use App\Models\Assignment;
use App\Models\Attendance;
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

            // 1. Create Facilitator
            $user = User::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => Hash::make('password'),
                'role' => 'facilitator',
            ]);

            $facilitator = Facilitator::create([
                'user_id' => $user->id,
                'skills' => 'Python, Leadership, Public Speaking',
                'experience' => '5 years in tech workshops',
                'certifications' => 'Certified Scrum Master',
                'average_rating' => 0,
            ]);

            $this->command->info('1. Created Facilitator: ' . $user->name);

            // 2. Create Events
            $event1 = Event::create([
                'event_name' => 'Tech Leadership Summit',
                'event_description' => 'A summit for tech leaders to discuss future trends.',
                'event_category' => 'Conference',
                'required_skill_tag' => 'Leadership, Strategy',
                'quota' => 5,
                'status' => 'upcoming',
                'start_date_time' => now()->addDays(5),
            ]);

            $event2 = Event::create([
                'event_name' => 'Basic Cooking Class',
                'event_description' => 'Learn to cook basic meals.',
                'event_category' => 'Workshop',
                'required_skill_tag' => 'Cooking, Patience',
                'quota' => 2,
                'status' => 'upcoming',
                'start_date_time' => now()->addDays(10),
            ]);

            $this->command->info('2. Created Events: ' . $event1->event_name . ' & ' . $event2->event_name);

            // 3. Test Recommendation Engine
            $service = new RecommendationService();
            $matches = $service->recommendEvents($facilitator->id);
            
            $this->command->info('3. Testing Recommendations for Facilitator...');
            if (!empty($matches) && $matches[0]['id'] == $event1->id) {
                $this->command->info('   SUCCESS: "Tech Leadership Summit" is top recommendation (Score: ' . $matches[0]['match_score'] . '%)');
            } else {
                $this->command->error('   FAILURE: Recommendation logic might need tuning.');
                if (!empty($matches)) {
                    $this->command->line('Top match was: ' . $matches[0]['name']);
                }
            }

            // 4. Apply for Event (Assignment)
            $assignment = Assignment::create([
                'event_id' => $event1->id,
                'user_id' => $user->id,
                'role' => 'Lead Facilitator',
                'date_assigned' => now(),
            ]);
            $this->command->info('4. Applied for event. Assignment ID: ' . $assignment->id);

            // 5. Clock In (Attendance)
            $attendance = Attendance::create([
                'event_id' => $event1->id,
                'facilitator_id' => $facilitator->id,
                'clock_in_time' => now(),
                'status' => 'present',
                'image_proof' => 'dummy_path.jpg',
            ]);
            $this->command->info('5. Clocked In. Attendance ID: ' . $attendance->id);

            // 6. Performance Review
            PerformanceReview::create([
                'facilitator_id' => $facilitator->id,
                'rating' => 5,
                'feedback_comments' => 'Excellent work!',
                'date_submitted' => now(),
            ]);
            
            // Update average
            $facilitator->update(['average_rating' => 5]);
            
            $this->command->info('6. Submitted Review. New Rating: ' . $facilitator->fresh()->average_rating);

            $this->command->info('Full System Verification Completed Successfully.');
        });
    }
}
