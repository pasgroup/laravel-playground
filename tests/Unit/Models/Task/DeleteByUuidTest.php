<?php

namespace Tests\Unit\Models\Task;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DeleteByUuidTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function itReturnsTrueAndSoftDeletesTaskWhenUuidExists(): void
    {
        $task = Task::factory()->notStarted()->create();
        $task_uuid = $task->task_uuid;

        $model = new Task();
        $result = $model->deleteByUuid($task_uuid);

        $this->assertTrue($result);
        $this->assertSoftDeleted('tasks', ['task_id' => $task->task_id]);
    }

    #[Test]
    public function itReturnsFalseWhenUuidDoesNotExist(): void
    {
        $model = new Task();
        $result = $model->deleteByUuid('00000000-0000-0000-0000-000000000000');

        $this->assertFalse($result);
    }

    #[Test]
    public function itReturnsFalseWhenTaskIsAlreadySoftDeleted(): void
    {
        $task = Task::factory()->notStarted()->create();
        $task->delete();
        $task_uuid = $task->task_uuid;

        $model = new Task();
        $result = $model->deleteByUuid($task_uuid);

        $this->assertFalse($result);
    }
}
