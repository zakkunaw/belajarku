<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudySession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'source',
        'duration_min',
        'what_learned',
        'difficulty',
        'topics',
        'date',
    ];

    protected $casts = [
        'date' => 'date',
        'difficulty' => 'integer',
        'duration_min' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function mood(): BelongsTo
    {
        return $this->belongsTo(Mood::class);
    }

    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class);
    }

    // Helper methods
    public function getDifficultyLabelAttribute(): string
    {
        return match($this->difficulty) {
            1 => 'Sangat Mudah',
            2 => 'Mudah',
            3 => 'Sedang',
            4 => 'Sulit',
            5 => 'Sangat Sulit',
            default => 'Tidak Diketahui'
        };
    }

    public function getTopicsArrayAttribute(): array
    {
        if (is_string($this->topics)) {
            return array_map('trim', explode(',', $this->topics));
        }
        return is_array($this->topics) ? $this->topics : [];
    }
}
