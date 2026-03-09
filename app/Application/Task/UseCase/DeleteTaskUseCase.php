<?php

namespace App\Application\Task\UseCase;

use App\Application\Task\DTO\DeleteTaskInput;
use App\Application\Task\DTO\TaskCommandOutput;
use App\Application\Task\Exceptions\TaskNotFoundException;
use App\Models\Task;

final class DeleteTaskUseCase
{
    public function __construct(
        private Task $task
    ) {
    }

    /**
     * @throws TaskNotFoundException
     */
    public function handle(DeleteTaskInput $input): TaskCommandOutput
    {
        $deleted = $this->task->deleteByUuid($input->task_uuid);

        if (! $deleted) {
            throw new TaskNotFoundException();
        }

        return new TaskCommandOutput('success', 'タスクを削除しました。');
    }
}
