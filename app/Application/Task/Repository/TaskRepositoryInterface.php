<?php

namespace App\Application\Task\Repository;

interface TaskRepositoryInterface
{
    public function createTask(
        string $title,
        ?string $detail,
        ?string $due_date,
        string $status
    ): int;

    public function getTaskOrderByDueDate(): iterable;

    public function findTaskStatusByUuid(string $task_uuid): ?string;

    public function updateTaskStatusByUuidAndCurrentStatus(
        string $task_uuid,
        string $current_status,
        string $next_status
    ): int;

    public function deleteTaskByUuid(string $task_uuid): bool;
}
