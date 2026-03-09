<?php

namespace Tests\Unit\Http\Presenters\Task\ViewModel;

use App\Http\Presenters\Task\ViewModel\TaskIndexItemViewModel;
use App\Http\Presenters\Task\ViewModel\TaskIndexViewModel;
use App\Http\Presenters\Task\ViewModel\TaskStatusOptionViewModel;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TaskIndexViewModelTest extends TestCase
{
    #[Test]
    public function itReturnsFalseWhenTasksAreEmpty(): void
    {
        $view_model = new TaskIndexViewModel(
            [],
            [new TaskStatusOptionViewModel('not_started', '未着手')],
            null,
            null
        );

        $this->assertFalse($view_model->hasTasks());
    }

    #[Test]
    public function itReturnsTrueWhenTasksExist(): void
    {
        $view_model = new TaskIndexViewModel(
            [
                new TaskIndexItemViewModel(
                    task_id: 1,
                    task_uuid: '11111111-1111-1111-1111-111111111111',
                    title: 'task',
                    detail: null,
                    due_date_text: '—',
                    status: 'not_started',
                    is_overdue: false,
                    is_first_completed: false
                ),
            ],
            [new TaskStatusOptionViewModel('not_started', '未着手')],
            null,
            null
        );

        $this->assertTrue($view_model->hasTasks());
    }
}
