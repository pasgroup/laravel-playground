<?php

namespace App\Domain\Task\Policy;

use App\Domain\Task\ValueObject\TaskStatus;

interface TaskTransitionPolicyInterface
{
    public function canTransition(TaskStatus $from_status, TaskStatus $to_status): bool;
}
