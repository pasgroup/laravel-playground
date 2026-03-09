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
        $next_status = TaskStatus::tryFrom($input->status);
        if ($next_status === null) {
            throw new InvalidTaskStatusTransitionException();
        }
        $task_query = $this->task->newQuery();
        $current_task = $task_query
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

        $updated = $this->task->newQuery()
            ->where('task_uuid', $input->task_uuid)
            ->where('status', $current_status->value)
            ->update([
                'status' => $next_status->value,
            ]);

        if ($updated === 0) {
            $task_exists = $this->task->newQuery()
                ->where('task_uuid', $input->task_uuid)
                ->exists();

            if (! $task_exists) {
                throw new TaskNotFoundException();
            }

            throw new InvalidTaskStatusTransitionException();
        }

        if ($updated !== 1) {
            throw new TaskNotFoundException();
        }

        return new TaskCommandOutput('success', 'タスクのステータスを更新しました。');
    }
}
