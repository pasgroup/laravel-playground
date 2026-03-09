<?php

namespace Tests\Unit\Domain\Task;

use App\Domain\Task\Policy\TaskTransition;
use App\Domain\Task\Policy\TaskTransitionPolicyInterface;
use App\Domain\Task\Service\TaskStatusDecisionService;
use App\Domain\Task\ValueObject\TaskStatus;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TaskStatusDecisionServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function itResolvesValidTransitionToStatusPair(): void
    {
        $service = new TaskStatusDecisionService(new TaskTransition());
        $resolved = $service->resolveTransition(
            TaskStatus::NOT_STARTED->value,
            TaskStatus::IN_PROGRESS->value
        );

        $this->assertNotNull($resolved);
        $this->assertSame(TaskStatus::NOT_STARTED, $resolved[0]);
        $this->assertSame(TaskStatus::IN_PROGRESS, $resolved[1]);
    }

    #[Test]
    public function itReturnsNullWhenEitherStatusIsUnknown(): void
    {
        $service = new TaskStatusDecisionService(new TaskTransition());

        $this->assertNull($service->resolveTransition('unknown', TaskStatus::IN_PROGRESS->value));
        $this->assertNull($service->resolveTransition(TaskStatus::NOT_STARTED->value, 'unknown'));
    }

    #[Test]
    public function itResolvesStatusValue(): void
    {
        $service = new TaskStatusDecisionService(new TaskTransition());

        $this->assertSame(TaskStatus::COMPLETED, $service->resolveStatus(TaskStatus::COMPLETED->value));
        $this->assertNull($service->resolveStatus('unknown'));
    }

    #[Test]
    public function itReturnsNullWhenTransitionRuleRejectsStatusPair(): void
    {
        /** @var TaskTransitionPolicyInterface&\Mockery\MockInterface $task_transition */
        $task_transition = Mockery::mock(TaskTransitionPolicyInterface::class);
        $task_transition->shouldReceive('canTransition')
            ->once()
            ->with(TaskStatus::NOT_STARTED, TaskStatus::IN_PROGRESS)
            ->andReturn(false);

        $service = new TaskStatusDecisionService($task_transition);

        $this->assertNull(
            $service->resolveTransition(TaskStatus::NOT_STARTED->value, TaskStatus::IN_PROGRESS->value)
        );
    }
}
