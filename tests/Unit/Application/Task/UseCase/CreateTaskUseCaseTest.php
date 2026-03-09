<?php

namespace Tests\Unit\Application\Task\UseCase;

use App\Application\Task\DTO\CreateTaskInput;
use App\Application\Task\UseCase\CreateTaskUseCase;
use App\Domain\Task\TaskStatus;
use App\Models\Task;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CreateTaskUseCaseTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function itCreatesTaskAndReturnsSuccessMessage(): void
    {
        $builder = Mockery::mock();
        $builder->shouldReceive('create')
            ->once()
            ->with([
                'title' => 'ユースケース作成テスト',
                'detail' => '詳細',
                'due_date' => '2026-03-31',
                'status' => TaskStatus::NOT_STARTED->value,
            ]);

        /** @var Task $task */
        $task = Mockery::mock(Task::class)->makePartial();
        $task->shouldReceive('newQuery')
            ->once()
            ->andReturn($builder);

        $use_case = new CreateTaskUseCase($task);
        $output = $use_case->handle(
            new CreateTaskInput(
                'ユースケース作成テスト',
                '詳細',
                '2026-03-31'
            )
        );

        $this->assertSame('success', $output->flash_type);
        $this->assertSame('タスクを登録しました。', $output->flash_message);
    }
}
