<?php

namespace App\Domain\Task\Policy;

use App\Domain\Task\ValueObject\TaskStatus;

interface TaskTransitionPolicyInterface
{
    public function canTransition(string|TaskStatus $from_status, string|TaskStatus $to_status): bool;
}
