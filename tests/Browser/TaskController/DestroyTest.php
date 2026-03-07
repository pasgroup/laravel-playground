<?php

namespace Tests\Browser\TaskController;

use App\Models\Task;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\Test;
use Tests\DuskTestCase;

class DestroyTest extends DuskTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh');
    }

    #[Test]
    public function itDeletesTaskAndShowsSuccessToast(): void
    {
        $task_for_not_delete = Task::factory()->notStarted()->create([
            'title' => '削除しないタスク',
        ]);
        $task_for_delete = Task::factory()->notStarted()->create([
            'title' => '削除するタスク',
        ]);

        $this->browse(function (Browser $browser) use ($task_for_not_delete, $task_for_delete) {
            $browser->visit(route('tasks.index'))
                ->assertSeeIn('@index-task-title-' . $task_for_not_delete->task_id, $task_for_not_delete->title)
                ->assertSeeIn('@index-task-title-' . $task_for_delete->task_id, $task_for_delete->title)
                ->press('@index-delete-btn-' . $task_for_delete->task_id);
            $browser->acceptDialog();

            $browser->waitFor('@toast-message', 3)
                ->assertSeeIn('@toast-message', 'タスクを削除しました。')
                ->assertSeeIn('@index-task-title-' . $task_for_not_delete->task_id, $task_for_not_delete->title)
                ->assertDontSee($task_for_delete->title);
        });
    }
}
