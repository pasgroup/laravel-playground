<?php

namespace App\Infrastructure\Persistence\Task;

use App\Application\Task\DTO\TaskListItemDto;
use App\Application\Task\Repository\TaskRepositoryInterface;
use App\Domain\Task\TaskStatus;
use App\Models\Task;

final class EloquentTaskRepository implements TaskRepositoryInterface
{
    public function __construct(
        private Task $task
    ) {
    }

    public function createTask(
        string $title,
        ?string $detail,
        ?string $due_date,
        string $status
    ): int {
        $task = $this->task->newQuery()->create([
            'title' => $title,
            'detail' => $detail,
            'due_date' => $due_date,
            'status' => $status,
        ]);

        return (int) $task->task_id;
    }

    /**
     * @return list<TaskListItemDto>
     */
    public function getTaskOrderByDueDate(): array
    {
        $tasks = $this->task->newQuery()
            ->select('task_id', 'task_uuid', 'title', 'detail', 'due_date', 'status')
            ->orderByRaw("(status != '" . TaskStatus::COMPLETED->value . "') DESC")
            ->orderByRaw('due_date IS NULL')
            ->orderBy('due_date', 'asc')
            ->orderBy('task_id', 'asc')
            ->get();

        return $tasks->map(
            fn (Task $task): TaskListItemDto => new TaskListItemDto(
                (int) $task->task_id,
                (string) $task->task_uuid,
                (string) $task->title,
                $task->detail !== null ? (string) $task->detail : null,
                $task->due_date,
                (string) $task->status,
                (string) $task->status_label,
                (bool) $task->is_completed,
                (bool) $task->is_overdue
            )
        )->all();
    }

    public function findTaskStatusByUuid(string $task_uuid): ?string
    {
        $task = $this->task->newQuery()
            ->select('status')
            ->where('task_uuid', $task_uuid)
            ->first();

        if ($task === null) {
            return null;
        }

        return (string) $task->status;
    }

    public function updateTaskStatusByUuidAndCurrentStatus(
        string $task_uuid,
        string $current_status,
        string $next_status
    ): int {
        return $this->task->newQuery()
            ->where('task_uuid', $task_uuid)
            ->where('status', $current_status)
            ->update([
                'status' => $next_status,
            ]);
    }

    public function deleteTaskByUuid(string $task_uuid): bool
    {
        $deleted = $this->task->newQuery()
            ->where('task_uuid', $task_uuid)
            ->delete();

        return $deleted > 0;
    }
}
