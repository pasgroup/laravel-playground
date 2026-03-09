<?php

namespace App\Models;

use App\Domain\Task\TaskDeadline;
use App\Domain\Task\TaskStatus;
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
     * UUIDで該当タスクのステータスを更新する
     *
     * @param string $task_uuid
     * @param string $status
     * @return bool
     */
    public function updateStatusByUuid(string $task_uuid, string $status): bool
    {
        $updated = $this->where('task_uuid', $task_uuid)->update(['status' => $status]);

        if ($updated > 0) {
            return true;
        }

        return $this->withoutTrashed()->where('task_uuid', $task_uuid)->exists();
    }

    /**
     * UUIDで該当タスクを削除する
     *
     * @param string $task_uuid
     * @return bool
     */
    public function deleteByUuid(string $task_uuid): bool
    {
        $deleted = $this->where('task_uuid', $task_uuid)->delete();

        return $deleted > 0;
    }

    /**
     * タスクを期限日順に取得（完了は一覧の下に表示）
     *
     * @return Collection
     */
    public function getTaskOrderByDueDate(): Collection
    {
        return $this->query()
            ->select('task_id', 'task_uuid', 'title', 'detail', 'due_date', 'status')
            ->orderByRaw("(status != '" . TaskStatus::COMPLETED->value . "') DESC")
            ->orderByRaw('due_date IS NULL')
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
