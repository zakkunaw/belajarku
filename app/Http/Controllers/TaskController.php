<?php

namespace App\Http\Controllers;

use App\Models\Milestone;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    public function store(Request $request, Milestone $milestone)
    {
        // Ensure user can only add tasks to milestones of their own goals
        if ($milestone->goal->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'due_date' => 'nullable|date',
        ]);

        try {
            Task::create([
                'milestone_id' => $milestone->id,
                'title' => $validated['title'],
                'description' => $validated['description'] ?? '',
                'due_date' => $validated['due_date'] ?? null,
                'status' => 'todo', // Use 'todo' instead of STATUS_PENDING
            ]);

            return redirect()->route('goals')
                ->with('status', 'Task berhasil ditambahkan!');

        } catch (\Exception $e) {
            Log::error('Error creating task: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors(['error' => 'Terjadi kesalahan saat menambahkan task: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function updateStatus(Request $request, Task $task)
    {
        // Ensure user can only update tasks of their own goals
        if ($task->milestone->goal->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,todo,doing,done',
        ]);

        try {
            $task->update([
                'status' => $validated['status']
            ]);

            $statusLabels = [
                'pending' => 'Pending',
                'todo' => 'Todo',
                'doing' => 'Doing',
                'done' => 'Done'
            ];

            return redirect()->route('goals')
                ->with('status', "Task status berhasil diubah ke {$statusLabels[$validated['status']]}!");

        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Terjadi kesalahan saat mengubah status task.']);
        }
    }
}
