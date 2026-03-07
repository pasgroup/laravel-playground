<?php

namespace Tests\Unit\Models\Task;

use App\Models\Task;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DeleteByUuidTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function itReturnsTrueWhenDeleteAffectsOneRow(): void
    {
        $task_uuid = '00000000-0000-0000-0000-000000000000';
        $query_mock = Mockery::mock();
        $query_mock->shouldReceive('delete')->once()->andReturn(1);

        $task = Mockery::mock(Task::class)->makePartial();
        $task->shouldReceive('where')
            ->once()
            ->with('task_uuid', $task_uuid)
            ->andReturn($query_mock);

        /** @var Task $task */
        $result = $task->deleteByUuid($task_uuid);

        $this->assertTrue($result);
    }

    #[Test]
    public function itReturnsFalseWhenDeleteAffectsZeroRows(): void
    {
        $task_uuid = '11111111-1111-1111-1111-111111111111';
        $query_mock = Mockery::mock();
        $query_mock->shouldReceive('delete')->once()->andReturn(0);

        $task = Mockery::mock(Task::class)->makePartial();
        $task->shouldReceive('where')
            ->once()
            ->with('task_uuid', $task_uuid)
            ->andReturn($query_mock);

        /** @var Task $task */
        $result = $task->deleteByUuid($task_uuid);

        $this->assertFalse($result);
    }
}
