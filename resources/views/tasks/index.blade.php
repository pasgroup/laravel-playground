@extends('layouts.tasks')

@section('title', 'タスク一覧')

@section('content')
    <header class="task-header">
        <h1 class="task-title">タスク一覧</h1>
        <a href="{{ route('tasks.create') }}" class="task-index-add-btn">新規タスク登録</a>
    </header>

    <h2 class="task-list-heading">登録済みタスク</h2>
    @if ($tasks->isEmpty())
        <p class="task-empty">登録されたタスクはありません。</p>
    @else
        <div class="task-table-wrap">
            <table class="task-table">
                <thead>
                    <tr>
                        <th>タイトル</th>
                        <th>詳細</th>
                        <th>期限日</th>
                        <th>ステータス</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tasks as $task)
                        <tr>
                            <td>{{ $task->title }}</td>
                            <td class="task-cell-muted">{{ $task->detail ?: '—' }}</td>
                            <td>{{ $task->due_date?->format('Y-m-d') ?? '—' }}</td>
                            <td>
                                <form action="{{ route('tasks.status.update', ['task_uuid' => $task->task_uuid]) }}" method="post" class="task-form-inline task-status-form">
                                    @csrf
                                    <select name="status" class="task-status-select" aria-label="ステータス" onchange="this.form.submit()">
                                        <option value="not_started" @selected($task->status === 'not_started')>未着手</option>
                                        <option value="in_progress" @selected($task->status === 'in_progress')>進行中</option>
                                        <option value="completed" @selected($task->status === 'completed')>完了</option>
                                    </select>
                                </form>
                            </td>
                            <td>
                                <form action="{{ route('tasks.destroy', ['task_uuid' => $task->task_uuid]) }}" method="post" class="task-form-inline" onsubmit="return confirm('このタスクを削除してもよろしいですか？');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="task-delete-btn">削除</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection
