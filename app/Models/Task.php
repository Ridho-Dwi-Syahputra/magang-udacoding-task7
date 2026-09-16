<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Task extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    public const STATUS = ['todo', 'progress', 'done'];

    protected $fillable = [
        'title',
        'description',
        'status',
        'due_date',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Task $task) {
            if (empty($task->id)) {
                $task->id = Str::random(16);
            }
        });
    }
}
