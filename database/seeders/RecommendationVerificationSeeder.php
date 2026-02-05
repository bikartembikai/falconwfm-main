<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\User;
use App\Models\Leave;
use App\Models\Assignment;
use App\Models\Payment;
use App\Models\Skill;
use Illuminate\Support\Facades\DB;

class RecommendationVerificationSeeder extends Seeder
{
    public function run()
    {
        // Clear existing data cleanly
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::truncate(); 
        Event::truncate(); 
        // Facilitator::truncate(); // Removed
        Leave::truncate();
        Assignment::truncate();
        Payment::truncate();
        DB::table('facilitator_skills')->truncate();
        Skill::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // ----------------------------------------------------------------
        // 1. Create Facilitators (with New Skills)
        // ----------------------------------------------------------------
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
                'role' => 'facilitator',
                'bankName' => $p['bank'],
                'bankAccountNumber' => $p['acct'],
                'phoneNumber' => '012-' . rand(1000000, 9999999),
                'joinDate' => now()->subYear(),
            ]);

            // Attach Skills
            $skillsList = explode(' ', $p['skills']);
            foreach ($skillsList as $skName) {
                // Ensure skill exists
                $skillModel = Skill::firstOrCreate(['skillName' => $skName]);
                $user->skills()->attach($skillModel->skillID);
            }
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
            Event::create([
                'eventName' => $evt['name'],
                'eventCategory' => $evt['cat'],
                // 'requiredSkillTag' => $evt['skills'], // Removed from schema, logic uses Rule or description/remark if needed.
                // If logic strictly needs a per-event tag override, we might need to rely on 'remark' or re-add the column. 
                // But migration removed it. So I'll put it in remark for now if needed, or omit.
                'remark' => 'Requires: ' . $evt['skills'],
                'status' => 'upcoming',
                'startDateTime' => now()->addDays(rand(5,30)),
                'quota' => 20
            ]);
        }
        
        // Create a Past Event with Pending Payment for Testing
        $pastEvent = Event::create([
            'eventName' => 'Past Training',
            'eventCategory' => 'TEAM BUILDING',
            'remark' => 'Leadership',
            'status' => 'completed',
            'startDateTime' => now()->subDays(10),
            'endDateTime' => now()->subDays(9),
            'quota' => 10
        ]);
        
        $facil = User::where('role', 'facilitator')->first();
        if ($facil) {
            // Create Assignment (which serves as attendance record)
            $assign = Assignment::create([
                'eventID' => $pastEvent->eventID,
                'userID' => $facil->userID,
                'status' => 'assigned',
                'attendanceStatus' => 'present',
                'clockInTime' => now()->subDays(10),
                'clockOutTime' => now()->subDays(10)->addHours(8),
            ]);
            
            Payment::create([
                'assignmentID' => $assign->assignmentID, // Linked to Assignment
                'amount' => 200.00,
                'paymentStatus' => 'pending',
            ]);
        }

        $this->command->info("Seeded with New Criteria Data & Payment Test Data.");
    }
}
