<?php

namespace Tests\Integration\Application\Task\UseCase;

use App\Application\Task\DTO\DeleteTaskInput;
use App\Application\Task\Exceptions\TaskNotFoundException;
use App\Application\Task\UseCase\DeleteTaskUseCase;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DeleteTaskUseCaseTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function itDeletesTask(): void
    {
        $task = Task::factory()->notStarted()->create();
        $use_case = app(DeleteTaskUseCase::class);

        $output = $use_case->handle(new DeleteTaskInput($task->task_uuid));

        $this->assertSoftDeleted('tasks', [
            'task_uuid' => $task->task_uuid,
        ]);
        $this->assertSame('success', $output->flash_type);
        $this->assertSame('タスクを削除しました。', $output->flash_message);
    }

    #[Test]
    public function itThrowsWhenTaskNotFound(): void
    {
        $this->expectException(TaskNotFoundException::class);

        $use_case = app(DeleteTaskUseCase::class);
        $use_case->handle(
            new DeleteTaskInput('00000000-0000-0000-0000-000000000000')
        );
    }
}
