<?php

namespace Tests\Unit\Http\Presenters\Task\Formatter;

use App\Domain\Task\Entity\TaskEntity;
use App\Domain\Task\ValueObject\TaskStatus;
use App\Http\Presenters\Task\Formatter\TaskIndexItemFormatter;
use Carbon\CarbonImmutable;
use LogicException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TaskIndexItemFormatterTest extends TestCase
{
    #[Test]
    public function itThrowsWhenTaskIdIsNull(): void
    {
        $this->expectException(LogicException::class);
        $formatter = new TaskIndexItemFormatter();

        $formatter->toViewModel(
            new TaskEntity(
                task_id: null,
                task_uuid: '11111111-1111-1111-1111-111111111111',
                title: 'task',
                detail: null,
                due_date: CarbonImmutable::parse('2026-03-10'),
                status: TaskStatus::NOT_STARTED->value
            ),
            null
        );
    }
}
