<?php

namespace Tests\Unit\Models\Task;

use App\Models\Task;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UpdateStatusByUuidTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function itReturnsTrueWhenUpdateAffectsOneRow(): void
    {
        $task_uuid = '00000000-0000-0000-0000-000000000000';
        $status = Task::STATUS_IN_PROGRESS;
        $query_mock = Mockery::mock();
        $query_mock->shouldReceive('update')
            ->once()
            ->with(['status' => $status])
            ->andReturn(1);

        $task = Mockery::mock(Task::class)->makePartial();
        $task->shouldReceive('where')
            ->once()
            ->with('task_uuid', $task_uuid)
            ->andReturn($query_mock);

        /** @var Task $task */
        $result = $task->updateStatusByUuid($task_uuid, $status);

        $this->assertTrue($result);
    }

    #[Test]
    public function itReturnsFalseWhenUpdateAffectsZeroRows(): void
    {
        $task_uuid = '11111111-1111-1111-1111-111111111111';
        $status = Task::STATUS_COMPLETED;
        $query_mock = Mockery::mock();
        $query_mock->shouldReceive('update')
            ->once()
            ->with(['status' => $status])
            ->andReturn(0);

        $exists_builder = Mockery::mock();
        $exists_builder->shouldReceive('where')
            ->once()
            ->with('task_uuid', $task_uuid)
            ->andReturnSelf();
        $exists_builder->shouldReceive('exists')
            ->once()
            ->andReturn(false);

        $task = Mockery::mock(Task::class)->makePartial();
        $task->shouldReceive('where')
            ->once()
            ->with('task_uuid', $task_uuid)
            ->andReturn($query_mock);
        $task->shouldReceive('withoutTrashed')
            ->once()
            ->andReturn($exists_builder);

        /** @var Task $task */
        $result = $task->updateStatusByUuid($task_uuid, $status);

        $this->assertFalse($result);
    }

    #[Test]
    public function itReturnsTrueWhenUpdateAffectsZeroRowsButRecordExists(): void
    {
        $task_uuid = '22222222-2222-2222-2222-222222222222';
        $status = Task::STATUS_NOT_STARTED;
        $query_mock = Mockery::mock();
        $query_mock->shouldReceive('update')
            ->once()
            ->with(['status' => $status])
            ->andReturn(0);

        $exists_builder = Mockery::mock();
        $exists_builder->shouldReceive('where')
            ->once()
            ->with('task_uuid', $task_uuid)
            ->andReturnSelf();
        $exists_builder->shouldReceive('exists')
            ->once()
            ->andReturn(true);

        $task = Mockery::mock(Task::class)->makePartial();
        $task->shouldReceive('where')
            ->once()
            ->with('task_uuid', $task_uuid)
            ->andReturn($query_mock);
        $task->shouldReceive('withoutTrashed')
            ->once()
            ->andReturn($exists_builder);

        /** @var Task $task */
        $result = $task->updateStatusByUuid($task_uuid, $status);

        $this->assertTrue($result);
    }
}
