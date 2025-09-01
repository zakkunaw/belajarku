<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AiController extends Controller
{
    public function review(Request $request)
    {
        $validated = $request->validate([
            'source' => 'required|string|max:255',
            'duration_min' => 'required|integer|min:1|max:600',
            'what_learned' => 'required|string|max:2000',
            'difficulty' => 'required|integer|min:1|max:5',
            'topics' => 'required|string|max:500',
            'mood_score' => 'required|integer|min:1|max:5',
            'mood_note' => 'nullable|string|max:255',
        ]);

        try {
            // Mock AI Analysis - In production, this would call actual AI service
            $analysis = $this->generateMockAnalysis($validated);
            
            return response()->json([
                'success' => true,
                'data' => $analysis,
                'message' => 'Analisis AI berhasil dibuat'
            ]);

        } catch (\Exception $e) {
            Log::error('AI Review Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menganalisis pembelajaran',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    private function generateMockAnalysis(array $data): array
    {
        $topics = array_map('trim', explode(',', $data['topics']));
        $difficulty = $data['difficulty'];
        $duration = $data['duration_min'];
        $source = $data['source'];

        // Generate summary based on topics and difficulty
        $summary = [
            "Kamu telah mempelajari {$data['topics']} selama {$duration} menit",
            "Tingkat kesulitan yang dirasakan adalah {$this->getDifficultyText($difficulty)} ({$difficulty}/5)",
            "Sumber pembelajaran: {$source}",
            "Durasi belajar yang cukup " . ($duration >= 30 ? "baik" : "perlu ditingkatkan"),
            "Mood pembelajaran: " . $this->getMoodText($data['mood_score']),
        ];

        // Add contextual summary based on difficulty
        if ($difficulty >= 4) {
            $summary[] = "Materi yang challenging menunjukkan growth mindset yang baik";
            $summary[] = "Disarankan untuk review ulang konsep yang sulit";
        } else {
            $summary[] = "Pemahaman materi terlihat solid dan konsisten";
        }

        // Generate misconceptions based on difficulty and topics
        $misconceptions = [];
        if ($difficulty >= 3) {
            $misconceptions = [
                "Pastikan memahami konsep fundamental sebelum lanjut ke topik lanjutan",
                "Jangan terburu-buru menghafal tanpa memahami logika di baliknya",
            ];
            
            if ($difficulty >= 4) {
                $misconceptions[] = "Hindari overthinking - kadang solusi lebih sederhana dari yang dibayangkan";
            }
        }

        // Generate recommendations based on topics and performance
        $recommendations = [
            "Coba praktikkan langsung apa yang sudah dipelajari dengan mini project",
            "Buat mind map atau catatan ringkasan untuk memperkuat retensi",
            "Diskusikan topik ini dengan teman atau komunitas untuk perspektif berbeda",
        ];

        // Generate level assessment
        $levelAssessment = $this->generateLevelAssessment($difficulty, $duration, count($topics));

        return [
            'summary' => $summary,
            'misconceptions' => $misconceptions,
            'recommendations' => $recommendations,
            'level_assessment' => $levelAssessment,
        ];
    }

    private function getDifficultyText(int $difficulty): string
    {
        return match($difficulty) {
            1 => 'Sangat Mudah',
            2 => 'Mudah',
            3 => 'Sedang',
            4 => 'Sulit',
            5 => 'Sangat Sulit',
            default => 'Tidak Diketahui'
        };
    }

    private function getMoodText(int $mood): string
    {
        return match($mood) {
            1 => 'Kurang baik - mungkin perlu istirahat',
            2 => 'Cukup - bisa ditingkatkan lagi',
            3 => 'Normal - stabil dalam belajar',
            4 => 'Baik - mood positif untuk belajar',
            5 => 'Sangat baik - kondisi optimal untuk belajar',
            default => 'Tidak Diketahui'
        };
    }

    private function generateLevelAssessment(int $difficulty, int $duration, int $topicCount): string
    {
        $score = 0;
        
        // Score based on difficulty handled
        $score += $difficulty * 2;
        
        // Score based on duration
        if ($duration >= 60) $score += 3;
        elseif ($duration >= 30) $score += 2;
        else $score += 1;
        
        // Score based on topic variety
        $score += min($topicCount, 3);

        if ($score >= 12) {
            return "🎯 Level: Advanced Learner. Langkah selanjutnya: Fokus pada penerapan praktis dan berbagi pengetahuan dengan mengajar orang lain.";
        } elseif ($score >= 8) {
            return "📈 Level: Intermediate Learner. Langkah selanjutnya: Tingkatkan durasi belajar dan coba materi yang lebih menantang.";
        } else {
            return "🌱 Level: Beginner Learner. Langkah selanjutnya: Konsisten belajar setiap hari dan fokus pada satu topik sampai paham betul.";
        }
    }
}
