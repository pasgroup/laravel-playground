<?php

namespace Tests\Integration\Http\Requests;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DestroyTaskRequestIntegrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 不正なUUIDの場合にエラーメッセージがセッションに格納され、一覧ページにリダイレクトされるか検証
     */
    #[Test]
    public function itRedirectsToIndexWithErrorWhenTaskUuidFormatIsInvalid(): void
    {
        $response = $this->delete(route('tasks.destroy', ['task_uuid' => 'invalid-uuid']));

        $response->assertRedirect(route('tasks.index'));
        $response->assertSessionHas('error', 'タスクの指定が不正です。');
    }

    /**
     * 有効なタスクUUIDの場合にルートパラメータがマージされているか検証
     */
    #[Test]
    public function itMergesRouteParametersWhenTaskUuidIsValid(): void
    {
        $task = Task::factory()->notStarted()->create();

        $response = $this->delete(route('tasks.destroy', ['task_uuid' => $task->task_uuid]));

        $response->assertRedirect(route('tasks.index'));
        $response->assertSessionHas('success', 'タスクを削除しました。');
        $this->assertSoftDeleted('tasks', ['task_id' => $task->task_id]);
    }
}
