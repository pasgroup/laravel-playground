<?php

namespace Tests\Unit\Models\Task;

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
            '未着手' => [
                Task::STATUS_NOT_STARTED,
                '未着手',
            ],
            '進行中' => [
                Task::STATUS_IN_PROGRESS,
                '進行中',
            ],
            '完了' => [
                Task::STATUS_COMPLETED,
                '完了',
            ],
            '未定義' => [
                'unknown_status',
                '未設定',
            ],
        ];
    }
}
