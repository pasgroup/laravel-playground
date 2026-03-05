<?php

namespace Tests\Unit\Models\Task;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GetTaskOrderByDueDateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * due_date IS NULL をロジックと判断してテストを作成した
     */
    #[Test]
    public function returnsTasksOrderedByDueDateAscThenTaskIdAscWithNullsLast(): void
    {
        $task_later = Task::create([
            'title' => '後ろの日付',
            'due_date' => '2026-03-10',
            'status' => Task::STATUS_NOT_STARTED,
        ]);
        $task_null = Task::create([
            'title' => '期限なし',
            'due_date' => null,
            'status' => Task::STATUS_NOT_STARTED,
        ]);
        $task_earlier = Task::create([
            'title' => '前の日付',
            'due_date' => '2026-03-01',
            'status' => Task::STATUS_NOT_STARTED,
        ]);

        $task = new Task();
        $result = $task->getTaskOrderByDueDate();

        $this->assertCount(3, $result);
        $ids = $result->pluck('task_id')->all();
        $this->assertSame([$task_earlier->task_id, $task_later->task_id, $task_null->task_id], $ids);
    }

    #[Test]
    public function whenDueDatesAreSameOrdersByTaskIdAsc(): void
    {
        $task_b = Task::create([
            'title' => 'B',
            'due_date' => '2026-03-05',
            'status' => Task::STATUS_NOT_STARTED,
        ]);
        $task_a = Task::create([
            'title' => 'A',
            'due_date' => '2026-03-05',
            'status' => Task::STATUS_NOT_STARTED,
        ]);

        $task = new Task();
        $result = $task->getTaskOrderByDueDate();

        $this->assertSame([$task_b->task_id, $task_a->task_id], $result->pluck('task_id')->all());
    }

    #[Test]
    public function selectsOnlyRequiredColumns(): void
    {
        Task::create([
            'title' => 'テスト',
            'due_date' => '2026-03-01',
            'status' => Task::STATUS_NOT_STARTED,
        ]);

        $task = new Task();
        $first = $task->getTaskOrderByDueDate()->first();

        $this->assertNotNull($first);
        $this->assertArrayHasKey('task_id', $first->getAttributes());
        $this->assertArrayHasKey('title', $first->getAttributes());
        $this->assertArrayHasKey('detail', $first->getAttributes());
        $this->assertArrayHasKey('due_date', $first->getAttributes());
        $this->assertArrayHasKey('status', $first->getAttributes());
        $this->assertArrayNotHasKey('created_at', $first->getAttributes());
        $this->assertArrayNotHasKey('updated_at', $first->getAttributes());
    }

    #[Test]
    public function excludesSoftDeletedTasksFromList(): void
    {
        $task_visible = Task::create([
            'title' => '表示するタスク',
            'due_date' => '2026-03-01',
            'status' => Task::STATUS_NOT_STARTED,
        ]);
        $task_deleted = Task::create([
            'title' => '削除したタスク',
            'due_date' => '2026-03-02',
            'status' => Task::STATUS_NOT_STARTED,
        ]);
        $task_deleted->delete();

        $task = new Task();
        $result = $task->getTaskOrderByDueDate();

        $this->assertCount(1, $result);
        $this->assertSame($task_visible->task_id, $result->first()->task_id);
        $this->assertSame('表示するタスク', $result->first()->title);
    }
}
