<?php

namespace Tests\Unit\Application\Task\UseCase;

use App\Application\Task\DTO\UpdateTaskStatusInput;
use App\Application\Task\Exceptions\InvalidTaskStatusTransitionException;
use App\Application\Task\Exceptions\TaskNotFoundException;
use App\Application\Task\Repository\TaskRepositoryInterface;
use App\Application\Task\UseCase\UpdateTaskStatusUseCase;
use App\Domain\Task\TaskStatus;
use App\Domain\Task\TaskTransition;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UpdateTaskStatusUseCaseTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function itUpdatesStatusAndReturnsSuccessMessage(): void
    {
        $task_uuid = '11111111-1111-1111-1111-111111111111';
        /** @var TaskRepositoryInterface $task_repository */
        $task_repository = Mockery::mock(TaskRepositoryInterface::class);
        $task_repository->shouldReceive('findTaskStatusByUuid')
            ->once()
            ->with($task_uuid)
            ->andReturn(TaskStatus::NOT_STARTED->value);
        $task_repository->shouldReceive('updateTaskStatusByUuidAndCurrentStatus')
            ->once()
            ->with(
                $task_uuid,
                TaskStatus::NOT_STARTED->value,
                TaskStatus::IN_PROGRESS->value
            )
            ->andReturn(1);

        $use_case = new UpdateTaskStatusUseCase($task_repository, new TaskTransition());

        $output = $use_case->handle(
            new UpdateTaskStatusInput($task_uuid, TaskStatus::IN_PROGRESS->value)
        );

        $this->assertSame('success', $output->flash_type);
        $this->assertSame('タスクのステータスを更新しました。', $output->flash_message);
    }

    #[Test]
    public function itThrowsWhenTaskNotFound(): void
    {
        $this->expectException(TaskNotFoundException::class);
        $task_uuid = '00000000-0000-0000-0000-000000000000';
        /** @var TaskRepositoryInterface $task_repository */
        $task_repository = Mockery::mock(TaskRepositoryInterface::class);
        $task_repository->shouldReceive('findTaskStatusByUuid')
            ->once()
            ->with($task_uuid)
            ->andReturn(null);

        $use_case = new UpdateTaskStatusUseCase($task_repository, new TaskTransition());
        $use_case->handle(
            new UpdateTaskStatusInput(
                $task_uuid,
                TaskStatus::IN_PROGRESS->value
            )
        );
    }

    #[Test]
    public function itReturnsSuccessWhenCurrentAndNextStatusAreSame(): void
    {
        $task_uuid = '11111111-1111-1111-1111-111111111111';
        /** @var TaskRepositoryInterface $task_repository */
        $task_repository = Mockery::mock(TaskRepositoryInterface::class);
        $task_repository->shouldReceive('findTaskStatusByUuid')
            ->once()
            ->with($task_uuid)
            ->andReturn(TaskStatus::IN_PROGRESS->value);

        $use_case = new UpdateTaskStatusUseCase($task_repository, new TaskTransition());

        $output = $use_case->handle(
            new UpdateTaskStatusInput($task_uuid, TaskStatus::IN_PROGRESS->value)
        );

        $this->assertSame('success', $output->flash_type);
        $this->assertSame('タスクのステータスを更新しました。', $output->flash_message);
    }

    #[Test]
    public function itThrowsWhenInputStatusIsInvalid(): void
    {
        $this->expectException(InvalidTaskStatusTransitionException::class);
        $task_uuid = '11111111-1111-1111-1111-111111111111';
        /** @var TaskRepositoryInterface $task_repository */
        $task_repository = Mockery::mock(TaskRepositoryInterface::class);

        $use_case = new UpdateTaskStatusUseCase($task_repository, new TaskTransition());
        $use_case->handle(
            new UpdateTaskStatusInput($task_uuid, 'invalid_status')
        );
    }

    #[Test]
    public function itThrowsWhenStoredStatusIsUnknown(): void
    {
        $this->expectException(InvalidTaskStatusTransitionException::class);
        $task_uuid = '11111111-1111-1111-1111-111111111111';
        /** @var TaskRepositoryInterface $task_repository */
        $task_repository = Mockery::mock(TaskRepositoryInterface::class);
        $task_repository->shouldReceive('findTaskStatusByUuid')
            ->once()
            ->with($task_uuid)
            ->andReturn('invalid_status');

        $use_case = new UpdateTaskStatusUseCase($task_repository, new TaskTransition());
        $use_case->handle(
            new UpdateTaskStatusInput($task_uuid, TaskStatus::IN_PROGRESS->value)
        );
    }
}
