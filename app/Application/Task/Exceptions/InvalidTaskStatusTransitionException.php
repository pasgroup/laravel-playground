<?php

namespace App\Application\Task\Exceptions;

final class InvalidTaskStatusTransitionException extends TaskApplicationException
{
    private const PUBLIC_MESSAGE = '許可されていないステータス遷移です。';

    public function __construct()
    {
        parent::__construct(self::PUBLIC_MESSAGE);
    }

    public function getPublicMessage(): string
    {
        return self::PUBLIC_MESSAGE;
    }
}
