<?php

namespace App\Application\Task\UseCase;

use App\Application\Task\DTO\TaskListOutput;
use App\Application\Task\Repository\TaskRepositoryInterface;

final class ListTasksUseCase
{
    public function __construct(
        private TaskRepositoryInterface $task_repository
    ) {
    }

    public function handle(): TaskListOutput
    {
        return new TaskListOutput(
            $this->task_repository->getTaskOrderByDueDate()
        );
    }
}
