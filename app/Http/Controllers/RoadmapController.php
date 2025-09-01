<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use App\Models\Milestone;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RoadmapController extends Controller
{
    /**
     * Display the roadmap creation page.
     */
    public function index()
    {
        return view('roadmap');
    }

    /**
     * Import roadmap data (manual or AI-generated) and create goals/milestones/tasks.
     */
    public function import(Request $request)
    {
        $request->validate([
            'type' => 'required|in:manual_json,manual_form,ai_generated',
            'goal_title' => 'required|string|max:255',
            'target_date' => 'required|date',
            'roadmap_data' => 'required|array'
        ]);

        try {
            DB::beginTransaction();

            $user = Auth::user();
            
            // Create the main goal
            $goal = Goal::create([
                'user_id' => $user->id,
                'title' => $request->goal_title,
                'description' => $this->generateGoalDescription($request->type, $request->roadmap_data),
                'target_date' => $request->target_date,
                'status' => 'active',
                'progress_percentage' => 0
            ]);

            // Process roadmap data based on type
            switch ($request->type) {
                case 'manual_json':
                case 'manual_form':
                    $this->processManualRoadmap($goal, $request->roadmap_data);
                    break;
                
                case 'ai_generated':
                    $this->processAIRoadmap($goal, $request->roadmap_data);
                    break;
            }

            DB::commit();

            return response()->json([
                'message' => 'Roadmap berhasil dibuat!',
                'goal_id' => $goal->id
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process manual roadmap data (JSON or Form format).
     */
    private function processManualRoadmap(Goal $goal, array $roadmapData)
    {
        $milestones = $roadmapData['milestones'] ?? [];
        
        foreach ($milestones as $index => $milestoneData) {
            // Validate milestone title
            $milestoneTitle = trim($milestoneData['title'] ?? '');
            if (empty($milestoneTitle)) {
                $milestoneTitle = "Milestone " . ($index + 1);
            }
            
            // Limit milestone title to 500 characters
            $milestoneTitle = substr($milestoneTitle, 0, 500);
            
            $milestone = Milestone::create([
                'goal_id' => $goal->id,
                'title' => $milestoneTitle,
                'description' => $milestoneData['description'] ?? '',
                'target_date' => $milestoneData['target_date'] ?? null,
                'status' => 'pending',
                'order_index' => $index
            ]);

            // Create tasks for this milestone
            $tasks = $milestoneData['tasks'] ?? [];
            foreach ($tasks as $taskIndex => $taskData) {
                $taskTitle = trim($taskData['title'] ?? '');
                
                if (!empty($taskTitle)) {
                    // Limit task title and split if too long
                    if (strlen($taskTitle) > 500) {
                        // Split long task into multiple shorter tasks
                        $chunks = str_split($taskTitle, 400);
                        foreach ($chunks as $chunkIndex => $chunk) {
                            $chunkTitle = trim($chunk);
                            if (!empty($chunkTitle)) {
                                $finalTitle = $chunkIndex > 0 ? "(...continued) " . $chunkTitle : $chunkTitle;
                                
                                Task::create([
                                    'milestone_id' => $milestone->id,
                                    'title' => $finalTitle,
                                    'description' => $taskData['description'] ?? '',
                                    'status' => 'pending',
                                    'due_date' => $taskData['due_date'] ?? null
                                ]);
                            }
                        }
                    } else {
                        Task::create([
                            'milestone_id' => $milestone->id,
                            'title' => $taskTitle,
                            'description' => $taskData['description'] ?? '',
                            'status' => 'pending',
                            'due_date' => $taskData['due_date'] ?? null
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Process AI-generated roadmap data.
     */
    private function processAIRoadmap(Goal $goal, array $roadmapData)
    {
        $weeks = $roadmapData['weeks'] ?? [];
        $startDate = Carbon::parse($goal->created_at);
        
        foreach ($weeks as $index => $week) {
            // Create milestone for each week
            $weekStartDate = $startDate->copy()->addWeeks($index);
            $weekEndDate = $weekStartDate->copy()->addWeek()->subDay();
            
            $weekNumber = $week['week'] ?? ($index + 1);
            $theme = $week['theme'] ?? 'Weekly Learning';
            $milestoneTitle = "Week {$weekNumber}: {$theme}";
            
            // Limit milestone title to 500 characters
            $milestoneTitle = substr($milestoneTitle, 0, 500);
            
            $milestone = Milestone::create([
                'goal_id' => $goal->id,
                'title' => $milestoneTitle,
                'description' => $this->formatWeekDescription($week),
                'target_date' => $weekEndDate->toDateString(),
                'status' => 'pending',
                'order_index' => $index
            ]);

            // Create tasks from week data
            $tasks = $week['tasks'] ?? [];
            foreach ($tasks as $taskIndex => $taskTitle) {
                $taskDueDate = $weekStartDate->copy()->addDays($taskIndex + 1);
                
                // Clean and validate task title
                $cleanTaskTitle = trim($taskTitle);
                if (!empty($cleanTaskTitle)) {
                    // Handle long task titles by splitting them
                    if (strlen($cleanTaskTitle) > 500) {
                        $chunks = str_split($cleanTaskTitle, 400);
                        foreach ($chunks as $chunkIndex => $chunk) {
                            $chunkTitle = trim($chunk);
                            if (!empty($chunkTitle)) {
                                $finalTitle = $chunkIndex > 0 ? "(...continued) " . $chunkTitle : $chunkTitle;
                                
                                Task::create([
                                    'milestone_id' => $milestone->id,
                                    'title' => $finalTitle,
                                    'description' => '',
                                    'status' => 'pending',
                                    'due_date' => $taskDueDate->toDateString()
                                ]);
                            }
                        }
                    } else {
                        Task::create([
                            'milestone_id' => $milestone->id,
                            'title' => $cleanTaskTitle,
                            'description' => '',
                            'status' => 'pending',
                            'due_date' => $taskDueDate->toDateString()
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Generate goal description based on roadmap type.
     */
    private function generateGoalDescription(string $type, array $roadmapData): string
    {
        switch ($type) {
            case 'manual_json':
                return 'Roadmap dibuat secara manual menggunakan format JSON.';
            
            case 'manual_form':
                $milestoneCount = count($roadmapData['milestones'] ?? []);
                return "Roadmap dibuat secara manual dengan {$milestoneCount} milestone.";
            
            case 'ai_generated':
                $weekCount = count($roadmapData['weeks'] ?? []);
                return "Roadmap generated oleh AI dengan {$weekCount} minggu pembelajaran.";
            
            default:
                return 'Roadmap pembelajaran yang disesuaikan.';
        }
    }

    /**
     * Format week description for AI-generated roadmap.
     */
    private function formatWeekDescription(array $week): string
    {
        $description = "Tema: {$week['theme']}\n\n";
        
        if (!empty($week['outcomes'])) {
            $description .= "Target Outcomes:\n";
            foreach ($week['outcomes'] as $outcome) {
                $description .= "• {$outcome}\n";
            }
            $description .= "\n";
        }
        
        if (!empty($week['resources'])) {
            $description .= "Resources:\n";
            foreach ($week['resources'] as $resource) {
                $description .= "• {$resource}\n";
            }
        }
        
        return $description;
    }
}
