<?php

namespace App\Application\Task\Repository;

use Illuminate\Database\Eloquent\Collection;

interface TaskRepositoryInterface
{
    public function createTask(
        string $title,
        ?string $detail,
        ?string $due_date,
        string $status
    ): int;

    public function getTaskOrderByDueDate(): Collection;

    public function findTaskStatusByUuid(string $task_uuid): ?string;

    public function updateTaskStatusByUuidAndCurrentStatus(
        string $task_uuid,
        string $current_status,
        string $next_status
    ): int;

    public function existsTaskByUuid(string $task_uuid): bool;

    public function deleteTaskByUuid(string $task_uuid): bool;
}
