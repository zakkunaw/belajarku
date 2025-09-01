<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Milestone extends Model
{
    use HasFactory;

    protected $fillable = [
        'goal_id',
        'title',
        'description',
        'target_date',
        'status',
        'order_index',
    ];

    protected $casts = [
        'target_date' => 'date',
        'order_index' => 'integer',
    ];

    public function goal(): BelongsTo
    {
        return $this->belongsTo(Goal::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class)->orderBy('created_at');
    }

    public function moveUp(): void
    {
        $previousMilestone = $this->goal->milestones()
            ->where('order_index', '<', $this->order_index)
            ->orderBy('order_index', 'desc')
            ->first();

        if ($previousMilestone) {
            $tempOrder = $this->order_index;
            $this->order_index = $previousMilestone->order_index;
            $previousMilestone->order_index = $tempOrder;
            
            $this->save();
            $previousMilestone->save();
        }
    }

    public function moveDown(): void
    {
        $nextMilestone = $this->goal->milestones()
            ->where('order_index', '>', $this->order_index)
            ->orderBy('order_index', 'asc')
            ->first();

        if ($nextMilestone) {
            $tempOrder = $this->order_index;
            $this->order_index = $nextMilestone->order_index;
            $nextMilestone->order_index = $tempOrder;
            
            $this->save();
            $nextMilestone->save();
        }
    }

    public function getTasksProgressAttribute(): array
    {
        $tasks = $this->tasks;
        $total = $tasks->count();
        
        if ($total === 0) {
            return [
                'total' => 0,
                'completed' => 0,
                'percentage' => 0
            ];
        }

        $completed = $tasks->where('status', Task::STATUS_DONE)->count();
        
        return [
            'total' => $total,
            'completed' => $completed,
            'percentage' => round(($completed / $total) * 100, 1)
        ];
    }
}
