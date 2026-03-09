<?php

namespace App\Http\Presenters\Task\ViewModel;

final class TaskIndexItemViewModel
{
    public function __construct(
        public int $task_id,
        public string $task_uuid,
        public string $title,
        public ?string $detail,
        public string $due_date_text,
        public string $status,
        public bool $is_overdue,
        public bool $is_first_completed
    ) {
    }
}
