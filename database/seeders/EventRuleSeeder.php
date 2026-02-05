<?php

namespace Database\Seeders;

use App\Models\EventRule;
use Illuminate\Database\Seeder;

class EventRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rules = [
            [
                'event_category' => 'TEAM BUILDING',
                'required_skills' => ['Speaking', 'Medic', 'Leadership', 'Facilitating'],
                'required_specialization' => 'Corporate Training',
                'min_experience' => 2,
                'min_rating' => 4,
            ],
            [
                'event_category' => 'TALK',
                'required_skills' => ['Speaking', 'Leadership', 'Public Speaking', 'Facilitating'],
                'required_specialization' => 'Motivation',
                'min_experience' => 3,
                'min_rating' => 4,
            ],
            [
                'event_category' => 'CAMP',
                'required_skills' => ['Medic', 'Speaking', 'Leadership', 'Hiking', 'Trekking', 'Motivation', 'Religious', 'Survival', 'Logistics'],
                'required_specialization' => 'Outdoor Activities',
                'min_experience' => 1,
                'min_rating' => 3,
            ],
            [
                'event_category' => 'WORKSHOP',
                'required_skills' => ['Public Speaking', 'Teaching', 'Survival', 'Archery', 'Facilitating', 'Time Management', 'Leadership', 'Organization Management', 'Logistics'],
                'required_specialization' => 'Education',
                'min_experience' => 2,
                'min_rating' => 3,
            ],
            [
                'event_category' => 'HOLIDAY',
                'required_skills' => ['Medic', 'Swimming', 'Logistic'],
                'required_specialization' => 'Recreation',
                'min_experience' => 0,
                'min_rating' => 0,
            ],
        ];

        foreach ($rules as $rule) {
            EventRule::updateOrCreate(
                ['event_category' => $rule['event_category']],
                $rule
            );
        }
    }
}
