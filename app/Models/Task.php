<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Task extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The primary key of the table.
     */
    protected $primaryKey = 'task_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'task_uuid',
        'title',
        'detail',
        'due_date',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'due_date' => 'date',
        ];
    }

    /**
     * タスクのステータス
     */
    public const STATUS_NOT_STARTED = 'not_started';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED   = 'completed';

    /**
     * UUIDを自動生成
     *
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Task $task): void {
            if (empty($task->task_uuid)) {
                $task->task_uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * タスクを期限日順に取得
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTaskOrderByDueDate(): Collection
    {
        return $this->query()
            ->select('task_id', 'title', 'detail', 'due_date', 'status')
            ->orderBy('due_date', 'asc')
            ->orderBy('task_id', 'asc')
            ->get();
    }

    /**
     * ステータスの日本語ラベルを取得
     *
     * @return string
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_NOT_STARTED => '未着手',
            self::STATUS_IN_PROGRESS => '進行中',
            self::STATUS_COMPLETED   => '完了',
            default => '未設定',
        };
    }
}
