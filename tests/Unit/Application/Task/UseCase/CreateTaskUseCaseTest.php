<?php

namespace Tests\Unit\Application\Task\UseCase;

use App\Application\Task\DTO\CreateTaskInput;
use App\Application\Task\Repository\TaskRepositoryInterface;
use App\Application\Task\UseCase\CreateTaskUseCase;
use App\Domain\Task\Entity\TaskEntity;
use App\Domain\Task\Service\TaskCreationService;
use App\Domain\Task\ValueObject\TaskStatus;
use Carbon\CarbonImmutable;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CreateTaskUseCaseTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function itCreatesTaskAndReturnsSuccessMessage(): void
    {
        /** @var TaskRepositoryInterface&\Mockery\MockInterface $task_repository */
        $task_repository = Mockery::mock(TaskRepositoryInterface::class);
        $task_repository->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function (TaskEntity $task_entity): bool {
                return $task_entity->title === 'ユースケース作成テスト'
                    && $task_entity->detail === '詳細'
                    && $task_entity->due_date?->format('Y-m-d') === '2026-03-31'
                    && $task_entity->status === TaskStatus::NOT_STARTED->value;
            }))
            ->andReturn(
                new TaskEntity(
                    task_id: 123,
                    task_uuid: '11111111-1111-1111-1111-111111111111',
                    title: 'ユースケース作成テスト',
                    detail: '詳細',
                    due_date: CarbonImmutable::parse('2026-03-31'),
                    status: TaskStatus::NOT_STARTED->value
                )
            );

        $use_case = new CreateTaskUseCase($task_repository, new TaskCreationService());
        $output = $use_case->handle(
            new CreateTaskInput(
                'ユースケース作成テスト',
                '詳細',
                '2026-03-31'
            )
        );

        $this->assertSame('success', $output->flash_type);
        $this->assertSame('タスクを登録しました。', $output->flash_message);
        $this->assertSame(123, $output->task_id);
    }
}
