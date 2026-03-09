<?php

namespace Tests\Unit\Domain\Task;

use App\Domain\Task\TaskStatus;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TaskStatusTest extends TestCase
{
    #[Test]
    #[DataProvider('labelProvider')]
    public function itReturnsStatusLabel(TaskStatus $status, string $expected): void
    {
        $this->assertSame($expected, $status->label());
    }

    #[Test]
    #[DataProvider('completedProvider')]
    public function itReturnsExpectedCompletedState(TaskStatus $status, bool $expected): void
    {
        $this->assertSame($expected, $status->isCompleted());
    }

    /**
     * @return array<string, array{TaskStatus, string}>
     */
    public static function labelProvider(): array
    {
        return [
            'not started' => [TaskStatus::NOT_STARTED, '未着手'],
            'in progress' => [TaskStatus::IN_PROGRESS, '進行中'],
            'completed' => [TaskStatus::COMPLETED, '完了'],
        ];
    }

    /**
     * @return array<string, array{TaskStatus, bool}>
     */
    public static function completedProvider(): array
    {
        return [
            'not started' => [TaskStatus::NOT_STARTED, false],
            'in progress' => [TaskStatus::IN_PROGRESS, false],
            'completed' => [TaskStatus::COMPLETED, true],
        ];
    }
}
