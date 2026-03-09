<?php

namespace App\Application\Task\UseCase;

use App\Application\Task\DTO\DeleteTaskInput;
use App\Application\Task\DTO\TaskCommandOutput;
use App\Application\Task\Exceptions\TaskNotFoundException;
use App\Application\Task\Repository\TaskRepositoryInterface;

final class DeleteTaskUseCase
{
    public function __construct(
        private TaskRepositoryInterface $task_repository
    ) {
    }

    /**
     * @throws TaskNotFoundException
     */
    public function handle(DeleteTaskInput $input): TaskCommandOutput
    {
        $deleted = $this->task_repository->deleteTaskByUuid($input->task_uuid);

        if (! $deleted) {
            throw new TaskNotFoundException();
        }

        return new TaskCommandOutput('success', 'タスクを削除しました。');
    }
}
