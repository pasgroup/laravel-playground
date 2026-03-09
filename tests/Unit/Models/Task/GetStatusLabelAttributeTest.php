<?php

namespace Tests\Unit\Models\Task;

use App\Domain\Task\TaskStatus;
use App\Models\Task;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GetStatusLabelAttributeTest extends TestCase
{
    #[Test]
    #[DataProvider('statusLabelProvider')]
    public function itReturnsExpectedLabelForStatus(string $status, string $expected_label): void
    {
        $task = new Task();
        $task->status = $status;

        $this->assertSame($expected_label, $task->status_label);
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function statusLabelProvider(): array
    {
        return [
            'not_started' => [
                TaskStatus::NOT_STARTED->value,
                '未着手',
            ],
            'in_progress' => [
                TaskStatus::IN_PROGRESS->value,
                '進行中',
            ],
            'completed' => [
                TaskStatus::COMPLETED->value,
                '完了',
            ],
            'undefined' => [
                'unknown_status',
                '未設定',
            ],
        ];
    }
}
