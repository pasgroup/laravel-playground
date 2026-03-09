<?php

namespace App\Domain\Task\Entity;

use App\Domain\Task\Specification\TaskDeadline;
use App\Domain\Task\ValueObject\TaskStatus;
use Carbon\CarbonInterface;

final class TaskEntity
{
    public function __construct(
        public ?int $task_id,
        public ?string $task_uuid,
        public string $title,
        public ?string $detail,
        public ?CarbonInterface $due_date,
        public string $status
    ) {
    }

    public function statusLabel(): string
    {
        $task_status = TaskStatus::tryFrom($this->status);

        if ($task_status === null) {
            return '未設定';
        }

        return $task_status->label();
    }

    public function isCompleted(): bool
    {
        $task_status = TaskStatus::tryFrom($this->status);

        return $task_status?->isCompleted() ?? false;
    }

    public function isOverdue(TaskDeadline $task_deadline): bool
    {
        return $task_deadline->isOverdue($this->status, $this->due_date);
    }
}
