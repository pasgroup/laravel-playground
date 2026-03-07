@extends('layouts.tasks')

@section('title', 'タスク追加')

@section('content')
    <header class="task-header">
        <h1 class="task-title" dusk="create-heading">タスク追加</h1>
        <a href="{{ route('tasks.index') }}" class="task-header-back-link" dusk="create-back-link">一覧に戻る</a>
    </header>

    @php
        $task_title_max  = config('app.task_title_max');
        $task_detail_max = config('app.task_detail_max');
    @endphp
    <section class="task-form-section" aria-labelledby="task-form-heading">
        <h2 id="task-form-heading" class="task-form-heading">新規タスク登録</h2>
        <form action="{{ route('tasks.store') }}" method="post" class="task-form" data-task-title-max="{{ $task_title_max }}" data-task-detail-max="{{ $task_detail_max }}" dusk="create-form">
            @csrf
            <div class="task-form-row">
                <div class="task-form-label-row">
                    <label for="title" class="task-form-label">タイトル <span class="task-form-required">必須</span></label>
                    <span id="title-char-count" class="task-form-char-count" aria-live="polite">0 / {{ $task_title_max }}</span>
                </div>
                <input type="text" name="title" id="title" value="{{ old('title') }}" class="task-form-input @error('title') task-form-input--error @enderror" maxlength="{{ $task_title_max }}" dusk="create-title-input">
                @error('title')
                    <p class="task-form-error" dusk="create-title-error">{{ $message }}</p>
                @enderror
            </div>
            <div class="task-form-row">
                <div class="task-form-label-row">
                    <label for="detail" class="task-form-label">詳細</label>
                    <span id="detail-char-count" class="task-form-char-count" aria-live="polite">0 / {{ $task_detail_max }}</span>
                </div>
                <textarea name="detail" id="detail" rows="4" class="task-form-input task-form-textarea @error('detail') task-form-input--error @enderror" maxlength="{{ $task_detail_max }}">{{ old('detail') }}</textarea>
                @error('detail')
                    <p class="task-form-error" dusk="create-detail-error">{{ $message }}</p>
                @enderror
            </div>
            <div class="task-form-row">
                <label for="due_date" class="task-form-label">期限日</label>
                <input type="date" name="due_date" id="due_date" value="{{ old('due_date') }}" class="task-form-input @error('due_date') task-form-input--error @enderror">
                @error('due_date')
                    <p class="task-form-error" dusk="create-due-date-error">{{ $message }}</p>
                @enderror
            </div>
            <div class="task-form-row task-form-row--submit">
                <button type="submit" class="task-form-submit" dusk="create-submit-btn">登録する</button>
            </div>
        </form>
    </section>

    @push('scripts')
    <script>
        (function () {
            const form = document.querySelector('.task-form');
            const titleEl = document.getElementById('title');
            const detailEl = document.getElementById('detail');
            if (!form || !titleEl || !detailEl) {
                return;
            }

            const updateTitleCount = function () {
                const titleCountEl = document.getElementById('title-char-count');
                const titleMax = Number(form.dataset.taskTitleMax);
                const len = titleEl.value.length;
                titleCountEl.textContent = len + ' / ' + titleMax;
            };

            const updateDetailCount = function () {
                const detailCountEl = document.getElementById('detail-char-count');
                const detailMax = Number(form.dataset.taskDetailMax);
                const len = detailEl.value.length;
                detailCountEl.textContent = len + ' / ' + detailMax;
            };

            titleEl.addEventListener('input', updateTitleCount);
            detailEl.addEventListener('input', updateDetailCount);

            updateTitleCount();
            updateDetailCount();
        })();
    </script>
    @endpush
@endsection
