<?php

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\StoreTaskRequest;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class StoreTaskRequestTest extends TestCase
{
    /**
     * Setup
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        // dataProviderで使用するために、タスクの最大文字数を固定
        Config::set('app.task_title_max', 30);
        Config::set('app.task_detail_max', 1000);
    }

    #[Test]
    #[DataProvider('validDataProvider')]
    public function validDataPassesValidation(array $data): void
    {
        $request = new StoreTaskRequest();
        $validator = Validator::make($data, $request->rules(), $request->messages());

        $this->assertFalse($validator->fails());
    }

    /**
     * @return array<string, array{array}>
     */
    public static function validDataProvider(): array
    {
        return [
            'title only' => [
                [
                    'title' => 'テストタスク',
                ],
            ],
            'title, detail and due_date' => [
                [
                    'title' => 'テストタスク',
                    'detail' => '詳細メモ',
                    'due_date' => '2026-03-10',
                ],
            ],
            'detail empty string' => [
                [
                    'title' => 'タイトル',
                    'detail' => '',
                ],
            ],
        ];
    }

    #[Test]
    #[DataProvider('invalidDataProvider')]
    public function invalidDataFailsValidationWithExpectedMessage(
        array $data,
        string $attribute,
        string $expected_message
    ): void {
        $request = new StoreTaskRequest();
        $validator = Validator::make($data, $request->rules(), $request->messages());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey($attribute, $validator->errors()->toArray());
        $this->assertSame($expected_message, $validator->errors()->first($attribute));
    }


    /**
     * @return array<string, array{array, string, string}>
     */
    public static function invalidDataProvider(): array
    {
        return [
            'title empty' => [
                [
                    'title' => '',
                ],
                'title',
                'タイトルを入力してください。',
            ],
            'title exceeds 30 characters' => [
                [
                    'title' => str_repeat('a', 31),
                ],
                'title',
                'タイトルは30文字以内で入力してください。',
            ],
            'detail exceeds 1000 characters' => [
                [
                    'title' => 'タイトル',
                    'detail' => str_repeat('a', 1001),
                ],
                'detail',
                '詳細は1000文字以内で入力してください。',
            ],
            'invalid date' => [
                [
                    'title' => 'タイトル',
                    'due_date' => 'invalid-date',
                ],
                'due_date',
                '期限日は有効な日付で入力してください。',
            ],
        ];
    }
}
