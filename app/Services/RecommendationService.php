<?php

namespace App\Services;

use App\Models\Facilitator;
use App\Models\Event;

class RecommendationService
{
    /**
     * Rules Mapping: Event Type => Criteria 1: Skills Needed
     */
    protected $rules = [
        'TEAM BUILDING' => [
            'Speaking', 'Medic', 'Leadership', 'Facilitating'
        ],
        'TALK' => [
            'Speaking', 'Leadership', 'Public Speaking', 'Facilitating'
        ],
        'CAMP' => [
            'Medic', 'Speaking', 'Leadership', 'Hiking', 'Trekking', 
            'Motivation', 'Religious', 'Survival', 'Logistics'
        ],
        'WORKSHOP' => [
            'Public Speaking', 'Teaching', 'Survival', 'Archery', 'Facilitating', 
            'Time Management', 'Leadership', 'Organization Management', 'Logistics'
        ],
        'HOLIDAY' => [
            'Medic', 'Swimming', 'Logistic' // Image says 'Logistic'
        ]
    ];

    /**
     * Recommend Facilitators for an Event (Rule-Based)
     */
    public function recommend($eventInput, $limit = 5)
    {
        $targetKeywords = [];
        $eventCategory = '';

        if (is_object($eventInput) && isset($eventInput->event_category)) {
            $eventCategory = strtoupper($eventInput->event_category);
            
            // Map common aliases
            $mapping = [
                'CERAMAH' => 'TALK',
                'KEM' => 'CAMP',
                'KURSUS/BENGKEL' => 'WORKSHOP',
                'PERCUTIAN' => 'HOLIDAY'
            ];
            if (isset($mapping[$eventCategory])) {
                $eventCategory = $mapping[$eventCategory];
            }

            if (isset($this->rules[$eventCategory])) {
                $targetKeywords = array_merge($targetKeywords, $this->rules[$eventCategory]);
            }
            if (!empty($eventInput->required_skill_tag)) {
                $targetKeywords[] = $eventInput->required_skill_tag;
            }
            $infoText = $eventInput->event_name . ' ' . $eventInput->event_description;
            // No, rule based is strict. Let's rely on rules mainly. 
            // But let's add text matches too for robustness or just strict rules?
            // User asked for "match with this types". Let's assume strict skills.
            // But code I wrote combines text search. Let's keep hybrid for robustness.
            $targetKeywords = array_merge($targetKeywords, $this->extractKeywordsFromText($infoText));

        } else {
            $text = (string)$eventInput;
            $targetKeywords = $this->extractKeywordsFromText($text);
        }

        $targetKeywords = array_unique(array_map('strtolower', $targetKeywords));
        
        if (empty($targetKeywords)) {
            return [];
        }

        $facilitators = Facilitator::with('user')->get();
        $scores = [];

        foreach ($facilitators as $facil) {
            $facilText = strtolower(
                ($facil->skills ?? '') . ' ' . 
                ($facil->experience ?? '') . ' ' . 
                ($facil->certifications ?? '')
            );

            $score = 0;
            $matches = [];
            foreach ($targetKeywords as $keyword) {
                if (strpos($facilText, $keyword) !== false) {
                    $score++;
                    $matches[] = $keyword;
                }
            }

            if ($score > 0) {
                $scores[$facil->id] = [
                    'score' => $score,
                    'matches' => $matches
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
    public function recommendEvents($facilitatorId, $limit = 5)
    {
        $facilitator = Facilitator::find($facilitatorId);
        if (!$facilitator) return [];

        $facilText = strtolower(
            ($facilitator->skills ?? '') . ' ' . 
            ($facilitator->experience ?? '')
        );

        // Fetch upcoming events
        $events = Event::where('status', 'upcoming')->get();
        $scores = [];

        foreach ($events as $event) {
            $requiredKeywords = [];
            $cat = strtoupper($event->event_category ?? '');
            
            // Map
            $mapping = [
                'CERAMAH' => 'TALK',
                'KEM' => 'CAMP',
                'KURSUS/BENGKEL' => 'WORKSHOP',
                'PERCUTIAN' => 'HOLIDAY'
            ];
            if (isset($mapping[$cat])) $cat = $mapping[$cat];

            if (isset($this->rules[$cat])) {
                $requiredKeywords = $this->rules[$cat];
            }
            // Add custom
            if ($event->required_skill_tag) {
                $requiredKeywords[] = $event->required_skill_tag;
            }

            // Calculate overlap
            $score = 0;
            $matches = [];
            foreach ($requiredKeywords as $keyword) {
                if (strpos($facilText, strtolower($keyword)) !== false) {
                    $score++;
                    $matches[] = $keyword;
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
                    'id' => $evt->id,
                    'name' => $evt->event_name,
                    'category' => $evt->event_category,
                    'match_score' => $data['score'],
                    'matched_keywords' => implode(', ', array_unique($data['matches']))
                ];
                $count++;
            }
        }
        return $results;
    }

    private function extractKeywordsFromText($text)
    {
        $found = [];
        $text = strtolower($text);
        foreach ($this->rules as $category => $keywords) {
            if (strpos($text, strtolower($category)) !== false) {
                $found = array_merge($found, $keywords);
            }
            foreach ($keywords as $k) {
                if (strpos($text, strtolower($k)) !== false) {
                    $found[] = $k;
                }
            }
        }
        return $found;
    }

    private function formatFacilitatorResults($scores, $facilitators, $limit)
    {
        $results = [];
        $count = 0;
        foreach ($scores as $id => $data) {
            if ($count >= $limit) break;
            $facilitator = $facilitators->find($id);
            if ($facilitator) {
                $results[] = [
                    'id' => $facilitator->id,
                    'name' => $facilitator->user ? $facilitator->user->name : 'Unknown',
                    'match_score' => $data['score'], 
                    'matched_keywords' => implode(', ', array_unique($data['matches'])),
                    'skills' => $facilitator->skills,
                    'experience' => $facilitator->experience
                ];
                $count++;
            }
        }
        return $results;
    }
}
