<?php

namespace App\Application\Task\Exceptions;

final class InvalidTaskStatusTransitionException extends TaskApplicationException
{
    public function __construct()
    {
        parent::__construct('許可されていないステータス遷移です。');
    }
}
