<?php

namespace App\Domain\Task\ValueObject;

enum TaskStatus: string
{
    case NOT_STARTED = 'not_started';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::NOT_STARTED => '未着手',
            self::IN_PROGRESS => '進行中',
            self::COMPLETED => '完了',
        };
    }

    public function isCompleted(): bool
    {
        return $this === self::COMPLETED;
    }
}
