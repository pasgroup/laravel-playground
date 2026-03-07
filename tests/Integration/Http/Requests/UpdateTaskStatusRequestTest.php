<?php

namespace Tests\Integration\Http\Requests;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UpdateTaskStatusRequestTest extends TestCase
{
    use RefreshDatabase;

    private const TASK_UUID = '11111111-1111-1111-1111-111111111111';
    private const SOFT_DELETED_TASK_UUID = '22222222-2222-2222-2222-222222222222';
    private const NOT_FOUND_UUID = '00000000-0000-0000-0000-000000000000';

    public function setUp(): void
    {
        parent::setUp();

        Task::factory()->notStarted()->create([
            'task_uuid' => self::TASK_UUID,
        ]);

        Task::factory()->notStarted()->create([
            'task_uuid' => self::SOFT_DELETED_TASK_UUID,
            'deleted_at' => now(),
        ]);
    }

    /**
     * マージ・リダイレクト・セッションを含むPOSTの結合テスト
     *
     * @param string $task_uuid
     * @param array<string, mixed> $post_data
     * @param string $expected_session_key
     * @param string $expected_session_message
     * @param string|null $expected_status 更新成功時のみDBのstatusを検証する場合に指定
     */
    #[Test]
    #[DataProvider('updateStatusScenariosProvider')]
    public function itHandlesUpdateStatusRequestWithRedirectAndSession(
        string $task_uuid,
        array $post_data,
        string $expected_session_key,
        string $expected_session_message,
        ?string $expected_status = null
    ): void {
        $response = $this->post(
            route('tasks.status.update', ['task_uuid' => $task_uuid]),
            $post_data
        );

        $response->assertRedirect(route('tasks.index'));
        $response->assertSessionHas($expected_session_key, $expected_session_message);

        if ($expected_status !== null) {
            $task = Task::where('task_uuid', $task_uuid)->first();
            $this->assertNotNull($task);
            $this->assertSame($expected_status, $task->status);
        }
    }

    /**
     * @return array<string, array{string, array<string, mixed>, string, string, string|null}>
     */
    public static function updateStatusScenariosProvider(): array
    {
        return [
            'updates to not started' => [
                self::TASK_UUID,
                ['status' => Task::STATUS_NOT_STARTED],
                'success',
                'タスクのステータスを更新しました。',
                Task::STATUS_NOT_STARTED,
            ],
            'updates to in progress' => [
                self::TASK_UUID,
                ['status' => Task::STATUS_IN_PROGRESS],
                'success',
                'タスクのステータスを更新しました。',
                Task::STATUS_IN_PROGRESS,
            ],
            'updates to completed' => [
                self::TASK_UUID,
                ['status' => Task::STATUS_COMPLETED],
                'success',
                'タスクのステータスを更新しました。',
                Task::STATUS_COMPLETED,
            ],
            'invalid status value' => [
                self::TASK_UUID,
                ['status' => 'invalid_status'],
                'error',
                'ステータスは未着手・進行中・完了のいずれかを指定してください。',
                null,
            ],
            'status not specified' => [
                self::TASK_UUID,
                ['status' => ''],
                'error',
                'ステータスを指定してください。',
                null,
            ],
            'redirects with error when status is not string' => [
                self::TASK_UUID,
                ['status' => [123]],
                'error',
                'ステータスの形式が不正です。',
                null,
            ],
            'redirects with error when task uuid does not exist' => [
                self::NOT_FOUND_UUID,
                ['status' => Task::STATUS_NOT_STARTED],
                'error',
                '指定されたタスクは存在しないか、既に削除されています。',
                null,
            ],
            'redirects with error when task uuid format is invalid' => [
                'invalid-task-uuid',
                ['status' => Task::STATUS_NOT_STARTED],
                'error',
                'タスクの指定が不正です。',
                null,
            ],
            'redirects with error when task is soft deleted' => [
                self::SOFT_DELETED_TASK_UUID,
                ['status' => Task::STATUS_IN_PROGRESS],
                'error',
                '指定されたタスクは存在しないか、既に削除されています。',
                null,
            ],
        ];
    }
}
