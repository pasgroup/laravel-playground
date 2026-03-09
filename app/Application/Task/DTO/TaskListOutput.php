<?php

namespace App\Application\Task\DTO;

final class TaskListOutput
{
    /**
     * @param list<TaskListItemDto> $tasks
     */
    public function __construct(
        public array $tasks
    ) {
    }
}
