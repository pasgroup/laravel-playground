<?php

namespace App\Infrastructure\Persistence\Task;

use App\Application\Task\Repository\TaskRepositoryInterface;
use App\Domain\Task\Entity\TaskEntity;
use App\Domain\Task\ValueObject\TaskStatus;
use App\Models\Task;
use Carbon\CarbonInterface;

final class EloquentTaskRepository implements TaskRepositoryInterface
{
    public function __construct(
        private Task $task
    ) {
    }

    public function create(TaskEntity $task_entity): TaskEntity
    {
        $created_task = $this->task->newQuery()->create([
            'title' => $task_entity->title,
            'detail' => $task_entity->detail,
            'due_date' => $task_entity->due_date?->toDateString(),
            'status' => $task_entity->status,
        ]);

        return $this->toEntity($created_task);
    }

    /**
     * @return list<TaskEntity>
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

        return $tasks
            ->map(fn (Task $task): TaskEntity => $this->toEntity($task))
            ->all();
    }

    public function findByUuid(string $task_uuid): ?TaskEntity
    {
        $task = $this->task->newQuery()
            ->select('task_id', 'task_uuid', 'title', 'detail', 'due_date', 'status')
            ->where('task_uuid', $task_uuid)
            ->first();

        if ($task === null) {
            return null;
        }

        return $this->toEntity($task);
    }

    public function updateTaskStatusByUuidAndCurrentStatus(
        string $task_uuid,
        TaskStatus $current_status,
        TaskStatus $next_status
    ): int {
        return $this->task->newQuery()
            ->where('task_uuid', $task_uuid)
            ->where('status', $current_status->value)
            ->update([
                'status' => $next_status->value,
            ]);
    }

    public function deleteTaskByUuid(string $task_uuid): bool
    {
        $deleted = $this->task->newQuery()
            ->where('task_uuid', $task_uuid)
            ->delete();

        return $deleted > 0;
    }

    private function toEntity(Task $task): TaskEntity
    {
        return new TaskEntity(
            task_id: (int) $task->task_id,
            task_uuid: (string) $task->task_uuid,
            title: (string) $task->title,
            detail: $task->detail !== null ? (string) $task->detail : null,
            due_date: $this->toCarbonOrNull($task->due_date),
            status: (string) $task->status
        );
    }

    private function toCarbonOrNull(mixed $value): ?CarbonInterface
    {
        if ($value instanceof CarbonInterface) {
            return $value;
        }

        return null;
    }
}
