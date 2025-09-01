<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use App\Models\Milestone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MilestoneController extends Controller
{
    public function store(Request $request, Goal $goal)
    {
        // Ensure user can only add milestones to their own goals
        if ($goal->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'target_date' => 'required|date',
            'description' => 'nullable|string|max:1000',
        ]);

        try {
            // Get the next order number
            $maxOrder = $goal->milestones()->max('order_index') ?? 0;

            Milestone::create([
                'goal_id' => $goal->id,
                'title' => $validated['title'],
                'description' => $validated['description'],
                'target_date' => $validated['target_date'],
                'status' => 'pending',
                'order_index' => $maxOrder + 1,
            ]);

            return redirect()->route('goals')
                ->with('status', 'Milestone berhasil ditambahkan! 📋');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Terjadi kesalahan saat menambahkan milestone.'])
                ->withInput();
        }
    }

    public function move(Request $request, Milestone $milestone)
    {
        // Ensure user can only move milestones of their own goals
        if ($milestone->goal->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'direction' => 'required|in:up,down',
        ]);

        try {
            DB::beginTransaction();

            if ($validated['direction'] === 'up') {
                $milestone->moveUp();
            } else {
                $milestone->moveDown();
            }

            DB::commit();

            return redirect()->route('goals')
                ->with('status', 'Urutan milestone berhasil diubah!');

        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()->back()
                ->withErrors(['error' => 'Terjadi kesalahan saat mengubah urutan milestone.']);
        }
    }
}
