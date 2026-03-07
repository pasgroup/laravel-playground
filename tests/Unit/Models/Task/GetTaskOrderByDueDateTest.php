<?php

namespace Tests\Unit\Models\Task;

use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GetTaskOrderByDueDateTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function itCallsQueryWithExpectedOrderAndReturnsCollection(): void
    {
        $builder = Mockery::mock();
        $builder->shouldReceive('select')
            ->once()
            ->with('task_id', 'task_uuid', 'title', 'detail', 'due_date', 'status')
            ->andReturnSelf();
        $builder->shouldReceive('orderByRaw')
            ->once()
            ->with("(status != 'completed') DESC")
            ->ordered()
            ->andReturnSelf();
        $builder->shouldReceive('orderByRaw')
            ->once()
            ->with('due_date IS NULL')
            ->ordered()
            ->andReturnSelf();
        $builder->shouldReceive('orderBy')
            ->once()
            ->with('due_date', 'asc')
            ->ordered()
            ->andReturnSelf();
        $builder->shouldReceive('orderBy')
            ->once()
            ->with('task_id', 'asc')
            ->ordered()
            ->andReturnSelf();

        $expected = new Collection([]);
        $builder->shouldReceive('get')->once()->andReturn($expected);

        /** @var Task $task */
        $task = Mockery::mock(Task::class)->makePartial();
        $task->shouldReceive('query')
            ->once()
            ->andReturn($builder);

        $result = $task->getTaskOrderByDueDate();

        $this->assertSame($expected, $result);
    }

    #[Test]
    public function itReturnsQueryBuilderResult(): void
    {
        $task_earlier = (object) ['task_id' => 1, 'due_date' => '2026-03-01'];
        $task_later = (object) ['task_id' => 2, 'due_date' => '2026-03-10'];
        $task_null = (object) ['task_id' => 3, 'due_date' => null];
        $collection = new Collection([$task_earlier, $task_later, $task_null]);

        $builder = Mockery::mock();
        $builder->shouldReceive('select')
            ->andReturnSelf();
        $builder->shouldReceive('orderByRaw')
            ->with("(status != 'completed') DESC")
            ->ordered()
            ->andReturnSelf();
        $builder->shouldReceive('orderByRaw')
            ->with('due_date IS NULL')
            ->ordered()
            ->andReturnSelf();
        $builder->shouldReceive('orderBy')
            ->with('due_date', 'asc')
            ->ordered()
            ->andReturnSelf();
        $builder->shouldReceive('orderBy')
            ->with('task_id', 'asc')
            ->ordered()
            ->andReturnSelf();
        $builder->shouldReceive('get')
            ->andReturn($collection);

        /** @var Task $task */
        $task = Mockery::mock(Task::class)->makePartial();
        $task->shouldReceive('query')->andReturn($builder);

        $result = $task->getTaskOrderByDueDate();

        $this->assertSame($collection, $result);
    }
}
