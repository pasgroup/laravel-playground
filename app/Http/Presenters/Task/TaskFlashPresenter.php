<?php

namespace App\Http\Presenters\Task;

use App\Application\Task\DTO\TaskCommandOutput;
use App\Application\Task\Exceptions\TaskApplicationException;

final class TaskFlashPresenter
{
    /**
     * @return array{flash_type: string, flash_message: string}
     */
    public function presentCommand(TaskCommandOutput $output): array
    {
        return [
            'flash_type' => $output->flash_type,
            'flash_message' => $output->flash_message,
        ];
    }

    /**
     * @return array{flash_type: string, flash_message: string}
     */
    public function presentException(TaskApplicationException $exception): array
    {
        return [
            'flash_type' => 'error',
            'flash_message' => $exception->getPublicMessage(),
        ];
    }
}
