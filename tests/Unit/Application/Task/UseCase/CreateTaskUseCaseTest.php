<?php

namespace Tests\Unit\Application\Task\UseCase;

use App\Application\Task\DTO\CreateTaskInput;
use App\Application\Task\Repository\TaskRepositoryInterface;
use App\Application\Task\UseCase\CreateTaskUseCase;
use App\Domain\Task\TaskStatus;
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
        /** @var TaskRepositoryInterface $task_repository */
        $task_repository = Mockery::mock(TaskRepositoryInterface::class);
        $task_repository->shouldReceive('createTask')
            ->once()
            ->with(
                'ユースケース作成テスト',
                '詳細',
                '2026-03-31',
                TaskStatus::NOT_STARTED->value
            )
            ->andReturn(123);

        $use_case = new CreateTaskUseCase($task_repository);
        $output = $use_case->handle(
            new CreateTaskInput(
                'ユースケース作成テスト',
                '詳細',
                '2026-03-31'
            )
        );

        $this->assertSame('success', $output->flash_type);
        $this->assertSame('タスクを登録しました。', $output->flash_message);
        $this->assertSame(123, $output->task_id);
    }
}
