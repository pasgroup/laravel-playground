<?php

namespace Tests\Unit\Http\Presenters\Task;

use App\Application\Task\DTO\TaskListOutput;
use App\Domain\Task\Entity\TaskEntity;
use App\Domain\Task\ValueObject\TaskStatus;
use App\Http\Presenters\Task\TaskIndexPresenter;
use Carbon\CarbonImmutable;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TaskIndexPresenterTest extends TestCase
{
    #[Test]
    public function itBuildsViewModelForIndex(): void
    {
        $presenter = new TaskIndexPresenter();
        $output = new TaskListOutput([
            new TaskEntity(
                task_id: 1,
                task_uuid: '11111111-1111-1111-1111-111111111111',
                title: '未完了タスク',
                detail: '詳細1',
                due_date: CarbonImmutable::parse('2026-03-10'),
                status: TaskStatus::IN_PROGRESS->value,
            ),
            new TaskEntity(
                task_id: 2,
                task_uuid: '22222222-2222-2222-2222-222222222222',
                title: '完了タスク',
                detail: null,
                due_date: null,
                status: TaskStatus::COMPLETED->value,
            ),
        ]);

        $view_model = $presenter->present($output, '成功', '失敗');

        $this->assertCount(2, $view_model->tasks);
        $this->assertCount(3, $view_model->status_options);
        $this->assertSame('2026-03-10', $view_model->tasks[0]->due_date_text);
        $this->assertSame('—', $view_model->tasks[1]->due_date_text);
        $this->assertTrue($view_model->tasks[1]->is_first_completed);
        $this->assertSame('成功', $view_model->success_message);
        $this->assertSame('失敗', $view_model->error_message);
    }

    #[Test]
    public function itBuildsEmptyTaskViewModel(): void
    {
        $presenter = new TaskIndexPresenter();
        $output = new TaskListOutput([]);
        $view_model = $presenter->present($output, null, null);

        $this->assertCount(0, $view_model->tasks);
        $this->assertFalse($view_model->hasTasks());
        $this->assertCount(3, $view_model->status_options);
    }

    #[Test]
    public function itDoesNotMarkFirstRowAsBoundaryWhenFirstTaskIsCompleted(): void
    {
        $presenter = new TaskIndexPresenter();
        $output = new TaskListOutput([
            new TaskEntity(
                task_id: 1,
                task_uuid: '11111111-1111-1111-1111-111111111111',
                title: '先頭完了タスク',
                detail: null,
                due_date: CarbonImmutable::parse('2026-03-10'),
                status: TaskStatus::COMPLETED->value,
            ),
        ]);
        $view_model = $presenter->present($output, null, null);

        $this->assertCount(1, $view_model->tasks);
        $this->assertFalse($view_model->tasks[0]->is_first_completed);
    }
}
