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
     * ステータスを未着手でタスクを登録する
     *
     * @param string $title
     * @param string|null $detail
     * @param string|null $due_date
     * @return self
     */
    public function createStatusNotStartedTask(
        string $title,
        ?string $detail = null,
        ?string $due_date = null
    ): self {
        return $this->create([
            'title' => $title,
            'detail' => $detail,
            'due_date' => $due_date,
            'status' => self::STATUS_NOT_STARTED,
        ]);
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
     * タスクを期限日順に取得
     *
     * @return Collection
     */
    public function getTaskOrderByDueDate(): Collection
    {
        return $this->query()
            ->select('task_id', 'task_uuid', 'title', 'detail', 'due_date', 'status')
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
        return match ($this->status) {
            self::STATUS_NOT_STARTED => '未着手',
            self::STATUS_IN_PROGRESS => '進行中',
            self::STATUS_COMPLETED   => '完了',
            default => '未設定',
        };
    }
}
