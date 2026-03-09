<?php

namespace App\Application\Task\UseCase;

use App\Application\Task\DTO\CreateTaskInput;
use App\Application\Task\DTO\TaskCommandOutput;
use App\Application\Task\Repository\TaskRepositoryInterface;
use App\Domain\Task\TaskStatus;

final class CreateTaskUseCase
{
    public function __construct(
        private TaskRepositoryInterface $task_repository
    ) {
    }

    public function handle(CreateTaskInput $input): TaskCommandOutput
    {
        $task_id = $this->task_repository->createTask(
            $input->title,
            $input->detail,
            $input->due_date,
            TaskStatus::NOT_STARTED->value
        );

        return new TaskCommandOutput('success', 'タスクを登録しました。', $task_id);
    }
}
