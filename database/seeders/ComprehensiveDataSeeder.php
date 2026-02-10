<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Event;
use App\Models\Assignment;
use App\Models\Leave;
use App\Models\Payment;
use App\Models\PerformanceReview;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ComprehensiveDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Starting comprehensive data seeding...');

        // 1. Ensure Facilitators Exist (should be seeded by RealFacilitatorSeeder)
        $facilitators = User::where('role', 'facilitator')->get();
        if ($facilitators->isEmpty()) {
            $this->command->info('No facilitators found. Seeding RealFacilitatorSeeder...');
            $this->call(RealFacilitatorSeeder::class);
            $facilitators = User::where('role', 'facilitator')->get();
        }

        // 2. Ensure Events Exist
        if (Event::count() == 0) {
            $this->command->info('No events found. Creating sample events...');
            $this->createEvents();
        }
        $events = Event::all();

        // 3. Create Assignments & Related Data
        foreach ($events as $event) {
            // Assign 3-5 random facilitators to each event
            $assignedFacilitators = $facilitators->random(min(rand(3, 5), $facilitators->count()));

            foreach ($assignedFacilitators as $facilitator) {
                // Determine logic based on event status
                if ($event->startDateTime < Carbon::now()->subDay()) {
                    // Past Event (Completed)
                    $this->createCompletedAssignmentData($event, $facilitator);
                } elseif ($event->startDateTime >= Carbon::now()->startOfDay() && $event->startDateTime <= Carbon::now()->endOfDay()) {
                    // Ongoing Event (Today)
                    $this->createOngoingAssignmentData($event, $facilitator);
                } else {
                    // Upcoming Event
                    $this->createUpcomingAssignment($event, $facilitator);
                }
            }
        }

        // 4. Create Leave Requests
        foreach ($facilitators as $facilitator) {
            if (rand(0, 100) < 30) { // 30% chance
                $status = ['pending', 'approved', 'rejected'][rand(0, 2)];
                Leave::create([
                    'userID' => $facilitator->userID,
                    'startDate' => Carbon::now()->addDays(rand(10, 20)),
                    'endDate' => Carbon::now()->addDays(rand(21, 25)),
                    'reason' => 'Personal matters',
                    'status' => $status,
                    'created_at' => Carbon::now()->subDays(rand(1, 5)),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }

        $this->command->info('Comprehensive data seeding completed!');
    }

    private function createEvents()
    {
        // Past Events
        Event::create([
            'eventName' => 'Leadership Summit 2025',
            'venue' => 'Grand Hyatt KL',
            'eventDescription' => 'Annual leadership conference.',
            'eventCategory' => 'Talk',
            'quota' => 5,
            'status' => 'completed',
            'startDateTime' => Carbon::now()->subDays(10)->setTime(9, 0),
            'endDateTime' => Carbon::now()->subDays(10)->setTime(17, 0),
        ]);

        Event::create([
            'eventName' => 'Team Building: Jungle Trek',
            'venue' => 'Taman Negara',
            'eventDescription' => 'Outdoor survival and team building.',
            'eventCategory' => 'Team Building',
            'quota' => 8,
            'status' => 'completed',
            'startDateTime' => Carbon::now()->subDays(20)->setTime(7, 0),
            'endDateTime' => Carbon::now()->subDays(20)->setTime(18, 0),
        ]);

        // Ongoing (Today)
        Event::create([
            'eventName' => 'Digital Marketing Workshop',
            'venue' => 'KLCC Convention Center',
            'eventDescription' => 'Practical workshop on SEO and SEM.',
            'eventCategory' => 'Workshop',
            'quota' => 4,
            'status' => 'ongoing',
            'startDateTime' => Carbon::now()->setTime(9, 0),
            'endDateTime' => Carbon::now()->setTime(17, 0),
        ]);

        // Upcoming
        Event::create([
            'eventName' => 'Annual Charity Gala',
            'venue' => 'Mandarin Oriental',
            'eventDescription' => 'Charity dinner and auction.',
            'eventCategory' => 'Holiday',
            'quota' => 6,
            'status' => 'upcoming',
            'startDateTime' => Carbon::now()->addDays(5)->setTime(19, 0),
            'endDateTime' => Carbon::now()->addDays(5)->setTime(23, 0),
        ]);
        
        Event::create([
            'eventName' => 'Youth Coding Camp',
            'venue' => 'Cyberjaya',
            'eventDescription' => 'Coding bootcamp for high school students.',
            'eventCategory' => 'Camp',
            'quota' => 10,
            'status' => 'upcoming',
            'startDateTime' => Carbon::now()->addDays(14)->setTime(8, 0),
            'endDateTime' => Carbon::now()->addDays(16)->setTime(17, 0),
        ]);
    }

    private function createCompletedAssignmentData($event, $facilitator)
    {
        // Create Assignment with Attendance info merged (since no separate table)
        // Schema: assignments table has clockInTime, clockOutTime, attendanceStatus, imageProof
        $assignment = Assignment::firstOrCreate(
            [
                'eventID' => $event->eventID,
                'userID' => $facilitator->userID,
            ],
            [
                'dateAssigned' => Carbon::now()->subDays(rand(15, 40)),
                'status' => 'accepted', // This is assignment status
                'attendanceStatus' => 'verified',
                'clockInTime' => Carbon::parse($event->startDateTime)->subMinutes(rand(10, 30)),
                'clockOutTime' => Carbon::parse($event->endDateTime)->addMinutes(rand(0, 30)),
                'imageProof' => $this->getRandomProofImage(),
            ]
        );

        // Payment
        Payment::create([
            'assignmentID' => $assignment->assignmentID,
            // 'facilitatorID' => $facilitator->userID, // Schema check: payments has foreignId assignmentID, usually no facilitatorID if linked via assignment?
            // Actually let's check migration: `payments` has `assignmentID`. Does it have `facilitatorID`? 
            // Migration line 115: create('payments'... -> foreignId('assignmentID')...
            // It does NOT have foreignId('userID') or 'facilitatorID'. 
            // Wait, previous seeder code used it. I should check if I missed it in migration view.
            // Migration view: foreignId('assignmentID')... table->decimal('amount')...
            // NO userID in payments table.
            'amount' => rand(200, 600),
            'paymentStatus' => 'paid',
            'paymentDate' => Carbon::parse($event->endDateTime)->addDays(rand(1, 5)),
            // 'paymentMethod' => 'Bank Transfer', // schema check: not in migration
            // 'remarks' => 'Settled', // schema check: not in migration
            'paymentProof' => $this->getRandomProofImage(),
        ]);

        // Review
        // PerformanceReviews table: userID, rating, comments, dateSubmitted
        PerformanceReview::create([
            'userID' => $facilitator->userID,
            'rating' => rand(3, 5),
            'comments' => $this->getRandomReviewComment(),
            'dateSubmitted' => Carbon::parse($event->endDateTime)->addDays(rand(1, 3)),
        ]);
    }

    private function createOngoingAssignmentData($event, $facilitator)
    {
        $isCheckedIn = rand(0, 100) < 80;
        
        Assignment::firstOrCreate(
            [
                'eventID' => $event->eventID,
                'userID' => $facilitator->userID,
            ],
            [
                'dateAssigned' => Carbon::now()->subDays(rand(2, 10)),
                'status' => 'accepted',
                'attendanceStatus' => $isCheckedIn ? 'pending' : 'absent',
                'clockInTime' => $isCheckedIn ? Carbon::now()->subHours(rand(1, 4)) : null,
                'clockOutTime' => null,
                'imageProof' => $isCheckedIn ? $this->getRandomProofImage() : null,
            ]
        );
    }

    private function createUpcomingAssignment($event, $facilitator)
    {
        Assignment::firstOrCreate(
            [
                'eventID' => $event->eventID,
                'userID' => $facilitator->userID,
            ],
            [
                'dateAssigned' => Carbon::now()->subDays(rand(1, 5)),
                'status' => ['pending', 'accepted'][rand(0, 1)],
                'attendanceStatus' => 'pending',
                'clockInTime' => null,
                'clockOutTime' => null,
                'imageProof' => null,
            ]
        );
    }

    private function getRandomProofImage()
    {
        // Just return a dummy path string
        return 'proofs/' . Str::random(10) . '.jpg';
    }

    private function getRandomReviewComment()
    {
        $comments = [
            'Excellent engagement with participants.',
            'Arrived on time and prepared.',
            'Good delivery but allow more time for Q&A.',
            'Outstanding energy!',
            'Professional and reliable.',
            'Great team player.',
        ];
        return $comments[array_rand($comments)];
    }
}
