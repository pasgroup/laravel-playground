<?php

namespace App\Application\Task\DTO;

use App\Domain\Task\Entity\TaskEntity;

final class TaskListOutput
{
    /**
     * @param list<TaskEntity> $tasks
     */
    public function __construct(
        public array $tasks
    ) {
    }
}
