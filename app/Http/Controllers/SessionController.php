<?php

namespace App\Http\Controllers;

use App\Models\StudySession;
use App\Models\Mood;
use App\Models\Journal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SessionController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'source' => 'required|string|max:255',
            'duration_min' => 'required|integer|min:1|max:600',
            'what_learned' => 'required|string|max:2000',
            'difficulty' => 'required|integer|min:1|max:5',
            'topics' => 'required|string|max:500',
            'mood_score' => 'required|integer|min:1|max:5',
            'mood_note' => 'nullable|string|max:255',
            'journal_content' => 'nullable|string|max:2000',
            'journal_is_private' => 'nullable|boolean',
        ]);

        try {
            DB::beginTransaction();

            // Create Study Session
            $studySession = StudySession::create([
                'user_id' => Auth::id(),
                'source' => $validated['source'],
                'duration_min' => $validated['duration_min'],
                'what_learned' => $validated['what_learned'],
                'difficulty' => $validated['difficulty'],
                'topics' => $validated['topics'], // Store as comma-separated string
                'date' => now()->toDateString(),
            ]);

            // Create Mood
            $mood = Mood::create([
                'user_id' => Auth::id(),
                'study_session_id' => $studySession->id,
                'mood_score' => $validated['mood_score'],
                'note' => $validated['mood_note'] ?? null,
                'date' => now()->toDateString(),
            ]);

            // Create Journal if content provided
            if (!empty($validated['journal_content'])) {
                Journal::create([
                    'user_id' => Auth::id(),
                    'study_session_id' => $studySession->id,
                    'content' => $validated['journal_content'],
                    'is_private' => $validated['journal_is_private'] ?? false,
                    'date' => now()->toDateString(),
                ]);
            }

            DB::commit();

            return redirect()->back()->with('status', 'Pembelajaran hari ini berhasil disimpan!');

        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()->back()
                ->withErrors(['error' => 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.'])
                ->withInput();
        }
    }

    public function index()
    {
        $sessions = StudySession::with(['mood', 'journal'])
            ->where('user_id', Auth::id())
            ->orderBy('date', 'desc')
            ->paginate(10);

        return view('sessions.index', compact('sessions'));
    }

    public function show(StudySession $session)
    {
        // Ensure user can only view their own sessions
        if ($session->user_id !== Auth::id()) {
            abort(403);
        }

        $session->load(['mood', 'journal']);

        return view('sessions.show', compact('session'));
    }
}
