<?php

namespace Tests\Integration\Application\Task\UseCase;

use App\Application\Task\UseCase\ListTasksUseCase;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ListTasksUseCaseTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function itReturnsTasksOrderedByDueDateAndCompletion(): void
    {
        $first_task = Task::factory()->notStarted()->create([
            'due_date' => '2026-03-10',
        ]);
        $second_task = Task::factory()->notStarted()->create([
            'due_date' => '2026-03-12',
        ]);
        Task::factory()->completed()->create([
            'due_date' => '2026-03-01',
        ]);

        $use_case = app(ListTasksUseCase::class);
        $output = $use_case->handle();

        $this->assertCount(3, $output->tasks);
        $this->assertSame($first_task->task_id, $output->tasks->first()->task_id);
        $this->assertSame($second_task->task_id, $output->tasks->get(1)->task_id);
    }
}
