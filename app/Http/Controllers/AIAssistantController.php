<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Goal;
use App\Models\Milestone;
use App\Helpers\GeminiResponseFormatter;

class AIAssistantController extends Controller
{
    public function askQuestion(Request $request)
    {
        try {
            $request->validate([
                'question' => 'required|string|max:1000',
                'context' => 'array'
            ]);

            $question = $request->input('question');
            $context = $request->input('context', []);
            
            // Check if API key is configured
            if (!env('GEMINI_API_KEY') || env('GEMINI_API_KEY') === 'your_gemini_api_key_here') {
                return $this->getMockResponse($question, $context);
            }
            
            // Build context-aware prompt
            $prompt = $this->buildPrompt($question, $context);
            
            // Call Gemini API
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=' . env('GEMINI_API_KEY'), [
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
                    'maxOutputTokens' => 1000,
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                    $aiResponse = $data['candidates'][0]['content']['parts'][0]['text'];
                    
                    // Format the response to remove unwanted characters
                    $formattedResponse = GeminiResponseFormatter::formatResponse($aiResponse);
                    
                    return response()->json([
                        'success' => true,
                        'response' => $formattedResponse
                    ]);
                } else {
                    Log::error('AI Assistant: Invalid response structure', ['response' => $data]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid response from AI service'
                    ]);
                }
            } else {
                Log::error('AI Assistant: API call failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to get response from AI service'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('AI Assistant: Exception occurred', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your request'
            ]);
        }
    }

    private function buildPrompt($question, $context)
    {
        $basePrompt = "You are an expert learning and productivity coach helping users achieve their goals. ";
        $basePrompt .= "Provide practical, actionable advice in a friendly and encouraging tone. ";
        $basePrompt .= "Keep your responses concise but comprehensive (max 300 words). ";

        // Add context-specific information
        if (isset($context['type']) && isset($context['id'])) {
            if ($context['type'] === 'milestone' && $context['id']) {
                $milestone = Milestone::with('goal')->find($context['id']);
                if ($milestone) {
                    $basePrompt .= "\n\nContext: The user is asking about a milestone titled '{$milestone->title}' ";
                    $basePrompt .= "which is part of their goal '{$milestone->goal->title}'. ";
                    if ($milestone->description) {
                        $basePrompt .= "Milestone description: {$milestone->description}. ";
                    }
                }
            } elseif ($context['type'] === 'goal' && $context['id']) {
                $goal = Goal::with('milestones')->find($context['id']);
                if ($goal) {
                    $basePrompt .= "\n\nContext: The user is asking about their goal titled '{$goal->title}'. ";
                    if ($goal->description) {
                        $basePrompt .= "Goal description: {$goal->description}. ";
                    }
                    $basePrompt .= "This goal has {$goal->milestones->count()} milestones. ";
                }
            }
        }

        $basePrompt .= "\n\nUser question: {$question}";

        return $basePrompt;
    }

    private function getMockResponse($question, $context)
    {
        // Provide helpful mock responses when API key is not configured
        $mockResponses = [
            'break down' => 'Here are some suggestions to break down your milestone:

1. **Start with research** - Gather necessary resources and documentation
2. **Create a learning plan** - Outline what you need to learn step by step  
3. **Practice with small projects** - Build mini-projects to test your understanding
4. **Review and iterate** - Regularly assess your progress and adjust your approach
5. **Get feedback** - Share your work with others for constructive input

Remember to set realistic deadlines for each task and celebrate small wins along the way!',

            'best practices' => 'Here are some best practices for achieving your milestone:

**Planning:**
• Set clear, measurable goals
• Break large tasks into smaller chunks
• Create realistic timelines

**Execution:**
• Focus on one task at a time
• Use the Pomodoro technique for focused work sessions
• Track your progress regularly

**Learning:**
• Practice consistently, even if just 15-30 minutes daily
• Learn from multiple sources (videos, documentation, tutorials)
• Build projects to apply what you learn

**Motivation:**
• Join communities related to your learning topic
• Find an accountability partner
• Celebrate your progress milestones',

            'challenges' => 'Common challenges you might face and how to overcome them:

**Time Management:**
• Solution: Use time-blocking and prioritize high-impact tasks
• Set specific learning schedules and stick to them

**Information Overload:**
• Solution: Focus on one concept at a time
• Choose quality resources over quantity

**Lack of Motivation:**
• Solution: Set small, achievable daily goals
• Connect with like-minded learners for support

**Imposter Syndrome:**
• Solution: Remember that everyone starts as a beginner
• Focus on progress, not perfection

**Technical Difficulties:**
• Solution: Build a strong foundation before advancing
• Don\'t hesitate to ask for help in communities',

            'timeline' => 'Tips for optimizing your timeline:

**Assessment:**
• Evaluate your current skill level honestly
• Identify knowledge gaps that need more time

**Planning:**
• Allocate 20% extra time for unexpected challenges
• Plan for regular review and practice sessions
• Include breaks and buffer time

**Optimization:**
• Schedule learning during your most productive hours
• Group similar tasks together for efficiency
• Use weekends for bigger projects or catch-up

**Flexibility:**
• Review and adjust your timeline weekly
• Be realistic about what you can accomplish
• It\'s better to learn thoroughly than rush through topics'
        ];

        // Simple keyword matching for mock responses
        $questionLower = strtolower($question);
        
        foreach ($mockResponses as $keyword => $response) {
            if (strpos($questionLower, $keyword) !== false) {
                return response()->json([
                    'success' => true,
                    'response' => $response
                ]);
            }
        }

        // Default response
        return response()->json([
            'success' => true,
            'response' => 'Great question! Here are some general tips:

• **Stay consistent** - Regular practice is more effective than intensive cramming
• **Set clear goals** - Know exactly what you want to achieve
• **Track progress** - Keep a learning journal or use progress tracking tools
• **Get hands-on** - Apply what you learn through practical projects
• **Join communities** - Learn from others and share your progress
• **Be patient** - Learning takes time, focus on understanding rather than speed

Remember, every expert was once a beginner. Keep pushing forward and celebrate your progress along the way!'
        ]);
    }
}
