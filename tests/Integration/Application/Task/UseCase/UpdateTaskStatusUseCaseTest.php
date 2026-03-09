<?php

namespace Tests\Integration\Application\Task\UseCase;

use App\Application\Task\DTO\UpdateTaskStatusInput;
use App\Application\Task\Exceptions\TaskNotFoundException;
use App\Application\Task\UseCase\UpdateTaskStatusUseCase;
use App\Domain\Task\ValueObject\TaskStatus;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UpdateTaskStatusUseCaseTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function itUpdatesStatus(): void
    {
        $task = Task::factory()->notStarted()->create();
        $use_case = app(UpdateTaskStatusUseCase::class);

        $use_case->handle(
            new UpdateTaskStatusInput($task->task_uuid, TaskStatus::IN_PROGRESS->value)
        );

        $this->assertDatabaseHas('tasks', [
            'task_uuid' => $task->task_uuid,
            'status' => TaskStatus::IN_PROGRESS->value,
        ]);
    }

    #[Test]
    public function itThrowsWhenTaskNotFound(): void
    {
        $this->expectException(TaskNotFoundException::class);

        $use_case = app(UpdateTaskStatusUseCase::class);
        $use_case->handle(
            new UpdateTaskStatusInput(
                '00000000-0000-0000-0000-000000000000',
                TaskStatus::IN_PROGRESS->value
            )
        );
    }
}
