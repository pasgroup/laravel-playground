<?php

namespace Tests\Integration\Http\Requests;

use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DestroyTaskRequestTest extends TestCase
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
     * マージ・リダイレクト・セッションを含むDELETEの結合テスト
     *
     * @param string $task_uuid
     * @param string $expected_session_key
     * @param string $expected_session_message
     * @param bool $assert_soft_deleted
     */
    #[Test]
    #[DataProvider('destroyScenariosProvider')]
    public function itHandlesDestroyRequestWithRedirectAndSession(
        string $task_uuid,
        string $expected_session_key,
        string $expected_session_message,
        bool $assert_soft_deleted
    ): void {
        $response = $this->delete(route('tasks.destroy', ['task_uuid' => $task_uuid]));

        $response->assertRedirect(route('tasks.index'));
        $response->assertSessionHas($expected_session_key, $expected_session_message);

        if ($assert_soft_deleted) {
            $this->assertSoftDeleted('tasks', ['task_uuid' => $task_uuid]);
        }
    }

    /**
     * @return array<string, array{string, string, string, bool}>
     */
    public static function destroyScenariosProvider(): array
    {
        return [
            'deletes successfully when uuid is valid' => [
                self::TASK_UUID,
                'success',
                'タスクを削除しました。',
                true,
            ],
            'redirects with error when uuid format is invalid' => [
                'invalid-uuid',
                'error',
                'タスクの指定が不正です。',
                false,
            ],
            'redirects with error when task uuid does not exist' => [
                self::NOT_FOUND_UUID,
                'error',
                '指定されたタスクは存在しないか、既に削除されています。',
                false,
            ],
            'redirects with error when task is soft deleted' => [
                self::SOFT_DELETED_TASK_UUID,
                'error',
                '指定されたタスクは存在しないか、既に削除されています。',
                false,
            ],
        ];
    }
}
