<?php

namespace App\Application\Task\UseCase;

use App\Application\Task\DTO\TaskCommandOutput;
use App\Application\Task\DTO\UpdateTaskStatusInput;
use App\Application\Task\Exceptions\InvalidTaskStatusTransitionException;
use App\Application\Task\Exceptions\TaskNotFoundException;
use App\Application\Task\Repository\TaskRepositoryInterface;
use App\Domain\Task\Service\TaskStatusDecisionService;

final class UpdateTaskStatusUseCase
{
    public function __construct(
        private TaskRepositoryInterface $task_repository,
        private TaskStatusDecisionService $task_status_decision_service
    ) {
    }

    /**
     * @throws TaskNotFoundException
     * @throws InvalidTaskStatusTransitionException
     */
    public function handle(UpdateTaskStatusInput $input): TaskCommandOutput
    {
        $current_task = $this->task_repository->findByUuid($input->task_uuid);
        if ($current_task === null) {
            throw new TaskNotFoundException();
        }
        $resolved_transition = $this->task_status_decision_service->resolveTransition(
            $current_task->status,
            $input->status
        );
        if ($resolved_transition === null) {
            throw new InvalidTaskStatusTransitionException();
        }
        [$current_status, $next_status] = $resolved_transition;

        if ($current_status === $next_status) {
            return new TaskCommandOutput('success', 'タスクのステータスを更新しました。');
        }

        $updated = $this->task_repository->updateTaskStatusByUuidAndCurrentStatus(
            $input->task_uuid,
            $current_status,
            $next_status
        );

        if ($updated === 0) {
            $latest_task = $this->task_repository->findByUuid($input->task_uuid);
            if ($latest_task === null) {
                throw new TaskNotFoundException();
            }
            $latest_status = $this->task_status_decision_service->resolveStatus($latest_task->status);

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
