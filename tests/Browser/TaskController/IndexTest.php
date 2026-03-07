<?php

namespace Tests\Browser\TaskController;

use App\Models\Task;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\Test;
use Tests\DuskTestCase;

class IndexTest extends DuskTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh');
    }

    #[Test]
    public function itDisplaysIndexPageWithHeadingAndCreateLink(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('tasks.index'))
                ->assertPresent('@index-heading')
                ->assertSeeIn('@index-heading', 'タスク一覧')
                ->assertPresent('@index-create-link')
                ->assertPresent('@index-list-heading');
        });
    }

    #[Test]
    public function itDisplaysEmptyMessageWhenNoTasks(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('tasks.index'))
                ->assertPresent('@index-empty-message');
        });
    }

    #[Test]
    public function itDisplaysTaskTableWhenTasksExist(): void
    {
        $task = Task::factory()->notStarted()->create([
            'title' => 'Dusk表示確認タスク',
        ]);

        $this->browse(function (Browser $browser) use ($task) {
            $browser->visit(route('tasks.index'))
                ->assertPresent('@index-task-table-wrap')
                ->assertPresent('@index-task-table')
                ->assertSeeIn('@index-task-title-' . $task->task_id, $task->title)
                ->assertPresent('@index-status-select-' . $task->task_id)
                ->assertPresent('@index-delete-btn-' . $task->task_id);
        });
    }
}
