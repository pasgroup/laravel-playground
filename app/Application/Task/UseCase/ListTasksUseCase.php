<?php

namespace App\Application\Task\UseCase;

use App\Application\Task\DTO\TaskListOutput;
use App\Models\Task;

final class ListTasksUseCase
{
    public function __construct(
        private Task $task
    ) {
    }

    public function handle(): TaskListOutput
    {
        return new TaskListOutput(
            $this->task->getTaskOrderByDueDate()
        );
    }
}
