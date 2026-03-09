<?php

namespace Tests\Unit\Domain\Task;

use App\Domain\Task\Service\TaskCreationService;
use App\Domain\Task\ValueObject\TaskStatus;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TaskCreationServiceTest extends TestCase
{
    #[Test]
    public function itCreatesNewTaskEntityWithParsedDueDate(): void
    {
        $task_creation_service = new TaskCreationService();

        $task_entity = $task_creation_service->createForNewTask(
            'service title',
            'service detail',
            '2026-04-01'
        );

        $this->assertNull($task_entity->task_id);
        $this->assertNull($task_entity->task_uuid);
        $this->assertSame('service title', $task_entity->title);
        $this->assertSame('service detail', $task_entity->detail);
        $this->assertSame('2026-04-01', $task_entity->due_date?->format('Y-m-d'));
        $this->assertSame(TaskStatus::NOT_STARTED->value, $task_entity->status);
    }

    #[Test]
    public function itCreatesNewTaskEntityWithNullableDueDate(): void
    {
        $task_creation_service = new TaskCreationService();

        $task_entity = $task_creation_service->createForNewTask(
            'service title',
            null,
            null
        );

        $this->assertNull($task_entity->detail);
        $this->assertNull($task_entity->due_date);
    }
}
