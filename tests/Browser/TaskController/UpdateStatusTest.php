<?php

namespace Tests\Browser\TaskController;

use App\Models\Task;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\Test;
use Tests\DuskTestCase;

class UpdateStatusTest extends DuskTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh');
    }

    #[Test]
    public function itUpdatesStatusAndShowsSuccessToast(): void
    {
        $task = Task::factory()->notStarted()->create([
            'title' => 'ステータス変更するタスク',
        ]);

        $this->browse(function (Browser $browser) use ($task) {
            $browser->visit(route('tasks.index'))
                ->assertSeeIn('@index-task-title-' . $task->task_id, $task->title)
                ->assertSelected('@index-status-select-' . $task->task_id, Task::STATUS_NOT_STARTED)
                ->select('@index-status-select-' . $task->task_id, Task::STATUS_IN_PROGRESS)
                ->waitFor('@toast-message', 3)
                ->assertSeeIn('@toast-message', 'タスクのステータスを更新しました。')
                ->assertSelected('@index-status-select-' . $task->task_id, Task::STATUS_IN_PROGRESS);
        });
    }
}
