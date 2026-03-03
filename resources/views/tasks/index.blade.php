<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>タスク一覧 - Laravel Playground</title>
        <link href="{{ asset('css/tasks.css') }}?v={{ filemtime(public_path('css/tasks.css')) }}" rel="stylesheet">
    </head>
    <body class="task-page">
        <div class="task-container">
            <h1 class="task-title">タスク一覧</h1>

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
        </div>
    </body>
</html>
