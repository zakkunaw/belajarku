<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'milestone_id',
        'title',
        'description',
        'due_date',
        'status',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_TODO = 'todo';
    const STATUS_DOING = 'doing';
    const STATUS_DONE = 'done';

    public static function getStatuses(): array
    {
        return [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_TODO => 'Todo',
            self::STATUS_DOING => 'Doing',
            self::STATUS_DONE => 'Done',
        ];
    }

    public function milestone(): BelongsTo
    {
        return $this->belongsTo(Milestone::class);
    }

    public function getStatusBadgeColorAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
            self::STATUS_TODO => 'bg-blue-100 text-blue-600 dark:bg-blue-700 dark:text-blue-400',
            self::STATUS_DOING => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-200',
            self::STATUS_DONE => 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-200',
            default => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400'
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'Pending',
            self::STATUS_TODO => 'Todo',
            self::STATUS_DOING => 'Doing',
            self::STATUS_DONE => 'Done',
            default => 'Unknown'
        };
    }

    public function getCheckboxClassAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'w-4 h-4 border-2 border-gray-300 rounded flex items-center justify-center hover:border-blue-500',
            self::STATUS_TODO => 'w-4 h-4 border-2 border-blue-500 bg-blue-100 rounded flex items-center justify-center hover:border-blue-600',
            self::STATUS_DOING => 'w-4 h-4 border-2 border-yellow-500 bg-yellow-100 rounded flex items-center justify-center hover:border-yellow-600',
            self::STATUS_DONE => 'w-4 h-4 border-2 border-green-500 bg-green-100 rounded flex items-center justify-center hover:border-green-600',
            default => 'w-4 h-4 border-2 border-gray-300 rounded flex items-center justify-center hover:border-blue-500'
        };
    }

    public function getNextStatusAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => self::STATUS_TODO,
            self::STATUS_TODO => self::STATUS_DOING,
            self::STATUS_DOING => self::STATUS_DONE,
            self::STATUS_DONE => self::STATUS_TODO,
            default => self::STATUS_TODO
        };
    }
}
