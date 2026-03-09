<?php

namespace App\Http\Presenters\Task\Formatter;

use App\Domain\Task\Entity\TaskEntity;
use App\Domain\Task\Specification\TaskDeadline;
use App\Http\Presenters\Task\ViewModel\TaskIndexItemViewModel;
use Carbon\CarbonInterface;
use LogicException;

final class TaskIndexItemFormatter
{
    public function __construct(
        private TaskDeadline $task_deadline = new TaskDeadline()
    ) {
    }

    public function toViewModel(TaskEntity $task, ?TaskEntity $previous_task): TaskIndexItemViewModel
    {
        if ($task->task_id === null) {
            throw new LogicException('TaskEntity.task_id must not be null when rendering index view.');
        }

        $is_first_completed = $previous_task !== null
            && $task->isCompleted()
            && ! $previous_task->isCompleted();

        return new TaskIndexItemViewModel(
            $task->task_id,
            $task->task_uuid,
            $task->title,
            $task->detail,
            $this->formatDueDate($task->due_date),
            $task->status,
            $task->isOverdue($this->task_deadline),
            $is_first_completed
        );
    }

    private function formatDueDate(?CarbonInterface $due_date): string
    {
        if ($due_date === null) {
            return '—';
        }

        return $due_date->format('Y-m-d');
    }
}
