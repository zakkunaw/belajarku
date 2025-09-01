<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AIRoadmapController extends Controller
{
    /**
     * Generate AI roadmap based on user input.
     */
    public function generateRoadmap(Request $request)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return response()->json([
                'message' => 'Unauthorized. Please login first.'
            ], 401);
        }

        $request->validate([
            'goal_title' => 'required|string|max:255',
            'target_date' => 'required|date',
            'self_assessed_level' => 'required|in:beginner,intermediate,advanced',
            'constraints' => 'nullable|string',
            'top_topics' => 'nullable|string'
        ]);

        try {
            // Calculate weeks between now and target date
            $startDate = Carbon::now();
            $targetDate = Carbon::parse($request->target_date);
            $totalWeeks = $startDate->diffInWeeks($targetDate);
            
            // Ensure we have at least 1 week
            $totalWeeks = max(1, min($totalWeeks, 24)); // Max 24 weeks
            
            // Try to generate roadmap using Gemini AI
            $roadmap = $this->generateGeminiRoadmap(
                $request->goal_title,
                $request->self_assessed_level,
                $totalWeeks,
                $request->constraints,
                $request->top_topics
            );

            // If Gemini fails, fallback to mock
            if (!$roadmap) {
                $roadmap = $this->generateMockRoadmap(
                    $request->goal_title,
                    $request->self_assessed_level,
                    $totalWeeks,
                    $request->constraints,
                    $request->top_topics
                );
            }

            return response()->json($roadmap);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat generate roadmap: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate roadmap using Gemini AI.
     */
    private function generateGeminiRoadmap(string $goalTitle, string $level, int $weeks, ?string $constraints, ?string $topTopics): ?array
    {
        try {
            $apiKey = 'AIzaSyDmkQPx2sRiZfXLaCqpGPcjJmeFpTJdTX4';
            
            // Construct prompt for Gemini
            $prompt = $this->buildGeminiPrompt($goalTitle, $level, $weeks, $constraints, $topTopics);
            
            $payload = [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $prompt
                            ]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'topK' => 40,
                    'topP' => 0.95,
                    'maxOutputTokens' => 2048,
                ]
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'X-goog-api-key: ' . $apiKey
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode !== 200) {
                error_log("Gemini API error: HTTP $httpCode - $response");
                return null;
            }

            $responseData = json_decode($response, true);
            
            if (!isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
                error_log("Gemini API response format error: " . $response);
                return null;
            }

            $aiText = $responseData['candidates'][0]['content']['parts'][0]['text'];
            
            // Parse AI response to extract roadmap data
            return $this->parseGeminiResponse($aiText, $goalTitle, $level, $weeks);

        } catch (\Exception $e) {
            error_log("Gemini AI error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Build prompt for Gemini AI.
     */
    private function buildGeminiPrompt(string $goalTitle, string $level, int $weeks, ?string $constraints, ?string $topTopics): string
    {
        $prompt = "Buat roadmap pembelajaran {$weeks} minggu untuk mencapai tujuan: '{$goalTitle}'\n\n";
        $prompt .= "Level skill saat ini: {$level}\n";
        
        if ($constraints) {
            $prompt .= "Batasan/Keterbatasan: {$constraints}\n";
        }
        
        if ($topTopics) {
            $prompt .= "Topik prioritas: {$topTopics}\n";
        }
        
        $prompt .= "\nFormat response dalam JSON seperti ini:\n";
        $prompt .= "{\n";
        $prompt .= '  "goal_title": "' . $goalTitle . '",' . "\n";
        $prompt .= '  "level": "' . $level . '",' . "\n";
        $prompt .= '  "total_weeks": ' . $weeks . ',' . "\n";
        $prompt .= '  "generated_at": "' . now()->toISOString() . '",' . "\n";
        $prompt .= '  "weeks": [' . "\n";
        $prompt .= '    {' . "\n";
        $prompt .= '      "week": 1,' . "\n";
        $prompt .= '      "theme": "Nama tema minggu ini",' . "\n";
        $prompt .= '      "outcomes": ["Target 1", "Target 2", "Target 3"],' . "\n";
        $prompt .= '      "tasks": ["Task 1", "Task 2", "Task 3"],' . "\n";
        $prompt .= '      "resources": ["Resource 1", "Resource 2"]' . "\n";
        $prompt .= '    }' . "\n";
        $prompt .= '  ]' . "\n";
        $prompt .= "}\n\n";
        $prompt .= "Pastikan:\n";
        $prompt .= "- Setiap minggu memiliki tema yang jelas dan progresif\n";
        $prompt .= "- Tasks konkret dan actionable\n";
        $prompt .= "- Resources relevan dan mudah diakses\n";
        $prompt .= "- Sesuai dengan level skill yang disebutkan\n";
        $prompt .= "- Mempertimbangkan batasan yang ada\n";
        $prompt .= "- Response harus valid JSON yang bisa di-parse\n";

        return $prompt;
    }

    /**
     * Parse Gemini AI response to extract roadmap data.
     */
    private function parseGeminiResponse(string $aiText, string $goalTitle, string $level, int $weeks): array
    {
        // Try to extract JSON from AI response
        $jsonStart = strpos($aiText, '{');
        $jsonEnd = strrpos($aiText, '}');
        
        if ($jsonStart !== false && $jsonEnd !== false) {
            $jsonText = substr($aiText, $jsonStart, $jsonEnd - $jsonStart + 1);
            $roadmapData = json_decode($jsonText, true);
            
            if ($roadmapData && isset($roadmapData['weeks'])) {
                return $roadmapData;
            }
        }
        
        // If JSON parsing fails, fallback to mock
        return $this->generateMockRoadmap($goalTitle, $level, $weeks, null, null);
    }

    /**
     * Generate mock AI roadmap (replace with actual AI integration).
     */
    private function generateMockRoadmap(string $goalTitle, string $level, int $weeks, ?string $constraints, ?string $topTopics): array
    {
        // This is a mock implementation. In production, you would:
        // 1. Send the data to OpenAI API with a structured prompt
        // 2. Parse the AI response
        // 3. Return the structured roadmap

        $roadmapTemplates = $this->getRoadmapTemplates();
        $template = $this->selectTemplate($goalTitle, $level);
        
        $generatedWeeks = [];
        
        for ($week = 1; $week <= min($weeks, 12); $week++) {
            $weekData = $this->generateWeekData($week, $template, $level, $constraints, $topTopics);
            $generatedWeeks[] = $weekData;
        }

        return [
            'goal_title' => $goalTitle,
            'level' => $level,
            'total_weeks' => count($generatedWeeks),
            'generated_at' => now()->toISOString(),
            'weeks' => $generatedWeeks
        ];
    }

    /**
     * Generate data for a specific week.
     */
    private function generateWeekData(int $week, array $template, string $level, ?string $constraints, ?string $topTopics): array
    {
        $themes = $template['themes'];
        $themeIndex = min($week - 1, count($themes) - 1);
        $theme = $themes[$themeIndex];

        // Adjust complexity based on level
        $complexityMultiplier = match($level) {
            'beginner' => 0.7,
            'intermediate' => 1.0,
            'advanced' => 1.3,
            default => 1.0
        };

        $baseTaskCount = 3;
        $taskCount = max(2, round($baseTaskCount * $complexityMultiplier));

        return [
            'week' => $week,
            'theme' => $theme['title'],
            'outcomes' => $this->generateOutcomes($theme, $level),
            'tasks' => $this->generateTasks($theme, $taskCount, $level),
            'resources' => $this->generateResources($theme, $level)
        ];
    }

    /**
     * Get roadmap templates based on common learning paths.
     */
    private function getRoadmapTemplates(): array
    {
        return [
            'web_development' => [
                'keywords' => ['web', 'frontend', 'backend', 'fullstack', 'html', 'css', 'javascript', 'react', 'vue', 'angular', 'node', 'laravel', 'php'],
                'themes' => [
                    ['title' => 'HTML & CSS Fundamentals', 'focus' => 'Structure and styling'],
                    ['title' => 'JavaScript Basics', 'focus' => 'Programming fundamentals'],
                    ['title' => 'DOM Manipulation', 'focus' => 'Interactive web pages'],
                    ['title' => 'Frontend Framework', 'focus' => 'Modern development'],
                    ['title' => 'Backend Basics', 'focus' => 'Server-side development'],
                    ['title' => 'Database Integration', 'focus' => 'Data persistence'],
                    ['title' => 'API Development', 'focus' => 'Web services'],
                    ['title' => 'Authentication & Security', 'focus' => 'User management'],
                    ['title' => 'Testing & Debugging', 'focus' => 'Quality assurance'],
                    ['title' => 'Deployment & DevOps', 'focus' => 'Production deployment'],
                    ['title' => 'Performance Optimization', 'focus' => 'Speed and efficiency'],
                    ['title' => 'Advanced Patterns', 'focus' => 'Best practices']
                ]
            ],
            'data_science' => [
                'keywords' => ['data', 'python', 'machine learning', 'ai', 'analytics', 'statistics', 'pandas', 'numpy', 'tensorflow'],
                'themes' => [
                    ['title' => 'Python Programming', 'focus' => 'Programming basics'],
                    ['title' => 'Data Manipulation', 'focus' => 'Pandas and NumPy'],
                    ['title' => 'Data Visualization', 'focus' => 'Charts and graphs'],
                    ['title' => 'Statistical Analysis', 'focus' => 'Statistics fundamentals'],
                    ['title' => 'Machine Learning Basics', 'focus' => 'ML algorithms'],
                    ['title' => 'Supervised Learning', 'focus' => 'Prediction models'],
                    ['title' => 'Unsupervised Learning', 'focus' => 'Pattern discovery'],
                    ['title' => 'Deep Learning', 'focus' => 'Neural networks'],
                    ['title' => 'Model Evaluation', 'focus' => 'Performance metrics'],
                    ['title' => 'Data Pipeline', 'focus' => 'End-to-end workflow'],
                    ['title' => 'Cloud Deployment', 'focus' => 'Production models'],
                    ['title' => 'Advanced Techniques', 'focus' => 'Specialized methods']
                ]
            ],
            'mobile_development' => [
                'keywords' => ['mobile', 'android', 'ios', 'flutter', 'react native', 'swift', 'kotlin', 'app'],
                'themes' => [
                    ['title' => 'Mobile Development Basics', 'focus' => 'Platform overview'],
                    ['title' => 'UI/UX Design', 'focus' => 'User interface'],
                    ['title' => 'Navigation & Routing', 'focus' => 'App flow'],
                    ['title' => 'State Management', 'focus' => 'Data handling'],
                    ['title' => 'API Integration', 'focus' => 'Network requests'],
                    ['title' => 'Local Storage', 'focus' => 'Data persistence'],
                    ['title' => 'Device Features', 'focus' => 'Camera, GPS, etc.'],
                    ['title' => 'Push Notifications', 'focus' => 'User engagement'],
                    ['title' => 'Testing & Debugging', 'focus' => 'Quality assurance'],
                    ['title' => 'App Store Deployment', 'focus' => 'Publishing'],
                    ['title' => 'Performance Optimization', 'focus' => 'Speed and battery'],
                    ['title' => 'Advanced Features', 'focus' => 'Platform-specific']
                ]
            ],
            'general' => [
                'keywords' => [],
                'themes' => [
                    ['title' => 'Foundation & Setup', 'focus' => 'Getting started'],
                    ['title' => 'Core Concepts', 'focus' => 'Basic understanding'],
                    ['title' => 'Practical Application', 'focus' => 'Hands-on practice'],
                    ['title' => 'Intermediate Skills', 'focus' => 'Building complexity'],
                    ['title' => 'Advanced Techniques', 'focus' => 'Specialized knowledge'],
                    ['title' => 'Real-world Projects', 'focus' => 'Portfolio building'],
                    ['title' => 'Best Practices', 'focus' => 'Industry standards'],
                    ['title' => 'Performance & Optimization', 'focus' => 'Efficiency'],
                    ['title' => 'Testing & Quality', 'focus' => 'Reliability'],
                    ['title' => 'Deployment & Production', 'focus' => 'Going live'],
                    ['title' => 'Maintenance & Updates', 'focus' => 'Long-term care'],
                    ['title' => 'Mastery & Innovation', 'focus' => 'Expert level']
                ]
            ]
        ];
    }

    /**
     * Select appropriate template based on goal title.
     */
    private function selectTemplate(string $goalTitle, string $level): array
    {
        $templates = $this->getRoadmapTemplates();
        $goalLower = strtolower($goalTitle);

        foreach ($templates as $category => $template) {
            foreach ($template['keywords'] as $keyword) {
                if (str_contains($goalLower, $keyword)) {
                    return $template;
                }
            }
        }

        return $templates['general'];
    }

    /**
     * Generate learning outcomes for a theme.
     */
    private function generateOutcomes(array $theme, string $level): array
    {
        $baseOutcomes = [
            "Memahami konsep dasar {$theme['title']}",
            "Mampu menerapkan {$theme['focus']} dalam project",
            "Dapat troubleshoot masalah umum"
        ];

        if ($level === 'advanced') {
            $baseOutcomes[] = "Menguasai teknik lanjutan dan best practices";
            $baseOutcomes[] = "Dapat mengoptimalkan performance dan efisiensi";
        }

        return $baseOutcomes;
    }

    /**
     * Generate tasks for a theme.
     */
    private function generateTasks(array $theme, int $taskCount, string $level): array
    {
        $baseTasks = [
            "Pelajari dokumentasi {$theme['title']}",
            "Ikuti tutorial interaktif",
            "Buat project sederhana untuk praktik",
            "Review dan refactor code yang dibuat",
            "Cari dan baca artikel/blog terkait"
        ];

        if ($level === 'beginner') {
            array_unshift($baseTasks, "Siapkan environment dan tools yang dibutuhkan");
        }

        if ($level === 'advanced') {
            $baseTasks[] = "Analisis performa dan optimasi";
            $baseTasks[] = "Implementasi advanced patterns";
        }

        return array_slice($baseTasks, 0, $taskCount);
    }

    /**
     * Generate learning resources for a theme.
     */
    private function generateResources(array $theme, string $level): array
    {
        $baseResources = [
            "Dokumentasi resmi {$theme['title']}",
            "Tutorial video di YouTube",
            "Artikel dan blog posts"
        ];

        if ($level === 'beginner') {
            $baseResources[] = "Interactive coding platforms (Codecademy, FreeCodeCamp)";
        }

        if ($level === 'advanced') {
            $baseResources[] = "Advanced courses dan certification";
            $baseResources[] = "Open source projects untuk kontribusi";
        }

        return $baseResources;
    }
}
