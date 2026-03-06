<?php

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TaskFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = Task::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'task_uuid' => (string) Str::uuid(),
            'title' => 'テストタイトル',
            'detail' => 'テスト詳細',
            'due_date' => '2026-03-10',
            'status' => Task::STATUS_NOT_STARTED,
        ];
    }

    /**
     * 未着手のタスクを作成する
     *
     * @return self
     */
    public function notStarted(): self
    {
        return $this->state(fn () => ['status' => Task::STATUS_NOT_STARTED]);
    }

    /**
     * 進行中のタスクを作成する
     *
     * @return self
     */
    public function inProgress(): self
    {
        return $this->state(fn () => ['status' => Task::STATUS_IN_PROGRESS]);
    }

    /**
     * 完了のタスクを作成する
     *
     * @return self
     */
    public function completed(): self
    {
        return $this->state(fn () => ['status' => Task::STATUS_COMPLETED]);
    }
}
