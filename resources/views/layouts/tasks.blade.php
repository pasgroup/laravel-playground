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
        <div class="task-container">
            @yield('content')
        </div>
        @stack('scripts')
    </body>
</html>
