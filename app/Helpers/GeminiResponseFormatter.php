<?php

namespace App\Helpers;

class GeminiResponseFormatter
{
    /**
     * Clean and format Gemini AI response to remove markdown artifacts
     */
    public static function formatResponse(string $response): string
    {
        // Remove triple asterisks patterns first (*** text ***)
        $response = preg_replace('/\*\*\*\s*([^*]+?)\s*\*\*\*/', '$1', $response);
        
        // Remove patterns like ** text ** (bold markdown with spaces)
        $response = preg_replace('/\*\*\s*([^*]+?)\s*\*\*/', '$1', $response);
        
        // Remove patterns like **text** (bold markdown without spaces)
        $response = preg_replace('/\*\*([^*]+?)\*\*/', '$1', $response);
        
        // Convert single asterisk bullet points to proper bullets (must be at start of line)
        $response = preg_replace('/^\*\s(.+)$/m', '• $1', $response);
        
        // Remove markdown headers (# ## ###)
        $response = preg_replace('/^#{1,6}\s*(.+)$/m', '$1', $response);
        
        // Convert numbered lists properly
        $response = preg_replace('/^(\d+)\.\s*(.+)$/m', '$1. $2', $response);
        
        // Remove any remaining multiple asterisks (2 or more) that weren't caught
        $response = preg_replace('/\*{2,}/', '', $response);
        
        // Remove excessive line breaks (3 or more)
        $response = preg_replace('/\n{3,}/', "\n\n", $response);
        
        // Clean up multiple spaces
        $response = preg_replace('/\s{2,}/', ' ', $response);
        
        return trim($response);
    }
    
    /**
     * Parse AI response to extract structured roadmap data
     */
    public static function parseRoadmapResponse(string $response): array
    {
        $formatted = self::formatResponse($response);
        $lines = explode("\n", $formatted);
        
        $roadmap = [
            'weeks' => []
        ];
        
        $currentWeek = null;
        $weekCounter = 1;
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            // Detect week headers
            if (preg_match('/Week\s*(\d+)/i', $line, $matches) || 
                preg_match('/Minggu\s*(\d+)/i', $line, $matches)) {
                
                if ($currentWeek) {
                    $roadmap['weeks'][] = $currentWeek;
                }
                
                $weekNumber = isset($matches[1]) ? (int)$matches[1] : $weekCounter++;
                $theme = preg_replace('/Week\s*\d+:?\s*/i', '', $line);
                $theme = preg_replace('/Minggu\s*\d+:?\s*/i', '', $theme);
                
                $currentWeek = [
                    'week' => $weekNumber,
                    'theme' => $theme ?: 'Learning Week ' . $weekNumber,
                    'tasks' => [],
                    'outcomes' => [],
                    'resources' => []
                ];
                
            } elseif ($currentWeek && preg_match('/^[•-]\s*(.+)$/', $line, $matches)) {
                // Task items
                $currentWeek['tasks'][] = trim($matches[1]);
                
            } elseif ($currentWeek && preg_match('/^\d+\.\s*(.+)$/', $line, $matches)) {
                // Numbered tasks
                $currentWeek['tasks'][] = trim($matches[1]);
                
            } elseif ($currentWeek && strlen($line) > 10) {
                // General description or outcome
                $currentWeek['outcomes'][] = $line;
            }
        }
        
        // Add the last week
        if ($currentWeek) {
            $roadmap['weeks'][] = $currentWeek;
        }
        
        return $roadmap;
    }
}
