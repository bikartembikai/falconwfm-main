<?php

namespace App\Services;

use App\Models\Facilitator;

class RecommendationService
{
    /**
     * Main function to get recommendations
     */
    public function recommend($eventRequirements, $limit = 5)
    {
        // 1. Fetch all facilitators from DB
        $facilitators = Facilitator::all();
        
        $documents = [];
        $ids = [];

        // 2. Prepare the "Corpus" (The list of all text)
        foreach ($facilitators as $facil) {
            // Combine relevant text fields (Skills + Bio)
            // Ensure data is clean (lowercase)
            $text = strtolower(($facil->skills ?? '') . ' ' . ($facil->bio ?? ''));
            // Skip empty profiles to avoid errors
            if (trim($text) === '') {
                continue;
            }
            $documents[$facil->id] = $this->tokenize($text);
            $ids[] = $facil->id;
        }

        // Add the Event Requirements as the last "document" to compare against
        $eventTokens = $this->tokenize(strtolower($eventRequirements));
        
        // If no facilitators have enough data, return empty
        if (empty($documents)) {
            return [];
        }

        // 3. Calculate TF-IDF
        // This generates the "Math Vectors" for everyone
        $vectors = $this->calculateTfidf($documents, $eventTokens);
        
        // 4. Calculate Cosine Similarity
        // The last vector is our Event. Compare it against all others.
        // We use the key 'EVENT_QUERY' to identify it reliably
        $eventVector = $vectors['EVENT_QUERY'];
        unset($vectors['EVENT_QUERY']);

        $scores = [];

        foreach ($vectors as $facilId => $vector) {
            $scores[$facilId] = $this->cosineSimilarity($eventVector, $vector);
        }

        // 5. Sort by Highest Score
        arsort($scores);

        // 6. Return top matches with details
        return $this->formatResults($scores, $facilitators, $limit);
    }

    // ----------------------------------------------------------------
    // THE MATH ENGINE (Pure PHP Implementation of AI Logic)
    // ----------------------------------------------------------------

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
    private function calculateTfidf($facilitatorDocs, $eventTokens)
    {
        // Combine all docs to build a "Vocabulary" (Unique words)
        $allDocs = $facilitatorDocs;
        $allDocs['EVENT_QUERY'] = $eventTokens;
        
        // 1. Build Vocabulary
        $vocabulary = [];
        foreach ($allDocs as $doc) {
            foreach ($doc as $word) {
                $vocabulary[$word] = 0;
            }
        }
        $vocabularyKeys = array_keys($vocabulary);

        // 2. Calculate IDF (How rare is a word?)
        $idf = [];
        $totalDocs = count($allDocs);
        
        foreach ($vocabularyKeys as $term) {
            $docCount = 0;
            foreach ($allDocs as $doc) {
                if (in_array($term, $doc)) $docCount++;
            }
            // Logarithmic scale (standard IDF formula)
            // Add 1 to denominator to avoid division by zero if term not found (unlikely here but safe)
            $idf[$term] = log($totalDocs / (1 + $docCount)) + 1; // +1 to ensure positive IDF
        }

        // 3. Calculate TF-IDF Vectors
        $vectors = [];
        foreach ($allDocs as $key => $doc) {
            $vector = [];
            
            // Count word frequency in this specific document
            $termCounts = array_count_values($doc);
            $docLength = count($doc);
            
            if ($docLength == 0) {
                 // Handle empty document case
                 foreach ($vocabularyKeys as $term) {
                    $vector[] = 0;
                 }
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

    /**
     * Step 4: Calculate Cosine Similarity between two vectors
     * Formula: (A . B) / (||A|| * ||B||)
     */
    private function cosineSimilarity($vecA, $vecB)
    {
        $dotProduct = 0;
        $normA = 0;
        $normB = 0;

        foreach ($vecA as $i => $valA) {
            $valB = $vecB[$i];
            $dotProduct += $valA * $valB;
            $normA += $valA * $valA;
            $normB += $valB * $valB;
        }

        if ($normA == 0 || $normB == 0) return 0;

        return $dotProduct / (sqrt($normA) * sqrt($normB));
    }

    private function formatResults($scores, $facilitators, $limit)
    {
        $results = [];
        $count = 0;
        
        foreach ($scores as $id => $score) {
            if ($count >= $limit) break;
            
            // Facilitator might vary if we used 'all()' vs filtered. 
            // Here $facilitators is a Collection.
            $facilitator = $facilitators->find($id);
            
            if ($facilitator) {
                $results[] = [
                    'facilitator_id' => $facilitator->id,
                    'name' => $facilitator->user ? $facilitator->user->name : 'Unknown', // Access via user relation
                    'profile_picture' => $facilitator->profile_picture,
                    'match_score' => round($score * 100, 1), 
                    'skills' => $facilitator->skills,
                    'bio' => $facilitator->bio
                ];
                $count++;
            }
        }
        return $results;
    }
}
