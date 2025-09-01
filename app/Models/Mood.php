<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mood extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'study_session_id',
        'mood_score',
        'note',
        'date',
    ];

    protected $casts = [
        'date' => 'date',
        'mood_score' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function studySession(): BelongsTo
    {
        return $this->belongsTo(StudySession::class);
    }

    // Helper methods
    public function getMoodLabelAttribute(): string
    {
        return match($this->mood_score) {
            1 => 'Sangat Buruk',
            2 => 'Buruk',
            3 => 'Biasa',
            4 => 'Bagus',
            5 => 'Sangat Bagus',
            default => 'Tidak Diketahui'
        };
    }

    public function getMoodEmojiAttribute(): string
    {
        return match($this->mood_score) {
            1 => '😞',
            2 => '😟',
            3 => '😐',
            4 => '😊',
            5 => '😄',
            default => '😐'
        };
    }
}
