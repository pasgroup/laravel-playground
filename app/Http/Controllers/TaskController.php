<?php

namespace App\Http\Controllers;

use App\Domain\Task\TaskStatus;
use App\Domain\Task\TaskTransition;
use App\Http\Requests\DestroyTaskRequest;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskStatusRequest;
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
        protected Task $task,
        protected TaskTransition $task_transition
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

        $this->task->newQuery()->create([
            'title' => $validated['title'],
            'detail' => $validated['detail'] ?? null,
            'due_date' => $validated['due_date'] ?? null,
            'status' => TaskStatus::NOT_STARTED->value,
        ]);

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
        // UUIDで該当タスクを削除
        $deleted = $this->task->deleteByUuid($request->validated('task_uuid'));

        $type = $deleted ? 'success' : 'error';
        $message = $deleted ? 'タスクを削除しました。' : '指定されたタスクは存在しないか、既に削除されています。';

        return redirect()->route('tasks.index')->with($type, $message);
    }

    /**
     * タスクのステータスを更新する
     *
     * @param UpdateTaskStatusRequest $request
     * @return RedirectResponse
     */
    public function updateStatus(UpdateTaskStatusRequest $request): RedirectResponse
    {
        $task_uuid = $request->validated('task_uuid');
        $next_status = TaskStatus::from((string) $request->validated('status'));
        $current_task = $this->task->newQuery()
            ->select('task_id', 'status')
            ->where('task_uuid', $task_uuid)
            ->first();

        $current_status = $current_task !== null ? TaskStatus::tryFrom((string) $current_task->status) : null;

        if ($current_status === null || ! $this->task_transition->canTransition($current_status, $next_status)) {
            return redirect()->route('tasks.index')->with('error', '許可されていないステータス遷移です。');
        }

        $updated = $this->task->updateStatusByUuid(
            $task_uuid,
            $next_status->value
        );

        $type = $updated ? 'success' : 'error';
        $message = $updated ? 'タスクのステータスを更新しました。' : '指定されたタスクは存在しないか、既に削除されています。';

        return redirect()->route('tasks.index')->with($type, $message);
    }
}
