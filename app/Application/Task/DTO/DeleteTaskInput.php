<?php

namespace App\Application\Task\DTO;

final class DeleteTaskInput
{
    public function __construct(
        public string $task_uuid
    ) {
    }
}
