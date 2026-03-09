<?php

namespace App\Http\Presenters\Task\ViewModel;

final class TaskStatusOptionViewModel
{
    public function __construct(
        public string $value,
        public string $label
    ) {
    }
}
