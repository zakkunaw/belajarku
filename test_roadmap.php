<?php

require_once 'vendor/autoload.php';

// Bootstrap the application
$app = require_once 'bootstrap/app.php';

$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Goal;
use App\Models\Milestone;
use App\Models\Task;

echo "Testing Roadmap Creation...\n";

try {
    // Find the first user
    $user = User::first();
    if (!$user) {
        echo "No user found. Creating test user...\n";
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);
    }
    
    echo "User: {$user->name} (ID: {$user->id})\n";
    
    // Create a test goal
    $goal = Goal::create([
        'user_id' => $user->id,
        'title' => 'Test Laravel Development',
        'description' => 'Learn Laravel framework basics',
        'status' => 'in_progress',
        'start_date' => now(),
        'target_date' => now()->addMonth(),
        'progress_percentage' => 0,
    ]);
    
    echo "Goal created: {$goal->title} (ID: {$goal->id})\n";
    
    // Create a test milestone with the new structure
    $milestone = Milestone::create([
        'goal_id' => $goal->id,
        'title' => 'Learn MVC Pattern',
        'description' => 'Understand the Model-View-Controller pattern in Laravel',
        'target_date' => now()->addWeek(),
        'status' => 'pending',
        'order_index' => 1,
    ]);
    
    echo "Milestone created: {$milestone->title} (ID: {$milestone->id})\n";
    
    // Create a test task with the new structure
    $task = Task::create([
        'milestone_id' => $milestone->id,
        'title' => 'Study Laravel Documentation',
        'description' => 'Read the official Laravel documentation on MVC',
        'due_date' => now()->addDays(3),
        'status' => 'pending',
    ]);
    
    echo "Task created: {$task->title} (ID: {$task->id})\n";
    
    echo "\n✅ Success! All database operations completed without errors.\n";
    echo "Database structure is now properly aligned.\n";
    
    // Display the structure to verify
    echo "\nCreated structure:\n";
    echo "Goal: {$goal->title}\n";
    echo "  └─ Milestone: {$milestone->title} (status: {$milestone->status}, target_date: {$milestone->target_date})\n";
    echo "      └─ Task: {$task->title} (status: {$task->status}, due_date: {$task->due_date})\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
