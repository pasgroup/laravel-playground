<?php

namespace App\Application\Task\DTO;

final class TaskListOutput
{
    public function __construct(
        public iterable $tasks
    ) {
    }
}
