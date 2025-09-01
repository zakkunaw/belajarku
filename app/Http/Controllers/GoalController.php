<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GoalController extends Controller
{
    public function index()
    {
        $goals = Goal::with(['milestones.tasks'])
            ->where('user_id', Auth::id())
            ->orderBy('target_date', 'asc')
            ->get();

        return view('goals', compact('goals'));
    }

    public function show(Goal $goal)
    {
        // Ensure user can only view their own goals
        if ($goal->user_id !== Auth::id()) {
            abort(403);
        }

        $goal->load(['milestones.tasks']);

        return view('goal-details', compact('goal'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'target_date' => 'required|date|after:today',
        ]);

        try {
            Goal::create([
                'user_id' => Auth::id(),
                'title' => $validated['title'],
                'description' => $validated['description'],
                'target_date' => $validated['target_date'],
                'status' => Goal::STATUS_PLANNED,
            ]);

            return redirect()->route('goals')
                ->with('status', 'Goal berhasil dibuat!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Terjadi kesalahan saat membuat goal.'])
                ->withInput();
        }
    }

    public function updateStatus(Request $request, Goal $goal)
    {
        // Ensure user can only update their own goals
        if ($goal->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:planned,in_progress,done',
        ]);

        try {
            $goal->update([
                'status' => $validated['status']
            ]);

            $statusLabels = [
                'planned' => 'Planned',
                'in_progress' => 'In Progress',
                'done' => 'Done'
            ];

            return redirect()->route('goals')
                ->with('status', "Goal status berhasil diubah ke {$statusLabels[$validated['status']]}!");

        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Terjadi kesalahan saat mengubah status goal.']);
        }
    }

    public function destroy(Goal $goal)
    {
        // Ensure user can only delete their own goals
        if ($goal->user_id !== Auth::id()) {
            abort(403);
        }

        try {
            $goalTitle = $goal->title;
            $goal->delete(); // Will cascade delete milestones and tasks

            return redirect()->route('goals')
                ->with('status', "Goal '{$goalTitle}' berhasil dihapus beserta semua milestones dan tasks!");

        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Terjadi kesalahan saat menghapus goal.']);
        }
    }
}
