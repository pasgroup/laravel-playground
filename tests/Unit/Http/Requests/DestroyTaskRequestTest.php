<?php

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\DestroyTaskRequest;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DestroyTaskRequestTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function itPassesValidationWhenTaskUuidIsValid(): void
    {
        $task = Task::factory()->notStarted()->create();

        $request = new DestroyTaskRequest();

        $validator = Validator::make(
            ['task_uuid' => $task->task_uuid],
            $request->rules(),
            $request->messages()
        );

        $this->assertFalse($validator->fails());
    }

    #[Test]
    #[DataProvider('invalidFormatProvider')]
    public function itFailsValidationWhenFormatIsInvalid(?string $taskUuid, string $expectedMessage): void
    {
        $request = new DestroyTaskRequest();

        $validator = Validator::make(
            ['task_uuid' => $taskUuid],
            $request->rules(),
            $request->messages()
        );

        $this->assertTrue($validator->fails());
        $this->assertEquals(
            $expectedMessage,
            $validator->errors()->first('task_uuid')
        );
    }

    public static function invalidFormatProvider(): array
    {
        return [
            'missing uuid' => [
                null,
                'タスクを指定してください。',
            ],
            'not uuid format' => [
                'invalid',
                'タスクの指定が不正です。',
            ],
        ];
    }

    #[Test]
    public function itFailsValidationWhenTaskUuidDoesNotExist(): void
    {
        $request = new DestroyTaskRequest();

        $validator = Validator::make(
            ['task_uuid' => (string) Str::uuid()],
            $request->rules(),
            $request->messages()
        );

        $this->assertTrue($validator->fails());
        $this->assertEquals(
            '指定されたタスクは存在しないか、既に削除されています。',
            $validator->errors()->first('task_uuid')
        );
    }

    #[Test]
    public function itFailsValidationWhenTaskIsSoftDeleted(): void
    {
        $task = Task::factory()->notStarted()->create();
        $task->delete();

        $request = new DestroyTaskRequest();

        $validator = Validator::make(
            ['task_uuid' => $task->task_uuid],
            $request->rules(),
            $request->messages()
        );

        $this->assertTrue($validator->fails());
        $this->assertEquals(
            '指定されたタスクは存在しないか、既に削除されています。',
            $validator->errors()->first('task_uuid')
        );
    }
}
