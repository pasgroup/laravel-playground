<?php

namespace App\Application\Task\DTO;

final class CreateTaskInput
{
    public function __construct(
        public string $title,
        public ?string $detail = null,
        public ?string $due_date = null
    ) {
    }
}
