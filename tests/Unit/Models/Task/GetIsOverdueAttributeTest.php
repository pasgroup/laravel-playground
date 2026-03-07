<?php

namespace Tests\Unit\Models\Task;

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
        $yesterday = now()->subDay()->format('Y-m-d');
        $tomorrow = now()->addDay()->format('Y-m-d');
        $today = now()->format('Y-m-d');

        return [
            'not_started with past due_date returns true' => [
                Task::STATUS_NOT_STARTED,
                $yesterday,
                true,
            ],
            'in_progress with past due_date returns true' => [
                Task::STATUS_IN_PROGRESS,
                $yesterday,
                true,
            ],
            'completed with past due_date returns false' => [
                Task::STATUS_COMPLETED,
                $yesterday,
                false,
            ],
            'not_started with future due_date returns false' => [
                Task::STATUS_NOT_STARTED,
                $tomorrow,
                false,
            ],
            'not_started with today due_date returns false' => [
                Task::STATUS_NOT_STARTED,
                $today,
                false,
            ],
            'not_started with null due_date returns false' => [
                Task::STATUS_NOT_STARTED,
                null,
                false,
            ],
        ];
    }
}
