<?php

namespace App\Domain\Task\Service;

use App\Domain\Task\Entity\TaskEntity;
use App\Domain\Task\ValueObject\TaskStatus;
use Carbon\CarbonImmutable;
use InvalidArgumentException;

final class TaskCreationService
{
    public function createForNewTask(string $title, ?string $detail, ?string $due_date): TaskEntity
    {
        return new TaskEntity(
            task_id: null,
            task_uuid: null,
            title: $title,
            detail: $detail,
            due_date: $due_date !== null ? $this->parseDueDate($due_date) : null,
            status: TaskStatus::NOT_STARTED->value
        );
    }

    private function parseDueDate(string $due_date): CarbonImmutable
    {
        $parsed = CarbonImmutable::createFromFormat('Y-m-d', $due_date);
        if ($parsed === false || $parsed->format('Y-m-d') !== $due_date) {
            throw new InvalidArgumentException('Invalid due_date format: ' . $due_date);
        }

        return $parsed;
    }
}
