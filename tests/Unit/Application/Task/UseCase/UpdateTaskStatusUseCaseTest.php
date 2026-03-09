<?php

namespace Tests\Unit\Application\Task\UseCase;

use App\Application\Task\DTO\UpdateTaskStatusInput;
use App\Application\Task\Exceptions\InvalidTaskStatusTransitionException;
use App\Application\Task\Exceptions\TaskNotFoundException;
use App\Application\Task\UseCase\UpdateTaskStatusUseCase;
use App\Domain\Task\TaskStatus;
use App\Domain\Task\TaskTransition;
use App\Models\Task;
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
        $current_task = (object) ['task_id' => 1, 'status' => TaskStatus::NOT_STARTED->value];
        $read_builder = Mockery::mock();
        $read_builder->shouldReceive('select')
            ->once()
            ->with('task_id', 'status')
            ->andReturnSelf();
        $read_builder->shouldReceive('where')
            ->once()
            ->with('task_uuid', $task_uuid)
            ->andReturnSelf();
        $read_builder->shouldReceive('first')
            ->once()
            ->andReturn($current_task);
        $update_builder = Mockery::mock();
        $update_builder->shouldReceive('where')
            ->once()
            ->with('task_uuid', $task_uuid)
            ->andReturnSelf();
        $update_builder->shouldReceive('where')
            ->once()
            ->with('status', TaskStatus::NOT_STARTED->value)
            ->andReturnSelf();
        $update_builder->shouldReceive('update')
            ->once()
            ->with([
                'status' => TaskStatus::IN_PROGRESS->value,
            ])
            ->andReturn(1);

        /** @var Task $task */
        $task = Mockery::mock(Task::class)->makePartial();
        $task->shouldReceive('newQuery')
            ->twice()
            ->andReturn($read_builder, $update_builder);

        $use_case = new UpdateTaskStatusUseCase($task, new TaskTransition());

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
        $builder = Mockery::mock();
        $builder->shouldReceive('select')
            ->once()
            ->andReturnSelf();
        $builder->shouldReceive('where')
            ->once()
            ->with('task_uuid', $task_uuid)
            ->andReturnSelf();
        $builder->shouldReceive('first')
            ->once()
            ->andReturn(null);

        /** @var Task $task */
        $task = Mockery::mock(Task::class)->makePartial();
        $task->shouldReceive('newQuery')
            ->once()
            ->andReturn($builder);

        $use_case = new UpdateTaskStatusUseCase($task, new TaskTransition());
        $use_case->handle(
            new UpdateTaskStatusInput(
                $task_uuid,
                TaskStatus::IN_PROGRESS->value
            )
        );
    }

    #[Test]
    public function itThrowsWhenStoredStatusIsUnknown(): void
    {
        $this->expectException(InvalidTaskStatusTransitionException::class);
        $task_uuid = '11111111-1111-1111-1111-111111111111';
        $current_task = (object) ['task_id' => 1, 'status' => 'invalid_status'];
        $read_builder = Mockery::mock();
        $read_builder->shouldReceive('select')
            ->once()
            ->andReturnSelf();
        $read_builder->shouldReceive('where')
            ->once()
            ->with('task_uuid', $task_uuid)
            ->andReturnSelf();
        $read_builder->shouldReceive('first')
            ->once()
            ->andReturn($current_task);

        /** @var Task $task */
        $task = Mockery::mock(Task::class)->makePartial();
        $task->shouldReceive('newQuery')
            ->once()
            ->andReturn($read_builder);

        $use_case = new UpdateTaskStatusUseCase($task, new TaskTransition());
        $use_case->handle(
            new UpdateTaskStatusInput($task_uuid, TaskStatus::IN_PROGRESS->value)
        );
    }
}
