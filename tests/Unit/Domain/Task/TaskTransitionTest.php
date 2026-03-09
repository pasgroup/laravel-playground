<?php

namespace Tests\Unit\Domain\Task;

use App\Domain\Task\Policy\TaskTransition;
use App\Domain\Task\ValueObject\TaskStatus;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TaskTransitionTest extends TestCase
{
    #[Test]
    public function itAllowsTransitionBetweenDefinedStatuses(): void
    {
        $task_transition = new TaskTransition();

        $this->assertTrue($task_transition->canTransition(TaskStatus::NOT_STARTED, TaskStatus::IN_PROGRESS));
        $this->assertTrue($task_transition->canTransition(TaskStatus::IN_PROGRESS, TaskStatus::COMPLETED));
        $this->assertTrue($task_transition->canTransition(TaskStatus::COMPLETED, TaskStatus::NOT_STARTED));

        $this->assertTrue($task_transition->canTransition(TaskStatus::NOT_STARTED, TaskStatus::NOT_STARTED));
        $this->assertTrue($task_transition->canTransition(TaskStatus::IN_PROGRESS, TaskStatus::IN_PROGRESS));
        $this->assertTrue($task_transition->canTransition(TaskStatus::COMPLETED, TaskStatus::COMPLETED));
    }

    #[Test]
    public function itAllowsBackwardOrSkippedTransitions(): void
    {
        $task_transition = new TaskTransition();

        $this->assertTrue($task_transition->canTransition(TaskStatus::NOT_STARTED, TaskStatus::COMPLETED));
        $this->assertTrue($task_transition->canTransition(TaskStatus::IN_PROGRESS, TaskStatus::NOT_STARTED));
        $this->assertTrue($task_transition->canTransition(TaskStatus::COMPLETED, TaskStatus::IN_PROGRESS));
    }

    #[Test]
    public function itRejectsUnknownStatuses(): void
    {
        $task_transition = new TaskTransition();

        $this->assertFalse($task_transition->canTransition('unknown', TaskStatus::IN_PROGRESS));
        $this->assertFalse($task_transition->canTransition(TaskStatus::IN_PROGRESS, 'unknown'));
    }
}
