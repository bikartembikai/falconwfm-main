<?php

namespace App\Services;

use App\Models\User;
use App\Models\Event;
use App\Models\EventRule;

class RecommendationService
{
    /**
     * Recommend Facilitators for an Event (Rule-Based from Database)
     */
    public function recommend($eventInput, $limit = 5)
    {
        $eventCategory = '';
        $customSkills = [];

        if (is_object($eventInput) && isset($eventInput->eventCategory)) {
            $eventCategory = strtoupper($eventInput->eventCategory);
            // Alias Mapping
            $eventCategory = $this->mapCategoryAlias($eventCategory);

            if (isset($eventInput->requiredSkillTag) && !empty($eventInput->requiredSkillTag)) {
                $customSkills[] = $eventInput->requiredSkillTag;
            }
        } else {
            return [];
        }

        // 1. Fetch Rule
        // 1. Fetch Rule
        $rule = EventRule::find($eventCategory);
        
        $requiredSkills = $rule ? ($rule->requiredSkill ?? []) : [];
        //$requiredSpecialization = $rule ? $rule->requiredSpecialization : null; // Removed from migration
        $minExperience = $rule ? $rule->minExperience : 0;
        $minRating = $rule ? $rule->minRating : 0;

        // Merge custom skills
        $targetSkills = array_unique(array_merge($requiredSkills, $customSkills));
        $targetSkills = array_map('strtolower', $targetSkills);

        if (empty($targetSkills)) {
            return [];
        }

        // FETCH USERS WITH ROLE 'facilitator' AND THEIR SKILLS
        $facilitators = User::where('role', 'facilitator')
                            ->with('skills')
                            ->get();
        $scores = [];

        foreach ($facilitators as $facil) {
            $score = 0;
            $matches = [];
            $reasons = [];

            // A. Skill Match (+1 per skill)
            // Get array of skill names from pivot
            $facilSkills = $facil->skills->pluck('skillName')->map(fn($s) => strtolower($s))->toArray();
            
            foreach ($targetSkills as $skill) {
                // Check if target skill is in facilitator's skill list
                // Using relaxed matching (strpos-like) or exact? Ideally exact now that we have normalized skills.
                // But let's keep strpos logic for flexibility if needed, or stick to exact.
                // Given "Speaking" vs "Public Speaking", simple in_array might fail if strict.
                // Let's use partial match check against normalized array.
                
                foreach ($facilSkills as $fSkill) {
                    if (strpos($fSkill, $skill) !== false || strpos($skill, $fSkill) !== false) {
                         $score += 1;
                         $matches[] = $skill;
                         break; // Count skill once
                    }
                }
            }

            // B. Specialization Match (Removed as per request)
            
            // C. Rating Check
            if ($facil->averageRating < $minRating) {
                $score -= 2; // Penalize low rating
            }

            if ($score > 0) {
                $scores[$facil->userID] = [
                    'score' => $score,
                    'matches' => $matches,
                    'reasons' => $reasons
                ];
            }
        }

        uasort($scores, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        return $this->formatFacilitatorResults($scores, $facilitators, $limit);
    }

    /**
     * Recommend Events for a Facilitator (Reverse Match)
     */
    public function recommendEvents($userId, $limit = 5)
    {
        $facilitator = User::find($userId);
        if (!$facilitator || $facilitator->role !== 'facilitator') return [];

        $facilSkills = $facilitator->skills->pluck('skillName')->map(fn($s) => strtolower($s))->toArray();

        // Fetch upcoming events
        $events = Event::where('status', 'upcoming')->get();
        $scores = [];

        foreach ($events as $event) {
            $cat = strtoupper($event->eventCategory ?? '');
            $cat = $this->mapCategoryAlias($cat);

            $rule = EventRule::find($cat);
            if (!$rule) continue;

            $requiredSkills = $rule ? ($rule->requiredSkill ?? []) : [];
            
            $score = 0;
            $matches = [];

            // A. Skills
            foreach ($requiredSkills as $skill) {
                $skillLower = strtolower($skill);
                foreach ($facilSkills as $fSkill) {
                    if (strpos($fSkill, $skillLower) !== false || strpos($skillLower, $fSkill) !== false) {
                        $score += 1;
                        $matches[] = $skill;
                        break;
                    }
                }
            }

            // B. Specialization (Removed)

            // C. Experience/Rating
            if ($rule) {
                if ($facilitator->averageRating < $rule->minRating) {
                    $score -= 2;
                }
            }

            if ($score > 0) {
                $scores[$event->id] = [
                    'score' => $score,
                    'matches' => $matches
                ];
            }
        }

        uasort($scores, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        // Format
        $results = [];
        $count = 0;
        foreach ($scores as $id => $data) {
            if ($count >= $limit) break;
            $evt = $events->find($id);
            if ($evt) {
                $results[] = [
                    'id' => $evt->eventID,
                    'name' => $evt->eventName,
                    'category' => $evt->eventCategory,
                    'match_score' => $data['score'],
                    'matched_keywords' => implode(', ', $data['matches'])
                ];
                $count++;
            }
        }
        return $results;
    }

    private function mapCategoryAlias($category)
    {
        $mapping = [
            'CERAMAH' => 'TALK',
            'KEM' => 'CAMP',
            'KURSUS/BENGKEL' => 'WORKSHOP',
            'PERCUTIAN' => 'HOLIDAY'
        ];
        return $mapping[$category] ?? $category;
    }

    private function formatFacilitatorResults($scores, $facilitators, $limit)
    {
        $results = [];
        $count = 0;
        foreach ($scores as $id => $data) {
            if ($count >= $limit) break;
            $facilitator = $facilitators->find($id);
            if ($facilitator) {
                $extraReasons = isset($data['reasons']) ? implode(', ', $data['reasons']) : '';
                $matchStr = implode(', ', array_unique($data['matches']));
                if ($extraReasons) $matchStr .= " | $extraReasons";

                $results[] = [
                    'id' => $facilitator->userID,
                    'name' => $facilitator->name, // Direct name from User
                    'match_score' => $data['score'], 
                    'matched_keywords' => $matchStr,
                    'skills' => $facilitator->skills->pluck('skillName')->implode(', '),
                    'experience' => $facilitator->experience // Merged attribute
                ];
                $count++;
            }
        }
        return $results;
    }
}

