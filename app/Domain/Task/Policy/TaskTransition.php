<?php

namespace App\Domain\Task\Policy;

use App\Domain\Task\ValueObject\TaskStatus;

final class TaskTransition implements TaskTransitionPolicyInterface
{
    /**
     * @var array<string, list<string>>
     */
    private static array $allowed_transitions = [];

    public function canTransition(string|TaskStatus $from_status, string|TaskStatus $to_status): bool
    {
        self::initializeAllowedTransitions();
        $from_status_value = $this->resolveStatus($from_status);
        $to_status_value = $this->resolveStatus($to_status);

        if ($from_status_value === null || $to_status_value === null) {
            return false;
        }

        return in_array($to_status_value->value, self::$allowed_transitions[$from_status_value->value], true);
    }

    private function resolveStatus(string|TaskStatus $status): ?TaskStatus
    {
        return $status instanceof TaskStatus ? $status : TaskStatus::tryFrom($status);
    }

    private static function initializeAllowedTransitions(): void
    {
        if (self::$allowed_transitions !== []) {
            return;
        }

        self::$allowed_transitions = [
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
    }
}
