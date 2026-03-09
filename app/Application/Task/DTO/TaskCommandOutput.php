<?php

namespace App\Application\Task\DTO;

final class TaskCommandOutput
{
    public function __construct(
        public string $flash_type,
        public string $flash_message
    ) {
    }
}
