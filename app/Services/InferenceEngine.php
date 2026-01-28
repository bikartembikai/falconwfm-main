<?php

namespace App\Services;

use App\Models\Facilitator;
use App\Models\Event;
use Carbon\Carbon;

class InferenceEngine
{
    /**
     * Main Inference Function (Forward Chaining Logic)
     * 
     * @param object|Event $event
     * @param int $limit
     * @return array
     */
    public function recommend($event, $limit = 5)
    {
        $candidates = $this->analyzeFacilitators($event);
        
        // Filter only available and qualified for automatic recommendation
        $filtered = array_filter($candidates, function($c) {
            return $c['status'] === 'available';
        });

        // Sort by Suitability Score Descending
        usort($filtered, function ($a, $b) {
            return $b['match_score'] <=> $a['match_score'];
        });

        return array_slice($filtered, 0, $limit);
    }

    /**
     * Analyze ALL facilitators and return their compatibility status
     * Used for the Assignment Interface
     */
    public function analyzeFacilitators($event)
    {
        // 1. Data Fuzzification / Normalization
        $category = $this->normalizeCategory($event->event_category ?? '');
        $eventStart = $event->start_date_time ? Carbon::parse($event->start_date_time) : null;
        $eventEnd = $event->end_date_time ? Carbon::parse($event->end_date_time) : null;
        
        // Load Rule from Knowledge Base (Database)
        $rule = \App\Models\EventRule::find($category);
        
        // Load Facts (Facilitators with History)
        $facilitators = Facilitator::with(['user.assignments.event', 'user.leaves'])->get();
        
        $results = [];

        foreach ($facilitators as $facil) {
            $reason = null;
            $status = 'available';
            $debugReason = 'Qualified';

            if (!$this->checkAvailability($facil, $eventStart, $eventEnd, $reason)) {
                $status = 'busy';
                $debugReason = $reason;
            }
            $skillMatchScore = $this->calculateSkillMatch($facil, $rule, $event->required_skill_tag ?? null);
            if ($status === 'available' && $skillMatchScore <= 0) {
                if ($rule && !empty($rule->required_skills)) {
                    $status = 'unqualified'; 
                    $reason = "No matching skills for {$category}";
                    $debugReason = $reason;
                }
            }

            if ($status === 'available' && $rule && !$this->checkExperienceConstraint($facil, $rule, $reason)) {
                $status = 'unqualified';
                $debugReason = $reason;
            }
            $rating = $facil->average_rating ?? 0;
            $suitabilityScore = ($rating * 2) + $skillMatchScore;

            $results[] = [
                'id' => $facil->id,
                'user_id' => $facil->user_id,
                'name' => $facil->user ? $facil->user->name : 'Unknown',
                'email' => $facil->user ? $facil->user->email : '',
                'match_score' => $suitabilityScore,
                'skills_matched' => $skillMatchScore,
                'rating' => $rating,
                'skills' => $facil->skills,
                'experience' => $facil->experience,
                'join_date' => $facil->join_date,
                'status' => $status, 
                'reason' => $reason
            ];
        }

        usort($results, function ($a, $b) {
            return $b['match_score'] <=> $a['match_score'];
        });

        return $results;
    }

    private function checkAvailability($facil, $start, $end, &$reason)
    {
        if (!$start || !$end) return true; 

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

        if ($facil->user && $facil->user->leaves) {
            foreach ($facil->user->leaves as $leave) {
                if ($leave->status === 'approved') {
                    $lStart = Carbon::parse($leave->start_date);
                    $lEnd = Carbon::parse($leave->end_date);

                    if ($start->lessThanOrEqualTo($lEnd) && $end->greaterThanOrEqualTo($lStart)) {
                        $reason = "On approved leave";
                        return false;
                    }
                }
            }
        }

        return true;
    }

    private function calculateSkillMatch($facil, $rule, $requiredTag)
    {
        $targetSkills = $rule ? ($rule->required_skills ?? []) : [];
        
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

    private function checkExperienceConstraint($facil, $rule, &$reason)
    {
        if (!$rule) return true;

        $isHighRisk = strtolower($rule->intensity_level) === 'high risk';
        $minExp = $rule->min_experience;

        if ($isHighRisk || $minExp > 0) {
            $tenureYears = 0;
            if ($facil->join_date) {
                $tenureYears = Carbon::parse($facil->join_date)->diffInYears(now());
            }

            if ($tenureYears < $minExp) {
                $reason = "Experience ($tenureYears yrs) < Required ($minExp yrs) for High Risk/Intense Event";
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
