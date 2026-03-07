<?php

namespace Database\Seeders;

use App\Models\Task;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $base_date = today();

        // 未着手・期限未来
        Task::factory()->notStarted()->create([
            'title' => '作成日の3日後のタスク',
            'detail' => '未着手のタスクです。',
            'due_date' => $base_date->copy()->addDays(3),
        ]);
        Task::factory()->notStarted()->create([
            'title' => '作成日の1日後のタスク（未着手）',
            'detail' => '明日が期限の未着手です。',
            'due_date' => $base_date->copy()->addDay(),
        ]);
        Task::factory()->notStarted()->create([
            'title' => '作成日の7日後のタスク',
            'detail' => '期限が最も遠いタスクです。',
            'due_date' => $base_date->copy()->addDays(7),
        ]);

        // 未着手・期限超過（一覧で赤表示）
        Task::factory()->notStarted()->create([
            'title' => '作成日の1日前のタスク（未着手・期限超過・境界値）',
            'detail' => '未完了で期限を過ぎたタスクです。',
            'due_date' => $base_date->copy()->subDay(),
        ]);
        Task::factory()->notStarted()->create([
            'title' => '作成日の3日前のタスク（未着手・期限超過）',
            'detail' => '未着手のまま期限超過です。',
            'due_date' => $base_date->copy()->subDays(3),
        ]);

        // 未着手・期限なし
        Task::factory()->notStarted()->create([
            'title' => '期限なしの未着手タスク',
            'detail' => '期限が未設定の未着手です。',
            'due_date' => null,
        ]);

        // 進行中・期限未来
        Task::factory()->inProgress()->create([
            'title' => '作成日の2日後のタスク（進行中）',
            'detail' => '進行中のタスクです。',
            'due_date' => $base_date->copy()->addDays(2),
        ]);

        // 進行中・期限超過（一覧で赤表示）
        Task::factory()->inProgress()->create([
            'title' => '作成日の1日前のタスク（進行中・期限超過）',
            'detail' => '進行中のまま期限を過ぎたタスクです。',
            'due_date' => $base_date->copy()->subDay(),
        ]);

        // 進行中・期限なし
        Task::factory()->inProgress()->create([
            'title' => '期限なしの進行中タスク',
            'detail' => '期限が未設定の進行中です。',
            'due_date' => null,
        ]);

        // 完了・期限過去（一覧では赤にならない）
        Task::factory()->completed()->create([
            'title' => '作成日の1日前のタスク（完了）',
            'detail' => '期限超過後に完了したタスクです。',
            'due_date' => $base_date->copy()->subDay(),
        ]);

        // 完了・期限未来
        Task::factory()->completed()->create([
            'title' => '作成日の7日後のタスク（完了）',
            'detail' => '期限前に完了したタスクです。',
            'due_date' => $base_date->copy()->addDays(7),
        ]);

        // 完了・期限なし
        Task::factory()->completed()->create([
            'title' => '期限なしの完了タスク',
            'detail' => '期限未設定で完了したタスクです。',
            'due_date' => null,
        ]);

        // 境界：今日が期限
        Task::factory()->notStarted()->create([
            'title' => '今日が期限のタスク',
            'detail' => '期限日が今日のタスクです。',
            'due_date' => $base_date->copy(),
        ]);
    }
}
