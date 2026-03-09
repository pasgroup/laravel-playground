<?php

namespace Tests\Integration\Http\Controllers;

use App\Application\Task\Repository\TaskRepositoryInterface;
use App\Domain\Task\ValueObject\TaskStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TaskControllerExceptionHandlingTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function itRedirectsWithErrorFlashWhenDeleteUseCaseThrows(): void
    {
        /** @var TaskRepositoryInterface&\Mockery\MockInterface $mock */
        $mock = Mockery::mock(TaskRepositoryInterface::class);
        $mock->shouldReceive('deleteTaskByUuid')
            ->once()
            ->with('11111111-1111-1111-1111-111111111111')
            ->andReturn(false);
        app()->instance(TaskRepositoryInterface::class, $mock);

        $response = $this->delete(
            route('tasks.destroy', ['task_uuid' => '11111111-1111-1111-1111-111111111111'])
        );

        $response->assertRedirect(route('tasks.index'));
        $response->assertSessionHas('error', '指定されたタスクは存在しないか、既に削除されています。');
    }

    #[Test]
    public function itRedirectsWithErrorFlashWhenUpdateUseCaseThrows(): void
    {
        /** @var TaskRepositoryInterface&\Mockery\MockInterface $mock */
        $mock = Mockery::mock(TaskRepositoryInterface::class);
        $mock->shouldReceive('findByUuid')
            ->once()
            ->with('11111111-1111-1111-1111-111111111111')
            ->andReturn(null);
        app()->instance(TaskRepositoryInterface::class, $mock);

        $response = $this->post(
            route('tasks.status.update', ['task_uuid' => '11111111-1111-1111-1111-111111111111']),
            ['status' => TaskStatus::IN_PROGRESS->value]
        );

        $response->assertRedirect(route('tasks.index'));
        $response->assertSessionHas('error', '指定されたタスクは存在しないか、既に削除されています。');
    }
}
