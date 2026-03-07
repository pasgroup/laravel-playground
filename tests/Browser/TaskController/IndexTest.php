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

    #[Test]
    public function itDisplaysTasksInCorrectOrderWithOverdueHighlight(): void
    {
        $not_completed = Task::factory()->notStarted()->create([
            'title' => '未完了タスク',
            'due_date' => now()->addDays(5),
        ]);
        $overdue = Task::factory()->notStarted()->create([
            'title' => '期限超過タスク',
            'due_date' => now()->subDays(2),
        ]);
        $completed = Task::factory()->completed()->create([
            'title' => '完了タスク',
            'due_date' => now()->subDay(),
        ]);

        $this->browse(function (Browser $browser) use ($not_completed, $overdue, $completed) {
            $browser->visit(route('tasks.index'));

            $browser->assertSeeIn('@index-task-title-' . $not_completed->task_id, $not_completed->title);
            $browser->assertSeeIn('@index-task-title-' . $overdue->task_id, $overdue->title);
            $browser->assertSeeIn('@index-task-title-' . $completed->task_id, $completed->title);

            $browser->assertPresent('@index-task-row-' . $overdue->task_id);
            $overdue_row = $browser->element('@index-task-row-' . $overdue->task_id);
            $this->assertStringContainsString('task-row-overdue', $overdue_row->getAttribute('class') ?? '');

            $browser->assertPresent('@index-task-row-' . $completed->task_id);
            $completed_row = $browser->element('@index-task-row-' . $completed->task_id);
            $this->assertStringNotContainsString('task-row-overdue', $completed_row->getAttribute('class') ?? '');

            $titles = $browser->elements('[dusk^="index-task-title-"]');
            $this->assertCount(3, $titles);

            $order = array_map(fn ($el) => $el->getAttribute('dusk'), $titles);
            $not_completed_dusk = 'index-task-title-' . $not_completed->task_id;
            $completed_dusk = 'index-task-title-' . $completed->task_id;
            $this->assertLessThan(
                array_search($completed_dusk, $order, true),
                array_search($not_completed_dusk, $order, true),
            );
        });
    }
}
