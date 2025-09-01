<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the study sessions for the user.
     */
    public function studySessions(): HasMany
    {
        return $this->hasMany(StudySession::class);
    }

    /**
     * Get the moods for the user.
     */
    public function moods(): HasMany
    {
        return $this->hasMany(Mood::class);
    }

    /**
     * Get the journals for the user.
     */
    public function journals(): HasMany
    {
        return $this->hasMany(Journal::class);
    }

    /**
     * Get the goals for the user.
     */
    public function goals(): HasMany
    {
        return $this->hasMany(Goal::class);
    }

    /**
     * Get recent study sessions.
     */
    public function recentStudySessions($limit = 5)
    {
        return $this->studySessions()
            ->with(['mood', 'journal'])
            ->orderBy('date', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get today's study session.
     */
    public function todayStudySession()
    {
        return $this->studySessions()
            ->with(['mood', 'journal'])
            ->whereDate('date', today())
            ->first();
    }
}
