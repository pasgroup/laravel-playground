<?php

namespace Tests\Unit\Application\Task\UseCase;

use App\Application\Task\Repository\TaskRepositoryInterface;
use App\Application\Task\UseCase\ListTasksUseCase;
use Illuminate\Database\Eloquent\Collection;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ListTasksUseCaseTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function itReturnsTasksByExpectedOrder(): void
    {
        $expected_tasks = new Collection([
            (object) ['task_id' => 1],
            (object) ['task_id' => 2],
        ]);

        /** @var TaskRepositoryInterface $task_repository */
        $task_repository = Mockery::mock(TaskRepositoryInterface::class);
        $task_repository->shouldReceive('getTaskOrderByDueDate')
            ->once()
            ->andReturn($expected_tasks);

        $use_case = new ListTasksUseCase($task_repository);
        $output = $use_case->handle();

        $this->assertSame($expected_tasks, $output->tasks);
    }
}
