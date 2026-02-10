<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use App\Models\Assignment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AttendanceTestSeeder extends Seeder
{
    /**
     * Seed attendance test data:
     * - 6 facilitators
     * - Events: 2 upcoming, 2 ongoing (today), 3 completed
     * - Assignments with various clock states
     * - 2 facilitators clocked in but not clocked out (for testing)
     */
    public function run(): void
    {
        // Create test facilitators
        $facilitators = [];
        $facilitatorData = [
            ['name' => 'Ahmad Faiz', 'email' => 'ahmad.faiz@falcon.test'],
            ['name' => 'Nurul Aisyah', 'email' => 'nurul.aisyah@falcon.test'],
            ['name' => 'Muhammad Hafiz', 'email' => 'hafiz@falcon.test'],
            ['name' => 'Siti Aminah', 'email' => 'siti.aminah@falcon.test'],
            ['name' => 'Zulkifli Rahman', 'email' => 'zulkifli@falcon.test'],
            ['name' => 'Fatimah Zahra', 'email' => 'fatimah@falcon.test'],
        ];

        foreach ($facilitatorData as $data) {
            $facilitators[] = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password123'),
                    'role' => 'facilitator',
                    'phoneNumber' => '01' . rand(10000000, 99999999),
                    'joinDate' => now()->subMonths(rand(1, 12)),
                    'averageRating' => rand(35, 50) / 10,
                ]
            );
        }

        // Create Operation Manager
        User::firstOrCreate(
            ['email' => 'manager@falcon.test'],
            [
                'name' => 'Operation Manager',
                'password' => Hash::make('password123'),
                'role' => 'operation_manager',
            ]
        );

        // ============ COMPLETED EVENTS (Past) ============
        $completedEvent1 = Event::create([
            'eventName' => 'Corporate Leadership Workshop',
            'venue' => 'Menara KLCC, Hall A',
            'eventDescription' => 'Leadership training for senior executives',
            'eventCategory' => 'Workshop',
            'status' => 'completed',
            'quota' => 3,
            'startDateTime' => now()->subDays(7)->setTime(9, 0),
            'endDateTime' => now()->subDays(7)->setTime(17, 0),
            'totalParticipants' => 45,
        ]);

        $completedEvent2 = Event::create([
            'eventName' => 'Team Building Retreat',
            'venue' => 'Port Dickson Beach Resort',
            'eventDescription' => 'Annual team building activity',
            'eventCategory' => 'Team Building',
            'status' => 'completed',
            'quota' => 4,
            'startDateTime' => now()->subDays(14)->setTime(8, 0),
            'endDateTime' => now()->subDays(14)->setTime(18, 0),
            'totalParticipants' => 80,
        ]);

        $completedEvent3 = Event::create([
            'eventName' => 'Youth Camp 2026',
            'venue' => 'FRIM, Kepong',
            'eventDescription' => 'Youth leadership camp',
            'eventCategory' => 'Camp',
            'status' => 'completed',
            'quota' => 5,
            'startDateTime' => now()->subDays(21)->setTime(7, 0),
            'endDateTime' => now()->subDays(21)->setTime(20, 0),
            'totalParticipants' => 120,
        ]);

        // ============ ONGOING EVENTS (Today) ============
        // Event 1: Facilitators clocked in but NOT clocked out (for testing)
        $ongoingEvent1 = Event::create([
            'eventName' => 'Digital Marketing Seminar',
            'venue' => 'Sunway Pyramid Convention Centre',
            'eventDescription' => 'Seminar on digital marketing strategies',
            'eventCategory' => 'Talk',
            'status' => 'ongoing',
            'quota' => 3,
            'startDateTime' => now()->setTime(9, 0),
            'endDateTime' => now()->setTime(17, 0),
            'totalParticipants' => 60,
        ]);

        // Event 2: Another ongoing with clocked in facilitators
        $ongoingEvent2 = Event::create([
            'eventName' => 'Innovation Workshop',
            'venue' => 'Cyberjaya MMU Campus',
            'eventDescription' => 'Creative thinking and innovation workshop',
            'eventCategory' => 'Workshop',
            'status' => 'ongoing',
            'quota' => 2,
            'startDateTime' => now()->setTime(10, 0),
            'endDateTime' => now()->setTime(16, 0),
            'totalParticipants' => 35,
        ]);

        // ============ UPCOMING EVENTS ============
        $upcomingEvent1 = Event::create([
            'eventName' => 'Annual Corporate Gala',
            'venue' => 'Mandarin Oriental KL',
            'eventDescription' => 'Annual company celebration',
            'eventCategory' => 'Holiday',
            'status' => 'upcoming',
            'quota' => 4,
            'startDateTime' => now()->addDays(3)->setTime(19, 0),
            'endDateTime' => now()->addDays(3)->setTime(23, 0),
            'totalParticipants' => 200,
        ]);

        $upcomingEvent2 = Event::create([
            'eventName' => 'Student Leadership Camp',
            'venue' => 'Tanjung Malim, Perak',
            'eventDescription' => 'Leadership camp for university students',
            'eventCategory' => 'Camp',
            'status' => 'upcoming',
            'quota' => 5,
            'startDateTime' => now()->addDays(7)->setTime(7, 0),
            'endDateTime' => now()->addDays(7)->setTime(21, 0),
            'totalParticipants' => 150,
        ]);

        // ============ ASSIGNMENTS FOR COMPLETED EVENTS ============
        // All facilitators completed their shifts
        foreach ([0, 1, 2] as $i) {
            Assignment::create([
                'eventID' => $completedEvent1->eventID,
                'userID' => $facilitators[$i]->userID,
                'dateAssigned' => now()->subDays(10),
                'status' => 'accepted',
                'clockInTime' => now()->subDays(7)->setTime(8, 45 + $i * 5),
                'clockOutTime' => now()->subDays(7)->setTime(17, 10 + $i * 3),
                'attendanceStatus' => 'completed',
            ]);
        }

        foreach ([1, 2, 3, 4] as $i) {
            Assignment::create([
                'eventID' => $completedEvent2->eventID,
                'userID' => $facilitators[$i]->userID,
                'dateAssigned' => now()->subDays(20),
                'status' => 'accepted',
                'clockInTime' => now()->subDays(14)->setTime(7, 50 + $i * 2),
                'clockOutTime' => now()->subDays(14)->setTime(18, 5 + $i * 2),
                'attendanceStatus' => 'completed',
            ]);
        }

        foreach ([0, 2, 3, 4, 5] as $i) {
            Assignment::create([
                'eventID' => $completedEvent3->eventID,
                'userID' => $facilitators[$i]->userID,
                'dateAssigned' => now()->subDays(25),
                'status' => 'accepted',
                'clockInTime' => now()->subDays(21)->setTime(6, 45 + $i * 3),
                'clockOutTime' => now()->subDays(21)->setTime(20, 15 + $i * 2),
                'attendanceStatus' => 'completed',
            ]);
        }

        // ============ ASSIGNMENTS FOR ONGOING EVENTS (TEST CLOCK OUT) ============
        // Facilitators clocked in but NOT clocked out
        Assignment::create([
            'eventID' => $ongoingEvent1->eventID,
            'userID' => $facilitators[0]->userID, // Ahmad Faiz
            'dateAssigned' => now()->subDays(2),
            'status' => 'accepted',
            'clockInTime' => now()->setTime(8, 55),
            'clockOutTime' => null, // NOT clocked out
            'attendanceStatus' => 'present',
            'imageProof' => 'attendance_proofs/sample1.jpg',
        ]);

        Assignment::create([
            'eventID' => $ongoingEvent1->eventID,
            'userID' => $facilitators[1]->userID, // Nurul Aisyah
            'dateAssigned' => now()->subDays(2),
            'status' => 'accepted',
            'clockInTime' => now()->setTime(9, 2),
            'clockOutTime' => null, // NOT clocked out
            'attendanceStatus' => 'present',
        ]);

        Assignment::create([
            'eventID' => $ongoingEvent1->eventID,
            'userID' => $facilitators[2]->userID, // Muhammad Hafiz
            'dateAssigned' => now()->subDays(2),
            'status' => 'accepted',
            'clockInTime' => null, // Not clocked in yet
            'clockOutTime' => null,
            'attendanceStatus' => 'pending',
        ]);

        // Second ongoing event
        Assignment::create([
            'eventID' => $ongoingEvent2->eventID,
            'userID' => $facilitators[3]->userID, // Siti Aminah
            'dateAssigned' => now()->subDays(3),
            'status' => 'accepted',
            'clockInTime' => now()->setTime(9, 58),
            'clockOutTime' => null, // NOT clocked out
            'attendanceStatus' => 'present',
            'imageProof' => 'attendance_proofs/sample2.jpg',
        ]);

        Assignment::create([
            'eventID' => $ongoingEvent2->eventID,
            'userID' => $facilitators[4]->userID, // Zulkifli
            'dateAssigned' => now()->subDays(3),
            'status' => 'accepted',
            'clockInTime' => now()->setTime(10, 5),
            'clockOutTime' => null, // NOT clocked out
            'attendanceStatus' => 'present',
        ]);

        // ============ ASSIGNMENTS FOR UPCOMING EVENTS ============
        // Some accepted, some pending
        Assignment::create([
            'eventID' => $upcomingEvent1->eventID,
            'userID' => $facilitators[0]->userID,
            'dateAssigned' => now(),
            'status' => 'accepted',
            'attendanceStatus' => 'pending',
        ]);

        Assignment::create([
            'eventID' => $upcomingEvent1->eventID,
            'userID' => $facilitators[2]->userID,
            'dateAssigned' => now(),
            'status' => 'accepted',
            'attendanceStatus' => 'pending',
        ]);

        Assignment::create([
            'eventID' => $upcomingEvent1->eventID,
            'userID' => $facilitators[4]->userID,
            'dateAssigned' => now(),
            'status' => 'pending', // Not yet accepted
            'attendanceStatus' => 'pending',
        ]);

        Assignment::create([
            'eventID' => $upcomingEvent1->eventID,
            'userID' => $facilitators[5]->userID,
            'dateAssigned' => now(),
            'status' => 'pending', // Not yet accepted
            'attendanceStatus' => 'pending',
        ]);

        foreach ([1, 3, 4, 5, 0] as $i) {
            Assignment::create([
                'eventID' => $upcomingEvent2->eventID,
                'userID' => $facilitators[$i]->userID,
                'dateAssigned' => now()->subDays(1),
                'status' => $i < 3 ? 'accepted' : 'pending',
                'attendanceStatus' => 'pending',
            ]);
        }

        $this->command->info('✅ AttendanceTestSeeder completed!');
        $this->command->info('   📌 6 Facilitators created (password: password123)');
        $this->command->info('   📌 1 Operation Manager: manager@falcon.test');
        $this->command->info('   📌 7 Events: 3 completed, 2 ongoing, 2 upcoming');
        $this->command->info('   📌 4 Facilitators clocked in but NOT clocked out (test clock out)');
    }
}
