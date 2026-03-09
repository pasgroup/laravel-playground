<?php

namespace App\Application\Task\UseCase;

use App\Application\Task\DTO\CreateTaskInput;
use App\Application\Task\DTO\TaskCommandOutput;
use App\Domain\Task\TaskStatus;
use App\Models\Task;

final class CreateTaskUseCase
{
    public function __construct(
        private Task $task
    ) {
    }

    public function handle(CreateTaskInput $input): TaskCommandOutput
    {
        $task = $this->task->newQuery()->create([
            'title' => $input->title,
            'detail' => $input->detail,
            'due_date' => $input->due_date,
            'status' => TaskStatus::NOT_STARTED->value,
        ]);

        return new TaskCommandOutput('success', 'タスクを登録しました。', $task->task_id);
    }
}
