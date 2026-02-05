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
                'eventCategory' => 'TEAM BUILDING',
                'requiredSkill' => ['Speaking', 'Medic', 'Leadership', 'Facilitating'],
                'minExperience' => 0,
                'minRating' => 0,

            ],
            [
                'eventCategory' => 'TALK',
                'requiredSkill' => ['Speaking', 'Leadership', 'Public Speaking', 'Facilitating'],
                'minExperience' => 0,
                'minRating' => 0,

            ],
            [
                'eventCategory' => 'CAMP',
                'requiredSkill' => ['Medic', 'Speaking', 'Leadership', 'Hiking', 'Trekking', 'Motivation', 'Religious', 'Survival', 'Logistics'],
                // Report Rule 6 & 7: High Risk => Min 2 Years
                'minExperience' => 2, 
                'minRating' => 0,

            ],
            [
                'eventCategory' => 'WORKSHOP',
                'requiredSkill' => ['Public Speaking', 'Teaching', 'Survival', 'Archery', 'Facilitating', 'Time Management', 'Leadership', 'Organization Management', 'Logistics'],
                'minExperience' => 0,
                'minRating' => 0,

            ],
            [
                'eventCategory' => 'HOLIDAY',
                'requiredSkill' => ['Medic', 'Swimming', 'Logistic'],
                // Report Rule 6 & 7: High Risk => Min 2 Years
                'minExperience' => 2,
                'minRating' => 0,

            ],
        ];

        foreach ($rules as $rule) {
            EventRule::updateOrCreate(
                ['eventCategory' => $rule['eventCategory']],
                $rule
            );
        }
    }
}
