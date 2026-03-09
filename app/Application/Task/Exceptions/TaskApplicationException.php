<?php

namespace App\Application\Task\Exceptions;

use RuntimeException;

class TaskApplicationException extends RuntimeException
{
    public function getPublicMessage(): string
    {
        return '処理中にエラーが発生しました。';
    }
}
