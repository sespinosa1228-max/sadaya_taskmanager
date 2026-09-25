@extends('layouts.app')

@section('title', $task->title)
@section('breadcrumb', 'Task details')

@section('content')
    <a class="back-link" href="{{ route('tasks.index', [], false) }}"><span aria-hidden="true">←</span> Back to tasks</a>
    <div class="page-heading"><div><p class="eyebrow">Task details</p><h1>{{ $task->title }}</h1></div><a class="button" href="{{ route('tasks.edit', $task, false) }}">Edit task</a></div>
    <section class="detail-panel">
        <p>{{ $task->description ?: 'No notes added.' }}</p>
        <p><strong>Status:</strong> {{ ucfirst($task->status) }}</p>
        <p><strong>Priority:</strong> {{ ucfirst($task->priority) }}</p>
        <p><strong>Due:</strong> {{ $task->due_date?->format('F j, Y') ?? 'No due date' }}</p>
    </section>
@endsection