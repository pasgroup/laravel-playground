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
        $tasks = [
            [
                'title' => '作成日の3日後のタスク',
                'detail' => '未着手のタスクです。',
                'due_date' => now()->addDays(3),
                'status' => Task::STATUS_NOT_STARTED,
            ],
            [
                'title' => '作成日の1日後のタスク',
                'detail' => '進行中のタスクです。',
                'due_date' => now()->addDay(),
                'status' => Task::STATUS_IN_PROGRESS,
            ],
            [
                'title' => '作成日の7日後のタスク',
                'detail' => '期限が最も遠いタスクです。',
                'due_date' => now()->addDays(7),
                'status' => Task::STATUS_NOT_STARTED,
            ],
            [
                'title' => '作成日の1日前のタスク',
                'detail' => '完了のタスクです。',
                'due_date' => now()->subDay(),
                'status' => Task::STATUS_COMPLETED,
            ],
        ];

        foreach ($tasks as $task) {
            Task::create($task);
        }
    }
}
