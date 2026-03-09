<?php

namespace Tests\Unit\Application\Task\UseCase;

use App\Application\Task\DTO\DeleteTaskInput;
use App\Application\Task\Exceptions\TaskNotFoundException;
use App\Application\Task\Repository\TaskRepositoryInterface;
use App\Application\Task\UseCase\DeleteTaskUseCase;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DeleteTaskUseCaseTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function itDeletesTaskAndReturnsSuccessMessage(): void
    {
        $task_uuid = '11111111-1111-1111-1111-111111111111';
        /** @var TaskRepositoryInterface $task_repository */
        $task_repository = Mockery::mock(TaskRepositoryInterface::class);
        $task_repository->shouldReceive('deleteTaskByUuid')
            ->once()
            ->with($task_uuid)
            ->andReturn(true);
        $use_case = new DeleteTaskUseCase($task_repository);

        $output = $use_case->handle(new DeleteTaskInput($task_uuid));

        $this->assertSame('success', $output->flash_type);
        $this->assertSame('タスクを削除しました。', $output->flash_message);
    }

    #[Test]
    public function itThrowsWhenTaskNotFound(): void
    {
        $this->expectException(TaskNotFoundException::class);
        $task_uuid = '00000000-0000-0000-0000-000000000000';
        /** @var TaskRepositoryInterface $task_repository */
        $task_repository = Mockery::mock(TaskRepositoryInterface::class);
        $task_repository->shouldReceive('deleteTaskByUuid')
            ->once()
            ->with($task_uuid)
            ->andReturn(false);

        $use_case = new DeleteTaskUseCase($task_repository);
        $use_case->handle(
            new DeleteTaskInput($task_uuid)
        );
    }
}
