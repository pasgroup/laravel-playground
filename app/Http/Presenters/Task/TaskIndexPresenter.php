<?php

namespace App\Http\Presenters\Task;

use App\Application\Task\DTO\TaskListOutput;
use App\Domain\Task\Entity\TaskEntity;
use App\Domain\Task\ValueObject\TaskStatus;
use App\Http\Presenters\Task\Formatter\TaskIndexItemFormatter;
use App\Http\Presenters\Task\ViewModel\TaskIndexItemViewModel;
use App\Http\Presenters\Task\ViewModel\TaskIndexViewModel;
use App\Http\Presenters\Task\ViewModel\TaskStatusOptionViewModel;

final class TaskIndexPresenter
{
    private TaskIndexItemFormatter $task_index_item_formatter;

    public function __construct(?TaskIndexItemFormatter $task_index_item_formatter = null)
    {
        $this->task_index_item_formatter = $task_index_item_formatter ?? new TaskIndexItemFormatter();
    }

    public function present(
        TaskListOutput $output,
        ?string $success_message,
        ?string $error_message
    ): TaskIndexViewModel {
        return new TaskIndexViewModel(
            $this->presentTaskItems($output->tasks),
            $this->presentStatusOptions(),
            $success_message,
            $error_message
        );
    }

    /**
     * @param list<TaskEntity> $tasks
     * @return list<TaskIndexItemViewModel>
     */
    private function presentTaskItems(array $tasks): array
    {
        $presented_tasks = [];
        $previous_task = null;

        foreach ($tasks as $task) {
            $presented_tasks[] = $this->task_index_item_formatter->toViewModel(
                $task,
                $previous_task
            );

            $previous_task = $task;
        }

        return $presented_tasks;
    }

    /**
     * @return list<TaskStatusOptionViewModel>
     */
    private function presentStatusOptions(): array
    {
        return array_map(
            fn (TaskStatus $task_status): TaskStatusOptionViewModel => new TaskStatusOptionViewModel(
                $task_status->value,
                $task_status->label()
            ),
            TaskStatus::cases()
        );
    }
}
