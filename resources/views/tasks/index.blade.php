@extends('layouts.app')

@section('title', 'My tasks')
@section('breadcrumb', 'My tasks')

@section('content')
    <div class="page-heading">
        <div>
            <p class="eyebrow">Your personal command center</p>
            <h1>My tasks</h1>
            <p class="subheading">One thing at a time. You’ve got this.</p>
        </div>
        <a class="button" href="{{ route('tasks.create', [], false) }}"><span class="button-icon" aria-hidden="true">＋</span> New task</a>
    </div>

    <section class="hero" aria-label="Daily focus">
        <div class="hero-copy">
            <p class="hero-kicker">Your friendly neighborhood focus</p>
            <h2>{{ $pendingTasks === 0 ? 'All clear. Nice work.' : 'Make today count.' }}</h2>
            <p>{{ $pendingTasks }} {{ \Illuminate\Support\Str::plural('task', $pendingTasks) }} left on your list. Every small win adds up.</p>
        </div>
    </section>

    <section class="stats" aria-label="Task summary">
        <div class="stat"><span class="stat-label">All tasks</span><strong class="stat-value">{{ $totalTasks }}</strong></div>
        <div class="stat"><span class="stat-label">In progress</span><strong class="stat-value">{{ $pendingTasks }}</strong></div>
        <div class="stat"><span class="stat-label">Completed</span><strong class="stat-value">{{ $completedTasks }}</strong></div>
    </section>

    <div class="task-toolbar">
        <nav class="tabs" aria-label="Filter tasks">
            @foreach (['all' => 'All', 'pending' => 'Pending', 'completed' => 'Completed'] as $value => $label)
                <a class="tab {{ $filter === $value ? 'active' : '' }}" href="{{ route('tasks.index', array_filter(['filter' => $value, 'q' => $search]), false) }}">{{ $label }}</a>
            @endforeach
        </nav>
        <form class="search-form" action="{{ route('tasks.index', [], false) }}" method="GET" role="search">
            <input type="hidden" name="filter" value="{{ $filter }}">
            <label class="visually-hidden" for="task-search">Search tasks</label>
            <input class="search-input" id="task-search" type="search" name="q" value="{{ $search }}" placeholder="Search your tasks...">
            <button class="search-button" type="submit" aria-label="Search">⌕</button>
        </form>
    </div>

    <section class="task-list" aria-label="Tasks">
        @forelse ($tasks as $task)
            <article class="task-row" style="animation-delay: {{ min($loop->index, 8) * 35 }}ms">
                <form action="{{ route('tasks.status', $task, false) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="{{ $task->status === 'completed' ? 'pending' : 'completed' }}">
                    <button class="task-check {{ $task->status === 'completed' ? 'is-complete' : '' }}" type="submit" aria-label="Mark {{ $task->status === 'completed' ? 'pending' : 'completed' }}: {{ $task->title }}">
                        @if ($task->status === 'completed')<span aria-hidden="true">✓</span>@endif
                    </button>
                </form>
                <div class="task-copy">
                    <h2 class="task-title {{ $task->status === 'completed' ? 'is-complete' : '' }}"><a href="{{ route('tasks.show', $task, false) }}">{{ $task->title }}</a></h2>
                    @if ($task->description)<p class="task-description">{{ $task->description }}</p>@endif
                </div>
                <time class="task-date" @if ($task->due_date) datetime="{{ $task->due_date->toDateString() }}" @endif>{{ $task->due_date?->format('M j, Y') ?? 'No due date' }}</time>
                <span class="priority {{ $task->priority }}">{{ $task->priority }}</span>
                <div class="task-actions">
                    <a class="icon-action" href="{{ route('tasks.edit', $task, false) }}" aria-label="Edit {{ $task->title }}" title="Edit task">✎</a>
                    <form action="{{ route('tasks.destroy', $task, false) }}" method="POST" onsubmit="return confirm('Delete this task?')">
                        @csrf
                        @method('DELETE')
                        <button class="icon-action" type="submit" aria-label="Delete {{ $task->title }}" title="Delete task">×</button>
                    </form>
                </div>
            </article>
        @empty
            <div class="empty-state">
                <span class="empty-mark" aria-hidden="true">✦</span>
                <h2>{{ $search !== '' || $filter !== 'all' ? 'No tasks found' : 'Your list starts here' }}</h2>
                <p>{{ $search !== '' || $filter !== 'all' ? 'Try another search or choose a different filter.' : 'Add a task and make a little progress today.' }}</p>
                @if ($search === '' && $filter === 'all')<a class="button small" href="{{ route('tasks.create', [], false) }}">Add your first task</a>@endif
            </div>
        @endforelse
    </section>
@endsection