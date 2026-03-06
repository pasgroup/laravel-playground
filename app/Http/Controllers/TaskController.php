<?php

namespace App\Http\Controllers;

use App\Http\Requests\DestroyTaskRequest;
use App\Http\Requests\StoreTaskRequest;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        // タスクを期限日順に取得
        $tasks = $this->task->getTaskOrderByDueDate();

        return view('tasks.index', [
            'tasks' => $tasks,
            'success_message' => $request->session()->get('success'),
        ]);
    }

    /**
     * タスク追加ページを表示
     *
     * @return View
     */
    public function create(): View
    {
        return view('tasks.create');
    }

    /**
     * 新規タスクの登録
     *
     * @param StoreTaskRequest $request
     * @return RedirectResponse
     */
    public function store(StoreTaskRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $this->task->createStatusNotStartedTask(
            $validated['title'],
            $validated['detail'] ?? null,
            $validated['due_date'] ?? null
        );

        return redirect()->route('tasks.index')->with('success', 'タスクを登録しました。');
    }

    /**
     * タスクを削除する
     *
     * @param DestroyTaskRequest $request
     * @return RedirectResponse
     */
    public function destroy(DestroyTaskRequest $request): RedirectResponse
    {
        // タスクを削除
        $this->task->deleteByUuid($request->validated('task_uuid'));

        return redirect()->route('tasks.index')->with('success', 'タスクを削除しました。');
    }
}
