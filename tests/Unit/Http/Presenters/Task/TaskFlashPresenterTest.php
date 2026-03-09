<?php

namespace Tests\Unit\Http\Presenters\Task;

use App\Application\Task\DTO\TaskCommandOutput;
use App\Application\Task\Exceptions\TaskNotFoundException;
use App\Http\Presenters\Task\TaskFlashPresenter;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TaskFlashPresenterTest extends TestCase
{
    #[Test]
    public function itPresentsCommandOutput(): void
    {
        $presenter = new TaskFlashPresenter();
        $output = new TaskCommandOutput('success', '完了しました。');
        $flash = $presenter->presentCommand($output);

        $this->assertSame('success', $flash['flash_type']);
        $this->assertSame('完了しました。', $flash['flash_message']);
    }

    #[Test]
    public function itPresentsExceptionAsErrorFlash(): void
    {
        $presenter = new TaskFlashPresenter();
        $flash = $presenter->presentException(new TaskNotFoundException());

        $this->assertSame('error', $flash['flash_type']);
        $this->assertSame('指定されたタスクは存在しないか、既に削除されています。', $flash['flash_message']);
    }
}
