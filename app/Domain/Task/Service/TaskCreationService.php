<?php

namespace App\Domain\Task\Service;

use App\Domain\Task\Entity\TaskEntity;
use App\Domain\Task\ValueObject\TaskStatus;
use Carbon\CarbonImmutable;

final class TaskCreationService
{
    public function createForNewTask(string $title, ?string $detail, ?string $due_date): TaskEntity
    {
        return new TaskEntity(
            task_id: null,
            task_uuid: null,
            title: $title,
            detail: $detail,
            due_date: $due_date !== null ? CarbonImmutable::parse($due_date) : null,
            status: TaskStatus::NOT_STARTED->value
        );
    }
}
