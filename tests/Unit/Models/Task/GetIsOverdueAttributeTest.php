<?php

namespace Tests\Unit\Models\Task;

use App\Domain\Task\ValueObject\TaskStatus;
use App\Models\Task;
use Carbon\Carbon;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GetIsOverdueAttributeTest extends TestCase
{
    #[Test]
    #[DataProvider('overdueProvider')]
    public function itReturnsExpectedValueForOverdue(
        string $status,
        ?string $due_date,
        bool $expected
    ): void {
        $task = new Task();
        $task->status = $status;
        $task->due_date = $due_date !== null ? Carbon::parse($due_date) : null;

        $this->assertSame($expected, $task->is_overdue);
    }

    /**
     * @return array<string, array{string, string|null, bool}>
     */
    public static function overdueProvider(): array
    {
        // 固定の相対日付を使用してフレーキーテストを回避
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $tomorrow = date('Y-m-d', strtotime('+1 day'));

        return [
            'not_started with past due_date returns true' => [
                TaskStatus::NOT_STARTED->value,
                $yesterday,
                true,
            ],
            'in_progress with past due_date returns true' => [
                TaskStatus::IN_PROGRESS->value,
                $yesterday,
                true,
            ],
            'completed with past due_date returns false' => [
                TaskStatus::COMPLETED->value,
                $yesterday,
                false,
            ],
            'not_started with future due_date returns false' => [
                TaskStatus::NOT_STARTED->value,
                $tomorrow,
                false,
            ],
            'not_started with today due_date returns false' => [
                TaskStatus::NOT_STARTED->value,
                $today,
                false,
            ],
            'not_started with null due_date returns false' => [
                TaskStatus::NOT_STARTED->value,
                null,
                false,
            ],
        ];
    }
}
