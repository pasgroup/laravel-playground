<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\View\View;

class TaskController extends Controller
{
    /**
     * コンストラクタ
     */
    public function __construct(
        protected Task $task
    ) {
    }

    /**
     * タスク一覧を表示
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        // タスクを期限日順に取得
        $tasks = $this->task->getTaskOrderByDueDate();

        return view('tasks.index', compact('tasks'));
    }
}
