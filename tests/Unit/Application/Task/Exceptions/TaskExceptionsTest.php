<?php

namespace Tests\Unit\Application\Task\Exceptions;

use App\Application\Task\Exceptions\InvalidTaskStatusTransitionException;
use App\Application\Task\Exceptions\TaskApplicationException;
use App\Application\Task\Exceptions\TaskNotFoundException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TaskExceptionsTest extends TestCase
{
    #[Test]
    public function itReturnsDefaultPublicMessageFromBaseException(): void
    {
        $exception = new TaskApplicationException('internal error');

        $this->assertSame('処理中にエラーが発生しました。', $exception->getPublicMessage());
    }

    #[Test]
    public function itReturnsPublicMessageFromTaskNotFoundException(): void
    {
        $exception = new TaskNotFoundException();

        $this->assertSame(
            '指定されたタスクは存在しないか、既に削除されています。',
            $exception->getPublicMessage()
        );
    }

    #[Test]
    public function itReturnsPublicMessageFromInvalidStatusTransitionException(): void
    {
        $exception = new InvalidTaskStatusTransitionException();

        $this->assertSame('許可されていないステータス遷移です。', $exception->getPublicMessage());
    }
}
