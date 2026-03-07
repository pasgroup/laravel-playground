<?php

namespace Tests\Browser\TaskController;

use App\Models\Task;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\Attributes\Test;
use Tests\DuskTestCase;

class StoreTest extends DuskTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate:fresh');
    }

    #[Test]
    public function itRegistersTaskAndShowsSuccessToast(): void
    {
        $task_title = 'Dusk登録テスト';

        $this->browse(function (Browser $browser) use ($task_title) {
            $browser->visit(route('tasks.create'))
                ->type('@create-title-input', $task_title)
                ->press('@create-submit-btn')
                ->waitFor('@toast-message', 5)
                ->assertSeeIn('@toast-message', 'タスクを登録しました。');

            $task = Task::where('title', $task_title)->first();
            $this->assertNotNull($task);

            $browser->assertSeeIn('@index-task-title-' . $task->task_id, $task_title);
        });
    }

    #[Test]
    public function itShowsValidationMessageWhenTitleIsEmpty(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(route('tasks.create'))
                ->clear('@create-title-input')
                ->press('@create-submit-btn')
                ->waitFor('@create-title-error', 3)
                ->assertRouteIs('tasks.create')
                ->assertPresent('@create-title-error');
        });
    }
}
