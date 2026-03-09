<?php

namespace Tests\Unit\Domain\Task;

use App\Domain\Task\TaskDeadline;
use App\Domain\Task\TaskStatus;
use Carbon\Carbon;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TaskDeadlineTest extends TestCase
{
    #[Test]
    #[DataProvider('isOverdueProvider')]
    public function itDeterminesOverdueByDomainRule(TaskStatus|string $status, ?string $due_date, bool $expected): void
    {
        $task_deadline = new TaskDeadline();

        Carbon::setTestNow('2026-03-10 12:00:00');
        $this->assertSame($expected, $task_deadline->isOverdue($status, $due_date));
        Carbon::setTestNow();
    }

    /**
     * @return array<string, array{TaskStatus|string, string|null, bool}>
     */
    public static function isOverdueProvider(): array
    {
        return [
            'past and not completed' => [TaskStatus::NOT_STARTED, '2026-03-09', true],
            'today and not completed' => [TaskStatus::NOT_STARTED, '2026-03-10', false],
            'future and not completed' => [TaskStatus::IN_PROGRESS, '2026-03-11', false],
            'past but completed' => [TaskStatus::COMPLETED, '2026-03-09', false],
            'null due date' => [TaskStatus::NOT_STARTED, null, false],
        ];
    }
}
