<?php

namespace App\Domain\Task;

final class TaskTransition
{
    /**
     * @var array<string, list<string>>
     */
    private const ALLOWED_TRANSITIONS = [
        TaskStatus::NOT_STARTED->value => [
            TaskStatus::NOT_STARTED->value,
            TaskStatus::IN_PROGRESS->value,
            TaskStatus::COMPLETED->value,
        ],
        TaskStatus::IN_PROGRESS->value => [
            TaskStatus::NOT_STARTED->value,
            TaskStatus::IN_PROGRESS->value,
            TaskStatus::COMPLETED->value,
        ],
        TaskStatus::COMPLETED->value => [
            TaskStatus::NOT_STARTED->value,
            TaskStatus::IN_PROGRESS->value,
            TaskStatus::COMPLETED->value,
        ],
    ];

    public function canTransition(string|TaskStatus $from_status, string|TaskStatus $to_status): bool
    {
        $from_status_value = $this->resolveStatus($from_status);
        $to_status_value = $this->resolveStatus($to_status);

        if ($from_status_value === null || $to_status_value === null) {
            return false;
        }

        return in_array($to_status_value->value, self::ALLOWED_TRANSITIONS[$from_status_value->value], true);
    }

    private function resolveStatus(string|TaskStatus $status): ?TaskStatus
    {
        return $status instanceof TaskStatus ? $status : TaskStatus::tryFrom($status);
    }
}
