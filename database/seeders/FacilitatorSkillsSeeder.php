<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Skill;
use Illuminate\Support\Facades\Hash;

class FacilitatorSkillsSeeder extends Seeder
{
    /**
     * Seed facilitators and skills for testing the skills feature.
     */
    public function run(): void
    {
        // Create skills
        $skillNames = [
            'Event Management',
            'Public Speaking',
            'Project Management',
            'Technical Leadership',
            'Workshop Design',
            'Team Building',
            'Leadership Training',
            'Communication Skills',
            'Time Management',
            'Conflict Resolution',
            'Strategic Planning',
            'Presentation Skills',
            'Facilitation',
            'Coaching',
            'Mentoring',
        ];

        $skills = [];
        foreach ($skillNames as $name) {
            $skills[$name] = Skill::firstOrCreate(['skillName' => $name]);
        }

        // Create facilitators
        $facilitators = [
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah.johnson@example.com',
                'experience' => 5,
                'phoneNumber' => '+63 912 345 6789',
                'skills' => ['Event Management', 'Public Speaking', 'Project Management'],
            ],
            [
                'name' => 'Michael Chen',
                'email' => 'michael.chen@example.com',
                'experience' => 8,
                'phoneNumber' => '+63 912 345 6780',
                'skills' => ['Technical Leadership', 'Team Building', 'Coaching'],
            ],
            [
                'name' => 'Emily Rodriguez',
                'email' => 'emily.rodriguez@example.com',
                'experience' => 3,
                'phoneNumber' => '+63 912 345 6781',
                'skills' => ['Workshop Design', 'Facilitation', 'Presentation Skills'],
            ],
            [
                'name' => 'David Kim',
                'email' => 'david.kim@example.com',
                'experience' => 6,
                'phoneNumber' => '+63 912 345 6782',
                'skills' => ['Leadership Training', 'Mentoring', 'Strategic Planning'],
            ],
            [
                'name' => 'Anna Williams',
                'email' => 'anna.williams@example.com',
                'experience' => 4,
                'phoneNumber' => '+63 912 345 6783',
                'skills' => ['Communication Skills', 'Conflict Resolution', 'Time Management'],
            ],
        ];

        foreach ($facilitators as $data) {
            $facilitator = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password123'),
                    'role' => 'facilitator',
                    'experience' => $data['experience'],
                    'phoneNumber' => $data['phoneNumber'],
                    'joinDate' => now(),
                    'averageRating' => rand(35, 50) / 10, // Random rating between 3.5 and 5.0
                ]
            );

            // Attach skills
            $skillIds = [];
            foreach ($data['skills'] as $skillName) {
                if (isset($skills[$skillName])) {
                    $skillIds[] = $skills[$skillName]->skillID;
                }
            }
            $facilitator->skills()->syncWithoutDetaching($skillIds);
        }

        $this->command->info('✓ Created ' . count($facilitators) . ' facilitators with skills');
        $this->command->info('✓ Created ' . count($skillNames) . ' skills');
    }
}
