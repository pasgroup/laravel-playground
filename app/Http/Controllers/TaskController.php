<?php

namespace App\Http\Controllers;

use App\Application\Task\DTO\CreateTaskInput;
use App\Application\Task\DTO\DeleteTaskInput;
use App\Application\Task\DTO\TaskCommandOutput;
use App\Application\Task\DTO\UpdateTaskStatusInput;
use App\Application\Task\Exceptions\TaskApplicationException;
use App\Application\Task\UseCase\CreateTaskUseCase;
use App\Application\Task\UseCase\DeleteTaskUseCase;
use App\Application\Task\UseCase\ListTasksUseCase;
use App\Application\Task\UseCase\UpdateTaskStatusUseCase;
use App\Http\Presenters\Task\TaskFlashPresenter;
use App\Http\Presenters\Task\TaskIndexPresenter;
use App\Http\Requests\DestroyTaskRequest;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskStatusRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskController extends Controller
{
    /**
     * コンストラクタ
     */
    public function __construct(
        protected ListTasksUseCase $list_tasks_use_case,
        protected CreateTaskUseCase $create_task_use_case,
        protected DeleteTaskUseCase $delete_task_use_case,
        protected UpdateTaskStatusUseCase $update_task_status_use_case,
        protected TaskIndexPresenter $task_index_presenter,
        protected TaskFlashPresenter $task_flash_presenter
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
        $output = $this->list_tasks_use_case->handle();
        $view_model = $this->task_index_presenter->present(
            $output,
            $request->session()->get('success'),
            $request->session()->get('error')
        );

        return view('tasks.index', [
            'view_model' => $view_model,
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

        $output = $this->create_task_use_case->handle(
            new CreateTaskInput(
                $validated['title'],
                $validated['detail'] ?? null,
                $validated['due_date'] ?? null
            )
        );

        return $this->redirectWithOutput($output);
    }

    /**
     * タスクを削除する
     *
     * @param DestroyTaskRequest $request
     * @return RedirectResponse
     */
    public function destroy(DestroyTaskRequest $request): RedirectResponse
    {
        try {
            $output = $this->delete_task_use_case->handle(
                new DeleteTaskInput($request->validated('task_uuid'))
            );

            return $this->redirectWithOutput($output);
        } catch (TaskApplicationException $exception) {
            return $this->redirectWithException($exception);
        }
    }

    /**
     * タスクのステータスを更新する
     *
     * @param UpdateTaskStatusRequest $request
     * @return RedirectResponse
     */
    public function updateStatus(UpdateTaskStatusRequest $request): RedirectResponse
    {
        try {
            $output = $this->update_task_status_use_case->handle(
                new UpdateTaskStatusInput(
                    $request->validated('task_uuid'),
                    $request->validated('status')
                )
            );

            return $this->redirectWithOutput($output);
        } catch (TaskApplicationException $exception) {
            return $this->redirectWithException($exception);
        }
    }

    private function redirectWithOutput(TaskCommandOutput $output): RedirectResponse
    {
        $flash = $this->task_flash_presenter->presentCommand($output);

        return redirect()->route('tasks.index')->with(
            $flash['flash_type'],
            $flash['flash_message']
        );
    }

    private function redirectWithException(TaskApplicationException $exception): RedirectResponse
    {
        $flash = $this->task_flash_presenter->presentException($exception);

        return redirect()->route('tasks.index')->with(
            $flash['flash_type'],
            $flash['flash_message']
        );
    }
}
