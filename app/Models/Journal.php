<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Journal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'study_session_id',
        'content',
        'is_private',
        'date',
    ];

    protected $casts = [
        'date' => 'date',
        'is_private' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function studySession(): BelongsTo
    {
        return $this->belongsTo(StudySession::class);
    }

    // Scopes
    public function scopePublic($query)
    {
        return $query->where('is_private', false);
    }

    public function scopePrivate($query)
    {
        return $query->where('is_private', true);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
