<?php

namespace App\Domain\Task\Specification;

use App\Domain\Task\ValueObject\TaskStatus;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;

final class TaskDeadline
{
    /**
     * @param TaskStatus|string $status
     * @param CarbonInterface|string|null $due_date
     */
    public function isOverdue(TaskStatus|string $status, CarbonInterface|string|null $due_date): bool
    {
        $status_value = $status instanceof TaskStatus ? $status : TaskStatus::tryFrom($status);

        if ($status_value === null || $status_value->isCompleted() || $due_date === null) {
            return false;
        }

        $due_date_value = $due_date instanceof CarbonInterface
            ? CarbonImmutable::instance($due_date)
            : CarbonImmutable::parse($due_date);

        return $due_date_value->startOfDay()->lt(today()->startOfDay());
    }
}
