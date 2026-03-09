<?php

namespace App\Application\Task\DTO;

final class UpdateTaskStatusInput
{
    public function __construct(
        public string $task_uuid,
        public string $status
    ) {
    }
}
