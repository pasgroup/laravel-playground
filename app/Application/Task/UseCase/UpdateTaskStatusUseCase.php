<?php

namespace App\Application\Task\UseCase;

use App\Application\Task\DTO\TaskCommandOutput;
use App\Application\Task\DTO\UpdateTaskStatusInput;
use App\Application\Task\Exceptions\InvalidTaskStatusTransitionException;
use App\Application\Task\Exceptions\TaskNotFoundException;
use App\Domain\Task\TaskStatus;
use App\Domain\Task\TaskTransition;
use App\Models\Task;

final class UpdateTaskStatusUseCase
{
    public function __construct(
        private Task $task,
        private TaskTransition $task_transition
    ) {
    }

    /**
     * @throws TaskNotFoundException
     * @throws InvalidTaskStatusTransitionException
     */
    public function handle(UpdateTaskStatusInput $input): TaskCommandOutput
    {
        $next_status = TaskStatus::from($input->status);
        $current_task = $this->task->newQuery()
            ->select('task_id', 'status')
            ->where('task_uuid', $input->task_uuid)
            ->first();

        if ($current_task === null) {
            throw new TaskNotFoundException();
        }

        $current_status = TaskStatus::tryFrom((string) $current_task->status);

        if ($current_status === null || ! $this->task_transition->canTransition($current_status, $next_status)) {
            throw new InvalidTaskStatusTransitionException();
        }

        $updated = $this->task->updateStatusByUuid(
            $input->task_uuid,
            $next_status->value
        );

        if (! $updated) {
            throw new TaskNotFoundException();
        }

        return new TaskCommandOutput('success', 'タスクのステータスを更新しました。');
    }
}
