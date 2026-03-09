<?php

namespace App\Application\Task\Exceptions;

final class TaskNotFoundException extends TaskApplicationException
{
    private const PUBLIC_MESSAGE = '指定されたタスクは存在しないか、既に削除されています。';

    public function __construct()
    {
        parent::__construct(self::PUBLIC_MESSAGE);
    }

    public function getPublicMessage(): string
    {
        return self::PUBLIC_MESSAGE;
    }
}
