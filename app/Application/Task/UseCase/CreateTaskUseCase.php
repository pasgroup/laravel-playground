<?php

namespace App\Application\Task\UseCase;

use App\Application\Task\DTO\CreateTaskInput;
use App\Application\Task\DTO\TaskCommandOutput;
use App\Application\Task\Repository\TaskRepositoryInterface;
use App\Domain\Task\Service\TaskCreationService;

final class CreateTaskUseCase
{
    public function __construct(
        private TaskRepositoryInterface $task_repository,
        private TaskCreationService $task_creation_service
    ) {
    }

    public function handle(CreateTaskInput $input): TaskCommandOutput
    {
        $task_entity = $this->task_creation_service->createForNewTask(
            $input->title,
            $input->detail,
            $input->due_date
        );
        $created_task = $this->task_repository->create($task_entity);

        return new TaskCommandOutput('success', 'タスクを登録しました。', $created_task->task_id);
    }
}
