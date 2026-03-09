<?php

namespace App\Http\Presenters\Task\ViewModel;

final class TaskIndexViewModel
{
    /**
     * @param list<TaskIndexItemViewModel> $tasks
     * @param list<TaskStatusOptionViewModel> $status_options
     */
    public function __construct(
        public array $tasks,
        public array $status_options,
        public ?string $success_message,
        public ?string $error_message
    ) {
    }

    public function hasTasks(): bool
    {
        return count($this->tasks) > 0;
    }
}
