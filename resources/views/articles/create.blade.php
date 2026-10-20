@extends('layouts.app')

@section('title', 'Новая статья')
@section('content-width', 'max-w-6xl')

@section('content')
    <header class="mb-8">
        <a href="{{ route('articles.index') }}"
            class="text-sm text-slate-400 hover:text-white focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-slate-400">←
            Все статьи</a>
        <h1 class="mt-4">Новая статья</h1>
    </header>

    <form action="{{ route('articles.store') }}" method="POST" class="flex flex-col gap-6">
        @include('articles._form', ['article' => null, 'submitLabel' => 'Создать статью'])
    </form>
@endsection
