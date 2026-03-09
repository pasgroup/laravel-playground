<?php

namespace App\Models;

use App\Domain\Task\Specification\TaskDeadline;
use App\Domain\Task\ValueObject\TaskStatus;
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
     * ステータスの日本語ラベルを取得
     *
     * @return string
     */
    public function getStatusLabelAttribute(): string
    {
        $task_status = TaskStatus::tryFrom((string) $this->status);

        if ($task_status === null) {
            return '未設定';
        }

        return $task_status->label();
    }

    /**
     * 完了済みかどうか
     *
     * @return bool
     */
    public function getIsCompletedAttribute(): bool
    {
        $task_status = TaskStatus::tryFrom((string) $this->status);

        return $task_status?->isCompleted() ?? false;
    }

    /**
     * 期限超過かどうか（未完了かつ期限日が過去）
     *
     * @return bool
     */
    public function getIsOverdueAttribute(): bool
    {
        $task_deadline = new TaskDeadline();

        return $task_deadline->isOverdue((string) $this->status, $this->due_date);
    }
}
