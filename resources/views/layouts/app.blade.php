<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Task Manager') · Web of Things</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bree+Serif&family=DM+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="app-shell">
        <aside class="sidebar">
            <a class="brand" href="{{ route('tasks.index', [], false) }}">
                <span class="brand-mark" aria-hidden="true">W</span>
                <span class="brand-name">Web of Things</span>
            </a>
            <div>
                <p class="side-label">Workspace</p>
                <nav class="nav-links" aria-label="Main navigation">
                    <a class="nav-link {{ request()->routeIs('tasks.index') ? 'active' : '' }}" href="{{ route('tasks.index', [], false) }}">
                        <span class="nav-icon" aria-hidden="true">⌂</span><span class="nav-text">My tasks</span>
                    </a>
                    <a class="nav-link {{ request()->routeIs('tasks.create') ? 'active' : '' }}" href="{{ route('tasks.create', [], false) }}">
                        <span class="nav-icon" aria-hidden="true">＋</span><span class="nav-text">New task</span>
                    </a>
                </nav>
            </div>
            <div class="sidebar-note">A little focus goes a long way.<br>Keep your day in good hands.</div>
        </aside>

        <main class="main-area">
            <header class="topbar">
                <span class="crumb">Workspace <span aria-hidden="true">/</span> @yield('breadcrumb', 'My tasks')</span>
                <time class="top-date" datetime="{{ now()->toDateString() }}">{{ now()->format('D, M j') }}</time>
            </header>
            <div class="content">
                @if (session('success'))
                    <div class="flash" role="status">{{ session('success') }}</div>
                @endif
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>