<?php

namespace App\Application\Task\Exceptions;

final class TaskNotFoundException extends TaskApplicationException
{
    public function __construct()
    {
        parent::__construct('指定されたタスクは存在しないか、既に削除されています。');
    }
}
