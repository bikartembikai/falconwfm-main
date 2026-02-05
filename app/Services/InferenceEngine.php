<?php

namespace App\Services;

use App\Models\User;
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
        $category = $this->normalizeCategory($event->eventCategory ?? '');
        $eventStart = $event->startDateTime ? Carbon::parse($event->startDateTime) : null;
        $eventEnd = $event->endDateTime ? Carbon::parse($event->endDateTime) : null;
        
        // Load Rule from Knowledge Base (Database)
        $rule = \App\Models\EventRule::find($category);
        
        // Load Facts (Facilitators with History)
        $facilitators = User::where('role', 'facilitator')
                            ->with(['assignments.event', 'leaves', 'skills'])
                            ->get();
        
        $results = [];

        foreach ($facilitators as $facil) {
            $reason = null;
            $status = 'available';
            $debugReason = 'Qualified';

            if (!$this->checkAvailability($facil, $eventStart, $eventEnd, $reason)) {
                $status = 'busy';
                $debugReason = $reason;
            }
            $skillMatchScore = $this->calculateSkillMatch($facil, $rule, $event->requiredSkillTag ?? null);
            if ($status === 'available' && $skillMatchScore <= 0) {
                if ($rule && !empty($rule->requiredSkill)) {
                    $status = 'unqualified'; 
                    $reason = "No matching skills for {$category}";
                    $debugReason = $reason;
                }
            }

            if ($status === 'available' && $rule && !$this->checkExperienceConstraint($facil, $rule, $reason)) {
                $status = 'unqualified';
                $debugReason = $reason;
            }
            $rating = $facil->averageRating ?? 0;
            $suitabilityScore = ($rating * 2) + $skillMatchScore;

            $results[] = [
                'id' => $facil->userID, // PK is userID
                'user_id' => $facil->userID, 
                'name' => $facil->name,
                'email' => $facil->email, // Direct
                'match_score' => $suitabilityScore,
                'skills_matched' => $skillMatchScore,
                'rating' => $rating,
                'skills' => $facil->skills->pluck('skillName')->implode(', '),
                'experience' => $facil->experience,
                'join_date' => $facil->joinDate,
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

        if ($facil->assignments) {
            foreach ($facil->assignments as $assign) {
                if ($assign->event) {
                    $aStart = Carbon::parse($assign->event->startDateTime);
                    $aEnd = Carbon::parse($assign->event->endDateTime);
                    
                    if ($start->lessThanOrEqualTo($aEnd) && $end->greaterThanOrEqualTo($aStart)) {
                        $reason = "Already assigned to event: " . $assign->event->eventName;
                        return false;
                    }
                }
            }
        }

        if ($facil->leaves) {
            foreach ($facil->leaves as $leave) {
                if ($leave->status === 'approved') {
                    $lStart = Carbon::parse($leave->startDate);
                    $lEnd = Carbon::parse($leave->endDate);

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
        $targetSkills = $rule ? ($rule->requiredSkill ?? []) : [];
        
        if ($requiredTag) {
            $targetSkills[] = $requiredTag;
        }
        
        // Facil skills array from relation
        $facilSkills = $facil->skills->pluck('skillName')->map(fn($s) => strtolower($s))->toArray();
        $match = 0;

        foreach ($targetSkills as $skill) {
            $skillLower = strtolower($skill);
            foreach ($facilSkills as $fSkill) {
                if (strpos($fSkill, $skillLower) !== false || strpos($skillLower, $fSkill) !== false) {
                    $match++;
                    break; 
                }
            }
        }

        return $match;
    }

    private function checkExperienceConstraint($facil, $rule, &$reason)
    {
        if (!$rule) return true;

        // $isHighRisk = strtolower($rule->intensity_level) === 'high risk'; // Removed
        $minExp = $rule->minExperience;

        if ($minExp > 0) {
            $tenureYears = 0;
            if ($facil->joinDate) {
                $tenureYears = Carbon::parse($facil->joinDate)->diffInYears(now());
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

