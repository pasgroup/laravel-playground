<?php

namespace App\Domain\Task\Service;

use App\Domain\Task\Policy\TaskTransitionPolicyInterface;
use App\Domain\Task\ValueObject\TaskStatus;

final class TaskStatusDecisionService
{
    public function __construct(
        private TaskTransitionPolicyInterface $task_transition
    ) {
    }

    /**
     * @return array{TaskStatus, TaskStatus}|null
     */
    public function resolveTransition(string $current_status_value, string $next_status_value): ?array
    {
        $current_status = TaskStatus::tryFrom($current_status_value);
        $next_status = TaskStatus::tryFrom($next_status_value);

        if ($current_status === null || $next_status === null) {
            return null;
        }

        if (! $this->task_transition->canTransition($current_status, $next_status)) {
            return null;
        }

        return [$current_status, $next_status];
    }

    public function resolveStatus(string $status_value): ?TaskStatus
    {
        return TaskStatus::tryFrom($status_value);
    }
}
