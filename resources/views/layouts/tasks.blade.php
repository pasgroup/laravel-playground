<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title') - {{ config('app.name') }}</title>
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
        <link href="{{ asset('css/tasks.css') }}?v={{ filemtime(public_path('css/tasks.css')) }}" rel="stylesheet">
    </head>
    <body class="task-page">
        <div id="task-toast-container" class="task-toast-container" aria-live="polite" aria-atomic="true"></div>
        <div class="task-container">
            @yield('content')
        </div>
        <script>
            (function () {
                const flash = @json(['success' => session('success'), 'error' => session('error')]);

                /**
                 * トーストを表示する
                 * @param string $type トーストの種類
                 * @param string $message トーストのメッセージ
                 */
                function showTaskToast(type, message) {
                    if (!message) return;
                    const container = document.getElementById('task-toast-container');
                    if (!container) return;
                    const toast = document.createElement('div');
                    toast.className = 'task-toast task-toast--' + type;
                    toast.setAttribute('role', 'alert');
                    toast.textContent = message;
                    container.appendChild(toast);
                    const duration = 4000;
                    const hide = function () {
                        toast.classList.add('task-toast--hide');
                        setTimeout(function () {
                            if (toast.parentNode) toast.parentNode.removeChild(toast);
                        }, 300);
                    };
                    setTimeout(hide, duration);
                    toast.addEventListener('click', hide);
                }

                if (flash.success) showTaskToast('success', flash.success);
                if (flash.error) showTaskToast('error', flash.error);
            })();
        </script>
        @stack('scripts')
    </body>
</html>
