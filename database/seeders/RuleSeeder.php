<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EventRule;

class RuleSeeder extends Seeder
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
                'min_experience' => 0,
                'min_rating' => 0,
                'intensity_level' => 'Normal'
            ],
            [
                'event_category' => 'TALK',
                'required_skills' => ['Speaking', 'Leadership', 'Public Speaking', 'Facilitating'],
                'min_experience' => 0,
                'min_rating' => 0,
                'intensity_level' => 'Normal'
            ],
            [
                'event_category' => 'CAMP',
                'required_skills' => ['Medic', 'Speaking', 'Leadership', 'Hiking', 'Trekking', 'Motivation', 'Religious', 'Survival', 'Logistics'],
                // Report Rule 6 & 7: High Risk => Min 2 Years
                'min_experience' => 2, 
                'min_rating' => 0,
                'intensity_level' => 'High Risk'
            ],
            [
                'event_category' => 'WORKSHOP',
                'required_skills' => ['Public Speaking', 'Teaching', 'Survival', 'Archery', 'Facilitating', 'Time Management', 'Leadership', 'Organization Management', 'Logistics'],
                'min_experience' => 0,
                'min_rating' => 0,
                'intensity_level' => 'Normal'
            ],
            [
                'event_category' => 'HOLIDAY',
                'required_skills' => ['Medic', 'Swimming', 'Logistic'],
                // Report Rule 6 & 7: High Risk => Min 2 Years
                'min_experience' => 2,
                'min_rating' => 0,
                'intensity_level' => 'High Risk'
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
