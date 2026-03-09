<?php

namespace App\Application\Task\UseCase;

use App\Application\Task\DTO\TaskCommandOutput;
use App\Application\Task\DTO\UpdateTaskStatusInput;
use App\Application\Task\Exceptions\InvalidTaskStatusTransitionException;
use App\Application\Task\Exceptions\TaskNotFoundException;
use App\Application\Task\Repository\TaskRepositoryInterface;
use App\Domain\Task\TaskStatus;
use App\Domain\Task\TaskTransition;

final class UpdateTaskStatusUseCase
{
    public function __construct(
        private TaskRepositoryInterface $task_repository,
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
        $current_status_value = $this->task_repository->findTaskStatusByUuid($input->task_uuid);
        if ($current_status_value === null) {
            throw new TaskNotFoundException();
        }

        $current_status = TaskStatus::tryFrom($current_status_value);

        if ($current_status === null || ! $this->task_transition->canTransition($current_status, $next_status)) {
            throw new InvalidTaskStatusTransitionException();
        }

        if ($current_status === $next_status) {
            return new TaskCommandOutput('success', 'タスクのステータスを更新しました。');
        }

        $updated = $this->task_repository->updateTaskStatusByUuidAndCurrentStatus(
            $input->task_uuid,
            $current_status->value,
            $next_status->value
        );

        if ($updated === 0) {
            $latest_status_value = $this->task_repository->findTaskStatusByUuid($input->task_uuid);

            if ($latest_status_value === null) {
                throw new TaskNotFoundException();
            }

            $latest_status = TaskStatus::tryFrom($latest_status_value);

            if ($latest_status === $next_status) {
                return new TaskCommandOutput('success', 'タスクのステータスを更新しました。');
            }

            throw new InvalidTaskStatusTransitionException();
        }

        if ($updated !== 1) {
            throw new TaskNotFoundException();
        }

        return new TaskCommandOutput('success', 'タスクのステータスを更新しました。');
    }
}
