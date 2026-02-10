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
                'eventCategory' => 'TEAM BUILDING',
                'requiredSkill' => json_encode(['Speaking', 'Medic', 'Leadership', 'Facilitating']),
                'minExperience' => 2,
                'minRating' => 4,
            ],
            [
                'eventCategory' => 'TALK',
                'requiredSkill' => json_encode(['Speaking', 'Leadership', 'Public Speaking', 'Facilitating']),
                'minExperience' => 3,
                'minRating' => 4,
            ],
            [
                'eventCategory' => 'CAMP',
                'requiredSkill' => json_encode(['Medic', 'Speaking', 'Leadership', 'Hiking', 'Trekking', 'Motivation', 'Religious', 'Survival', 'Logistics']),
                'minExperience' => 1,
                'minRating' => 3,
            ],
            [
                'eventCategory' => 'WORKSHOP',
                'requiredSkill' => json_encode(['Public Speaking', 'Teaching', 'Survival', 'Archery', 'Facilitating', 'Time Management', 'Leadership', 'Organization Management', 'Logistics']),
                'minExperience' => 2,
                'minRating' => 3,
            ],
            [
                'eventCategory' => 'HOLIDAY',
                'requiredSkill' => json_encode(['Medic', 'Swimming', 'Logistics']),
                'minExperience' => 0,
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
