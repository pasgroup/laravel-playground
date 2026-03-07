<?php

namespace Tests\Unit\Models\Task;

use App\Models\Task;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GetIsCompletedAttributeTest extends TestCase
{
    #[Test]
    #[DataProvider('statusProvider')]
    public function itReturnsExpectedValueForStatus(string $status, bool $expected): void
    {
        $task = new Task();
        $task->status = $status;

        $this->assertSame($expected, $task->is_completed);
    }

    /**
     * @return array<string, array{string, bool}>
     */
    public static function statusProvider(): array
    {
        return [
            'not_started' => [Task::STATUS_NOT_STARTED, false],
            'in_progress' => [Task::STATUS_IN_PROGRESS, false],
            'completed' => [Task::STATUS_COMPLETED, true],
        ];
    }
}
