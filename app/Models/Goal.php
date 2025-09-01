<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Goal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'target_date',
        'status',
        'progress_percentage',
    ];

    protected $casts = [
        'target_date' => 'date',
    ];

    const STATUS_ACTIVE = 'active';
    const STATUS_COMPLETED = 'completed';
    const STATUS_PAUSED = 'paused';
    const STATUS_PLANNED = 'planned';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_DONE = 'done';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_PAUSED => 'Paused',
            self::STATUS_PLANNED => 'Planned',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_DONE => 'Done',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(Milestone::class)->orderBy('order_index');
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_ACTIVE => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
            self::STATUS_COMPLETED => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
            self::STATUS_PAUSED => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
            self::STATUS_PLANNED => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200',
            self::STATUS_IN_PROGRESS => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
            self::STATUS_DONE => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200'
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_PAUSED => 'Paused',
            self::STATUS_PLANNED => 'Planned',
            self::STATUS_IN_PROGRESS => 'In Progress',
            self::STATUS_DONE => 'Done',
            default => 'Unknown'
        };
    }

    public function getProgressPercentageAttribute(): float
    {
        $totalTasks = $this->milestones->sum(function ($milestone) {
            return $milestone->tasks->count();
        });

        if ($totalTasks === 0) {
            return 0;
        }

        $completedTasks = $this->milestones->sum(function ($milestone) {
            return $milestone->tasks->where('status', Task::STATUS_DONE)->count();
        });

        return round(($completedTasks / $totalTasks) * 100, 1);
    }
}
