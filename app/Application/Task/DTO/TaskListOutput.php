<?php

namespace App\Application\Task\DTO;

use Illuminate\Database\Eloquent\Collection;

final class TaskListOutput
{
    public function __construct(
        public Collection $tasks
    ) {
    }
}
