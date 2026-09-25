@extends('layouts.app')

@section('title', 'Edit task')
@section('breadcrumb', 'Edit task')

@section('content')
    <a class="back-link" href="{{ route('tasks.index', [], false) }}"><span aria-hidden="true">←</span> Back to tasks</a>
    <div class="page-heading">
        <div><p class="eyebrow">Keep your plans in shape</p><h1>Edit task</h1><p class="subheading">Update the details whenever things change.</p></div>
    </div>
    <section class="form-shell">
        <form action="{{ route('tasks.update', $task, false) }}" method="POST">
            @csrf
            @method('PUT')
            @include('tasks._form')
        </form>
    </section>
@endsection