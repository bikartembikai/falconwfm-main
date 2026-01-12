<?php

namespace App\Services;

use App\Models\Facilitator;

class RecommendationService
{
    /**
     * Main function to get recommendations
     */
    /**
     * Recommend Facilitators for an Event
     */
    public function recommend($eventRequirements, $limit = 5)
    {
        // 1. Fetch all facilitators from DB
        $facilitators = Facilitator::with('user')->get(); // Eager load user
        
        $documents = [];
        $ids = [];

        // 2. Prepare the Corpus
        foreach ($facilitators as $facil) {
            // New schema: skills + experience + certifications
            $text = strtolower(
                ($facil->skills ?? '') . ' ' . 
                ($facil->experience ?? '') . ' ' . 
                ($facil->certifications ?? '')
            );
            
            if (trim($text) === '') continue;
            
            $documents[$facil->id] = $this->tokenize($text);
            $ids[] = $facil->id;
        }

        $eventTokens = $this->tokenize(strtolower($eventRequirements));
        
        if (empty($documents)) return [];

        // 3. TF-IDF & Cosine
        $vectors = $this->calculateTfidf($documents, $eventTokens);
        
        $eventVector = $vectors['EVENT_QUERY'];
        unset($vectors['EVENT_QUERY']);

        $scores = [];
        foreach ($vectors as $facilId => $vector) {
            $scores[$facilId] = $this->cosineSimilarity($eventVector, $vector);
        }

        arsort($scores);

        return $this->formatFacilitatorResults($scores, $facilitators, $limit);
    }

    /**
     * Recommend Events for a Facilitator (New Bidirectional Feature)
     */
    public function recommendEvents($facilitatorId, $limit = 5)
    {
        $facilitator = Facilitator::find($facilitatorId);
        if (!$facilitator) return [];

        // Facilitator Profile Text
        $facilitatorText = strtolower(
            ($facilitator->skills ?? '') . ' ' . 
            ($facilitator->experience ?? '') . ' ' .
            ($facilitator->certifications ?? '')
        );
        $facilitatorTokens = $this->tokenize($facilitatorText);

        // Fetch all upcoming events
        $events = \App\Models\Event::where('status', 'upcoming')->get();
        if ($events->isEmpty()) return [];

        $documents = [];
        foreach ($events as $event) {
            // Event Profile: Name + Description + Category + Required Skills
            $text = strtolower(
                $event->event_name . ' ' . 
                ($event->event_description ?? '') . ' ' . 
                ($event->event_category ?? '') . ' ' . 
                ($event->required_skill_tag ?? '')
            );
            $documents[$event->id] = $this->tokenize($text);
        }

        // Add Facilitator as the "Query" document
        $documents['FACILITATOR_QUERY'] = $facilitatorTokens;

        // Calculate
        $vectors = $this->calculateTfidf($documents, []); // 2nd arg unused here really, simplified logic below could be better but reusing func
        
        // TF-IDF logic needs slight adjusting for "Query" being inside documents or separate.
        // Let's reuse calculateTfidf but pass Facilitator Tokens as the "EventTokens" arg effectively
        // Actually, my calculateTfidf merges the 2nd arg.
        
        // RE-CALL properly:
        // Generic approach: (All Docs, Query Tokens)
        unset($documents['FACILITATOR_QUERY']); // Remove from list, pass as query
        $vectors = $this->calculateTfidf($documents, $facilitatorTokens);

        $queryVector = $vectors['EVENT_QUERY']; // calculateTfidf names key 'EVENT_QUERY'
        unset($vectors['EVENT_QUERY']);

        $scores = [];
        foreach ($vectors as $eventId => $vector) {
            $scores[$eventId] = $this->cosineSimilarity($queryVector, $vector);
        }

        arsort($scores);

        return $this->formatEventResults($scores, $events, $limit);
    }

    // ... Math Engine ... (Tokenize, Tfidf, Cosine - Unchanged, keeping helper methods below)

    /**
     * Helper: Convert string to array of words
     */
    private function tokenize($text)
    {
        // Remove punctuation and split by space
        $text = preg_replace('/[^\w\s]/', '', $text);
        $words = explode(' ', $text);
        
        // Filter out "Stop Words" (the, is, and, etc.)
        $stopWords = ['the', 'and', 'is', 'of', 'to', 'a', 'in', 'for', 'with', 'or', 'at', 'by'];
        
        // Filter empty strings and stop words
        return array_filter($words, function($word) use ($stopWords) {
            return !empty($word) && !in_array($word, $stopWords);
        });
    }

    /**
     * Step 3: Calculate Term Frequency - Inverse Document Frequency
     */
    private function calculateTfidf($docs, $queryTokens)
    {
        $allDocs = $docs;
        $allDocs['EVENT_QUERY'] = $queryTokens; // Always use this key for the query vector
        
        // 1. Build Vocabulary
        $vocabulary = [];
        foreach ($allDocs as $doc) {
            foreach ($doc as $word) {
                $vocabulary[$word] = 0;
            }
        }
        $vocabularyKeys = array_keys($vocabulary);

        // 2. Calculate IDF 
        $idf = [];
        $totalDocs = count($allDocs);
        
        foreach ($vocabularyKeys as $term) {
            $docCount = 0;
            foreach ($allDocs as $doc) {
                if (in_array($term, $doc)) $docCount++;
            }
            $idf[$term] = log($totalDocs / (1 + $docCount)) + 1; 
        }

        // 3. Calculate Vectors
        $vectors = [];
        foreach ($allDocs as $key => $doc) {
            $vector = [];
            $termCounts = array_count_values($doc);
            $docLength = count($doc);
            
            if ($docLength == 0) {
                 foreach ($vocabularyKeys as $term) $vector[] = 0;
            } else {
                foreach ($vocabularyKeys as $term) {
                    $tf = isset($termCounts[$term]) ? ($termCounts[$term] / $docLength) : 0;
                    $vector[] = $tf * $idf[$term];
                }
            }
            $vectors[$key] = $vector;
        }

        return $vectors;
    }

    private function cosineSimilarity($vecA, $vecB)
    {
        $dotProduct = 0; $normA = 0; $normB = 0;

        foreach ($vecA as $i => $valA) {
            $valB = $vecB[$i];
            $dotProduct += $valA * $valB;
            $normA += $valA * $valA;
            $normB += $valB * $valB;
        }

        if ($normA == 0 || $normB == 0) return 0;
        return $dotProduct / (sqrt($normA) * sqrt($normB));
    }

    private function formatFacilitatorResults($scores, $facilitators, $limit)
    {
        $results = [];
        $count = 0;
        foreach ($scores as $id => $score) {
            if ($count >= $limit) break;
            $facilitator = $facilitators->find($id);
            if ($facilitator) {
                $results[] = [
                    'id' => $facilitator->id,
                    'name' => $facilitator->user ? $facilitator->user->name : 'Unknown',
                    'match_score' => round($score * 100, 1), 
                    'skills' => $facilitator->skills,
                    'experience' => $facilitator->experience
                ];
                $count++;
            }
        }
        return $results;
    }

    private function formatEventResults($scores, $events, $limit)
    {
        $results = [];
        $count = 0;
        foreach ($scores as $id => $score) {
            if ($count >= $limit) break;
            $event = $events->find($id);
            if ($event) {
                $results[] = [
                    'id' => $event->id,
                    'name' => $event->event_name,
                    'match_score' => round($score * 100, 1),
                    'required_skills' => $event->required_skill_tag,
                    'start_date' => $event->start_date_time,
                ];
                $count++;
            }
        }
        return $results;
    }
}
