<?php

namespace App\Domain\Task\Policy;

use App\Domain\Task\ValueObject\TaskStatus;

final class TaskTransition implements TaskTransitionPolicyInterface
{
    /**
     * @var array<string, list<string>>
     */
    private static array $allowed_transitions = [];

    public function canTransition(TaskStatus $from_status, TaskStatus $to_status): bool
    {
        self::initializeAllowedTransitions();
        return in_array(
            $to_status->value,
            self::$allowed_transitions[$from_status->value] ?? [],
            true
        );
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
