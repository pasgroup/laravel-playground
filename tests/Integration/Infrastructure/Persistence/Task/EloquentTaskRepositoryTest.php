<?php

namespace Tests\Integration\Infrastructure\Persistence\Task;

use App\Domain\Task\Specification\TaskDeadline;
use App\Infrastructure\Persistence\Task\EloquentTaskRepository;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EloquentTaskRepositoryTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function itMapsUnknownStatusWithoutLeakingEloquentModel(): void
    {
        $task = Task::factory()->create([
            'status' => 'unknown_status',
            'due_date' => now()->subDay()->toDateString(),
        ]);
        $repository = app(EloquentTaskRepository::class);
        $items = $repository->getTaskOrderByDueDate();
        $mapped = collect($items)->firstWhere('task_uuid', $task->task_uuid);

        $this->assertNotNull($mapped);
        $this->assertSame('unknown_status', $mapped->status);
        $this->assertSame('未設定', $mapped->statusLabel());
        $this->assertFalse($mapped->isCompleted());
        $this->assertFalse($mapped->isOverdue(new TaskDeadline()));
        $this->assertNotInstanceOf(Task::class, $mapped);
    }

    #[Test]
    public function itMapsNullableFieldsToDtoShape(): void
    {
        $task = Task::factory()->create([
            'detail' => null,
            'due_date' => null,
        ]);
        $repository = app(EloquentTaskRepository::class);
        $items = $repository->getTaskOrderByDueDate();
        $mapped = collect($items)->firstWhere('task_uuid', $task->task_uuid);

        $this->assertNotNull($mapped);
        $this->assertNull($mapped->detail);
        $this->assertNull($mapped->due_date);
        $this->assertSame('not_started', $mapped->status);
    }
}
