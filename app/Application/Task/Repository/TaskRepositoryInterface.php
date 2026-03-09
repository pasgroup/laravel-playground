<?php

namespace App\Application\Task\Repository;

use App\Domain\Task\Entity\TaskEntity;
use App\Domain\Task\ValueObject\TaskStatus;

interface TaskRepositoryInterface
{
    public function create(TaskEntity $task_entity): TaskEntity;

    /**
     * @return list<TaskEntity>
     */
    public function getTaskOrderByDueDate(): array;

    public function findByUuid(string $task_uuid): ?TaskEntity;

    public function updateTaskStatusByUuidAndCurrentStatus(
        string $task_uuid,
        TaskStatus $current_status,
        TaskStatus $next_status
    ): int;

    public function deleteTaskByUuid(string $task_uuid): bool;
}
