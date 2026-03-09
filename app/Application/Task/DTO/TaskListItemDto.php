<?php

namespace App\Application\Task\DTO;

use Carbon\CarbonInterface;

final class TaskListItemDto
{
    public function __construct(
        public int $task_id,
        public string $task_uuid,
        public string $title,
        public ?string $detail,
        public ?CarbonInterface $due_date,
        public string $status,
        public string $status_label,
        public bool $is_completed,
        public bool $is_overdue
    ) {
    }
}
