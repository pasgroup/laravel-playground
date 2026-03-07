@extends('layouts.tasks')

@section('title', 'タスク一覧')

@section('content')
    <header class="task-header">
        <h1 class="task-title" dusk="index-heading">タスク一覧</h1>
        <a href="{{ route('tasks.create') }}" class="task-index-add-btn" dusk="index-create-link">新規タスク登録</a>
    </header>

    <h2 class="task-list-heading" dusk="index-list-heading">登録済みタスク</h2>
    @if ($tasks->isEmpty())
        <p class="task-empty" dusk="index-empty-message">登録されたタスクはありません。</p>
    @else
        <div class="task-table-wrap" dusk="index-task-table-wrap">
            <table class="task-table" dusk="index-task-table">
                <thead>
                    <tr>
                        <th>タイトル</th>
                        <th>詳細</th>
                        <th class="task-cell-due-date">期限日</th>
                        <th>ステータス</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    @php $prev_task = null; @endphp
                    @foreach ($tasks as $task)
                        @php
                            $is_first_completed = $prev_task !== null && $task->is_completed && ! $prev_task->is_completed;
                        @endphp
                        <tr dusk="index-task-row-{{ $task->task_id }}" @class([
                            'task-row-overdue' => $task->is_overdue,
                            'task-row-first-completed' => $is_first_completed,
                        ])>
                            <td dusk="index-task-title-{{ $task->task_id }}">{{ $task->title }}</td>
                            <td class="task-cell-muted" dusk="index-task-detail-{{ $task->task_id }}">
                                @if ($task->detail !== null && $task->detail !== '')
                                    {!! nl2br(e($task->detail)) !!}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="task-cell-due-date" dusk="index-task-due-date-{{ $task->task_id }}">{{ $task->due_date?->format('Y-m-d') ?? '—' }}</td>
                            <td>
                                <form action="{{ route('tasks.status.update', ['task_uuid' => $task->task_uuid]) }}" method="post" class="task-form-inline task-status-form">
                                    @csrf
                                    <select name="status" class="task-status-select" aria-label="ステータス" onchange="this.form.submit()" dusk="index-status-select-{{ $task->task_id }}">
                                        <option value="not_started" @selected($task->status === 'not_started')>未着手</option>
                                        <option value="in_progress" @selected($task->status === 'in_progress')>進行中</option>
                                        <option value="completed" @selected($task->status === 'completed')>完了</option>
                                    </select>
                                    <button type="submit" class="task-status-submit-btn" aria-label="ステータスを更新">更新</button>
                                </form>
                            </td>
                            <td>
                                <form action="{{ route('tasks.destroy', ['task_uuid' => $task->task_uuid]) }}" method="post" class="task-form-inline" onsubmit="return confirm('このタスクを削除してもよろしいですか？');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="task-delete-btn" dusk="index-delete-btn-{{ $task->task_id }}">削除</button>
                                </form>
                            </td>
                        </tr>
                        @php $prev_task = $task; @endphp
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection
