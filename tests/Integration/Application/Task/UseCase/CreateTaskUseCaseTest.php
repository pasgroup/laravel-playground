<?php

namespace Tests\Integration\Application\Task\UseCase;

use App\Application\Task\DTO\CreateTaskInput;
use App\Application\Task\UseCase\CreateTaskUseCase;
use App\Domain\Task\TaskStatus;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CreateTaskUseCaseTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function itCreatesTaskWithExpectedStatus(): void
    {
        $use_case = new CreateTaskUseCase(new Task());
        $output = $use_case->handle(
            new CreateTaskInput(
                '統合テスト_作成',
                '統合テスト詳細',
                '2026-04-01'
            )
        );
        $created_task = Task::query()->where('task_id', $output->task_id)->first();

        $this->assertNotNull($output->task_id);
        $this->assertNotNull($created_task);
        $this->assertDatabaseHas('tasks', [
            'title' => '統合テスト_作成',
            'status' => TaskStatus::NOT_STARTED->value,
        ]);
    }
}
