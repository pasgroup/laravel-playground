<?php

namespace Tests\Unit\Domain\Task;

use App\Domain\Task\Entity\TaskEntity;
use App\Domain\Task\Specification\TaskDeadline;
use App\Domain\Task\ValueObject\TaskStatus;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TaskEntityTest extends TestCase
{
    #[Test]
    public function itReturnsStatusLabelForKnownStatus(): void
    {
        $task_entity = new TaskEntity(
            1,
            '11111111-1111-1111-1111-111111111111',
            'task',
            null,
            null,
            TaskStatus::IN_PROGRESS->value
        );

        $this->assertSame('進行中', $task_entity->statusLabel());
    }

    #[Test]
    public function itReturnsDefaultLabelForUnknownStatus(): void
    {
        $task_entity = new TaskEntity(
            1,
            '11111111-1111-1111-1111-111111111111',
            'task',
            null,
            null,
            'unknown_status'
        );

        $this->assertSame('未設定', $task_entity->statusLabel());
    }

    #[Test]
    public function itDeterminesCompletedStateFromStatus(): void
    {
        $completed_task = new TaskEntity(
            1,
            '11111111-1111-1111-1111-111111111111',
            'task',
            null,
            null,
            TaskStatus::COMPLETED->value
        );
        $unknown_status_task = new TaskEntity(
            2,
            '22222222-2222-2222-2222-222222222222',
            'task',
            null,
            null,
            'unknown_status'
        );

        $this->assertTrue($completed_task->isCompleted());
        $this->assertFalse($unknown_status_task->isCompleted());
    }

    #[Test]
    public function itDeterminesOverdueViaDeadlineDomainRule(): void
    {
        $task_deadline = new TaskDeadline();
        $overdue_task = new TaskEntity(
            1,
            '11111111-1111-1111-1111-111111111111',
            'task',
            null,
            CarbonImmutable::parse('2026-03-09'),
            TaskStatus::NOT_STARTED->value
        );
        $not_overdue_task = new TaskEntity(
            2,
            '22222222-2222-2222-2222-222222222222',
            'task',
            null,
            CarbonImmutable::parse('2026-03-11'),
            TaskStatus::NOT_STARTED->value
        );

        Carbon::setTestNow('2026-03-10 12:00:00');
        try {
            $this->assertTrue($overdue_task->isOverdue($task_deadline));
            $this->assertFalse($not_overdue_task->isOverdue($task_deadline));
        } finally {
            Carbon::setTestNow();
        }
    }
}
