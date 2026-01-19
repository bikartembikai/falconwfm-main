<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\User;
use App\Models\Facilitator;
use App\Models\Leave;
use App\Models\Attendance;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class RecommendationVerificationSeeder extends Seeder
{
    public function run()
    {
        // Clear existing data cleanly
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::truncate(); 
        Event::truncate(); 
        Facilitator::truncate();
        Leave::truncate();
        Attendance::truncate();
        Payment::truncate();
        DB::table('assignments')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // ----------------------------------------------------------------
        // 1. Create Facilitators (with New Skills)
        // ----------------------------------------------------------------
        // Skills: Speaking, Medic, Leadership, Facilitating, Public Speaking, Hiking, Trekking, Motivation, Religious, Survival, Logistics, Teaching, Archery, Time Management, Organization Management, Swimming, Logistic
        $profiles = [
            // Matches Team Building & Talk
            ['name' => 'Alice Leader', 'skills' => 'Speaking Leadership Facilitating Public Speaking', 'bank' => 'Maybank', 'acct' => '111'],
            
            // Matches Camp & Holiday
            ['name' => 'Bob Ranger', 'skills' => 'Medic Hiking Trekking Survival Logistics', 'bank' => 'CIMB', 'acct' => '222'],
            
            // Matches Workshop
            ['name' => 'Charlie Teacher', 'skills' => 'Teaching Public Speaking Time Management Organization Management', 'bank' => 'RHB', 'acct' => '333'],
            
            // Matches Camp (Religious/Motivation)
            ['name' => 'Ustaz David', 'skills' => 'Religious Motivation Speaking', 'bank' => 'Bank Islam', 'acct' => '444'],
            
            // Matches Holiday (Specific)
            ['name' => 'Eve Swimmer', 'skills' => 'Swimming Medic Logistic', 'bank' => 'Public Bank', 'acct' => '555'],
            
            // Mixed / Archery
            ['name' => 'Archer Frank', 'skills' => 'Archery Survival Facilitating', 'bank' => 'HLB', 'acct' => '666'],
        ];

        foreach ($profiles as $p) {
            $user = User::create([
                'name' => $p['name'],
                'email' => strtolower(str_replace(' ', '.', $p['name'])) . '@falcon.com',
                'password' => bcrypt('password'),
                'role' => 'facilitator'
            ]);

            Facilitator::create([
                'user_id' => $user->id,
                'skills' => $p['skills'],
                'bank_name' => $p['bank'],
                'bank_account_number' => $p['acct'],
                'phone_number' => '012-' . rand(1000000, 9999999),
                'join_date' => now()->subYear(),
            ]);
        }

        // ----------------------------------------------------------------
        // 2. Create Events (New English Categories)
        // ----------------------------------------------------------------
        $events = [
            ['name' => 'Mega Team Building', 'cat' => 'TEAM BUILDING', 'skills' => 'Leadership'],
            ['name' => 'Leadership Talk', 'cat' => 'TALK', 'skills' => 'Public Speaking'],
            ['name' => 'Jungle Camp', 'cat' => 'CAMP', 'skills' => 'Survival'],
            ['name' => 'Management Workshop', 'cat' => 'WORKSHOP', 'skills' => 'Time Management'],
            ['name' => 'Island Holiday', 'cat' => 'HOLIDAY', 'skills' => 'Swimming'],
        ];

        foreach ($events as $evt) {
            $e = Event::create([
                'event_name' => $evt['name'],
                'event_category' => $evt['cat'],
                'required_skill_tag' => $evt['skills'],
                'status' => 'upcoming',
                'start_date_time' => now()->addDays(rand(5,30)),
                'quota' => 20
            ]);
        }
        
        // Create a Past Event with Pending Payment for Testing
        $pastEvent = Event::create([
            'event_name' => 'Past Training',
            'event_category' => 'TEAM BUILDING',
            'required_skill_tag' => 'Leadership',
            'status' => 'completed',
            'start_date_time' => now()->subDays(10),
            'end_date_time' => now()->subDays(9),
            'quota' => 10
        ]);
        
        $facil = Facilitator::first();
        if ($facil) {
            $att = Attendance::create([
                'event_id' => $pastEvent->id,
                'facilitator_id' => $facil->id,
                'status' => 'present',
                'clock_in_time' => now()->subDays(10),
                'clock_out_time' => now()->subDays(10)->addHours(8),
            ]);
            
            Payment::create([
                'attendance_id' => $att->id,
                'amount' => 200.00,
                'payment_status' => 'pending',
            ]);
        }

        $this->command->info("Seeded with New Criteria Data & Payment Test Data.");
    }
}
