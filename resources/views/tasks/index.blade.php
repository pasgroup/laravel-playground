@extends('layouts.tasks')

@section('title', 'タスク一覧')

@section('content')
    <header class="task-header">
        <h1 class="task-title">タスク一覧</h1>
        <a href="{{ route('tasks.create') }}" class="task-index-add-btn">新規タスク登録</a>
    </header>

    @if (session('success'))
        <p class="task-message task-message--success">{{ session('success') }}</p>
    @endif

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
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tasks as $task)
                        <tr>
                            <td>{{ $task->title }}</td>
                            <td class="task-cell-muted">{{ $task->detail ?: '—' }}</td>
                            <td>{{ $task->due_date?->format('Y-m-d') ?? '—' }}</td>
                            <td>{{ $task->status_label }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection
