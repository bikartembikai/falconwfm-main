<?php

namespace App\Services;

use App\Models\Facilitator;
use App\Models\Event;
use Carbon\Carbon;

class InferenceEngine
{
    /**
     * Knowledge Base: Event Type => Criteria
     * (Hard Coded Rules as per FYP / System Design)
     */
    protected $rules = [
        'TEAM BUILDING' => [
            'skills' => ['Speaking', 'Medic', 'Leadership', 'Facilitating']
        ],
        'TALK' => [
            'skills' => ['Speaking', 'Leadership', 'Public Speaking', 'Facilitating']
        ],
        'CAMP' => [
            'skills' => ['Medic', 'Speaking', 'Leadership', 'Hiking', 'Trekking', 'Motivation', 'Religious', 'Survival', 'Logistics'],
            'high_risk' => true // Heuristic Rule Trigger
        ],
        'WORKSHOP' => [
            'skills' => ['Public Speaking', 'Teaching', 'Survival', 'Archery', 'Facilitating', 'Time Management', 'Leadership', 'Organization Management', 'Logistics']
        ],
        'HOLIDAY' => [
            'skills' => ['Medic', 'Swimming', 'Logistic'],
            'high_risk' => true // Heuristic Rule Trigger
        ]
    ];

    /**
     * Main Inference Function (Forward Chaining Logic)
     * 
     * @param object|Event $event
     * @param int $limit
     * @return array
     */
    public function recommend($event, $limit = 5)
    {
        // 1. Data Fuzzification / Normalization
        $category = $this->normalizeCategory($event->event_category ?? '');
        $eventStart = $event->start_date_time ? Carbon::parse($event->start_date_time) : null;
        $eventEnd = $event->end_date_time ? Carbon::parse($event->end_date_time) : null;
        
        // Load Facts (Facilitators with History)
        $facilitators = Facilitator::with(['user.assignments.event', 'user.leaves'])->get();
        
        $candidates = [];

        foreach ($facilitators as $facil) {
            $reason = null;

            // ---------------------------------------------------------
            // RULE 1: AVAILABILITY CHECK (Hard Constraint)
            // ---------------------------------------------------------
            if (!$this->checkAvailability($facil, $eventStart, $eventEnd, $reason)) {
                // Log rejected for debugging/explanation if needed
                continue; 
            }

            // ---------------------------------------------------------
            // RULE 2: COMPETENCY MAPPING (Hard Constraint)
            // ---------------------------------------------------------
            $skillMatchScore = $this->calculateSkillMatch($facil, $category, $event->required_skill_tag ?? null);
            if ($skillMatchScore <= 0) {
                continue; // Reject if 0 skills match
            }

            // ---------------------------------------------------------
            // RULE 3: EXPERIENCE HEURISTICS (Safety Rule)
            // ---------------------------------------------------------
            if (!$this->checkExperienceConstraint($facil, $category, $reason)) {
                continue; // Reject inexperienced staff for high risk events
            }

            // ---------------------------------------------------------
            // RULE 4: QUALITY RANKING (Soft Constraint / Inference)
            // ---------------------------------------------------------
            // Suitability Score = (Rating * 2) + SkillMatches
            // Example: Rating 5.0 * 2 = 10 + 3 skills = 13
            $rating = $facil->average_rating ?? 0;
            $suitabilityScore = ($rating * 2) + $skillMatchScore;

            $candidates[] = [
                'id' => $facil->id,
                'name' => $facil->user ? $facil->user->name : 'Unknown',
                'match_score' => $suitabilityScore,
                'skills_matched' => $skillMatchScore,
                'rating' => $rating,
                'skills' => $facil->skills,
                'experience' => $facil->experience,
                'debug_reason' => 'Qualified'
            ];
        }

        // Sort by Suitability Score Descending
        usort($candidates, function ($a, $b) {
            return $b['match_score'] <=> $a['match_score'];
        });

        return array_slice($candidates, 0, $limit);
    }

    // --------------------------------------------------------------------
    // INFERENCE RULES
    // --------------------------------------------------------------------

    private function checkAvailability($facil, $start, $end, &$reason)
    {
        if (!$start || !$end) return true; // Cannot check without dates

        // Check 1: Assignments Overlap
        if ($facil->user && $facil->user->assignments) {
            foreach ($facil->user->assignments as $assign) {
                if ($assign->event) {
                    $aStart = Carbon::parse($assign->event->start_date_time);
                    $aEnd = Carbon::parse($assign->event->end_date_time);
                    
                    if ($start->lessThanOrEqualTo($aEnd) && $end->greaterThanOrEqualTo($aStart)) {
                        $reason = "Already assigned to event: " . $assign->event->event_name;
                        return false;
                    }
                }
            }
        }

        // Check 2: Leaves Overlap
        if ($facil->user && $facil->user->leaves) {
            foreach ($facil->user->leaves as $leave) {
                if ($leave->status === 'approved') {
                    $lStart = Carbon::parse($leave->start_date);
                    $lEnd = Carbon::parse($leave->end_date);

                    // If leave overlaps event
                    if ($start->lessThanOrEqualTo($lEnd) && $end->greaterThanOrEqualTo($lStart)) {
                        $reason = "On approved leave";
                        return false;
                    }
                }
            }
        }

        return true;
    }

    private function calculateSkillMatch($facil, $category, $requiredTag)
    {
        $targetSkills = $this->rules[$category]['skills'] ?? [];
        if ($requiredTag) {
            $targetSkills[] = $requiredTag;
        }
        
        $facilSkills = strtolower($facil->skills ?? '');
        $match = 0;

        foreach ($targetSkills as $skill) {
            if (strpos($facilSkills, strtolower($skill)) !== false) {
                $match++;
            }
        }

        return $match;
    }

    private function checkExperienceConstraint($facil, $category, &$reason)
    {
        $isHighRisk = $this->rules[$category]['high_risk'] ?? false;

        if ($isHighRisk) {
            $tenureYears = 0;
            if ($facil->join_date) {
                $tenureYears = Carbon::parse($facil->join_date)->diffInYears(now());
            }

            if ($tenureYears < 2) {
                $reason = "Experience < 2 years for High Risk event";
                return false;
            }
        }
        return true;
    }

    private function normalizeCategory($cat)
    {
        $cat = strtoupper($cat);
        $mapping = [
            'CERAMAH' => 'TALK',
            'KEM' => 'CAMP',
            'KURSUS/BENGKEL' => 'WORKSHOP',
            'PERCUTIAN' => 'HOLIDAY'
        ];
        return $mapping[$cat] ?? $cat;
    }
}
