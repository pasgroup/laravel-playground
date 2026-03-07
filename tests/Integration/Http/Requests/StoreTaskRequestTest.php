<?php

namespace Tests\Integration\Http\Requests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class StoreTaskRequestTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        Config::set('app.task_title_max', 30);
        Config::set('app.task_detail_max', 1000);
    }

    /**
     * マージ・リダイレクト・セッションを含むPOSTの結合テスト
     *
     * @param array<string, mixed> $post_data
     * @param string $expected_redirect_route
     * @param string|null $expected_session_key
     * @param string|null $expected_session_message
     * @param string|null $expected_error_attribute
     * @param string|null $expected_error_message
     */
    #[Test]
    #[DataProvider('storeScenariosProvider')]
    public function itHandlesStoreRequestWithRedirectAndSession(
        array $post_data,
        string $expected_redirect_route,
        ?string $expected_session_key,
        ?string $expected_session_message,
        ?string $expected_error_attribute = null,
        ?string $expected_error_message = null
    ): void {
        $response = $this->from(route('tasks.create'))
            ->post(route('tasks.store'), $post_data);

        $response->assertRedirect(route($expected_redirect_route));

        if ($expected_session_key !== null && $expected_session_message !== null) {
            $response->assertSessionHas($expected_session_key, $expected_session_message);
        }

        if ($expected_error_attribute === null && $expected_error_message === null) {
            $this->assertDatabaseHas('tasks', [
                'title' => $post_data['title'],
            ]);
        }

        if ($expected_error_attribute !== null && $expected_error_message !== null) {
            $response->assertSessionHasErrors($expected_error_attribute);

            $errors = app('session.store')->get('errors');
            $this->assertSame(
                $expected_error_message,
                $errors->getBag('default')->first($expected_error_attribute)
            );
        }
    }

    /**
     * @return array<string, array{array<string, mixed>, string, string|null, string|null, string|null, string|null}>
     */
    public static function storeScenariosProvider(): array
    {
        return [
            'registers successfully with title only' => [
                ['title' => 'テストタスク'],
                'tasks.index',
                'success',
                'タスクを登録しました。',
                null,
                null,
            ],
            'registers successfully with title max length' => [
                ['title' => str_repeat('a', 30)],
                'tasks.index',
                'success',
                'タスクを登録しました。',
                null,
                null,
            ],
            'registers successfully with title detail and due date' => [
                [
                    'title' => 'テストタスク',
                    'detail' => '詳細メモ',
                    'due_date' => '2026-03-10',
                ],
                'tasks.index',
                'success',
                'タスクを登録しました。',
                null,
                null,
            ],
            'fails when title is empty' => [
                ['title' => ''],
                'tasks.create',
                null,
                null,
                'title',
                'タイトルを入力してください。',
            ],
            'fails when title exceeds 30 characters' => [
                ['title' => str_repeat('a', 31)],
                'tasks.create',
                null,
                null,
                'title',
                'タイトルは30文字以内で入力してください。',
            ],
            'fails when detail exceeds 1000 characters' => [
                [
                    'title' => 'タイトル',
                    'detail' => str_repeat('a', 1001),
                ],
                'tasks.create',
                null,
                null,
                'detail',
                '詳細は1000文字以内で入力してください。',
            ],
            'fails when date is invalid' => [
                [
                    'title' => 'タイトル',
                    'due_date' => 'invalid-date',
                ],
                'tasks.create',
                null,
                null,
                'due_date',
                '期限日は有効な日付で入力してください。',
            ],
        ];
    }
}
