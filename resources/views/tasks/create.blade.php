@extends('layouts.app')

@section('title', 'New task')
@section('breadcrumb', 'New task')

@section('content')
    <a class="back-link" href="{{ route('tasks.index', [], false) }}"><span aria-hidden="true">←</span> Back to tasks</a>
    <div class="page-heading">
        <div><p class="eyebrow">Get it out of your head</p><h1>New task</h1><p class="subheading">Give your next thing a name and a place to land.</p></div>
    </div>
    <section class="form-shell">
        <form action="{{ route('tasks.store', [], false) }}" method="POST">
            @csrf
            @include('tasks._form')
        </form>
    </section>
@endsection