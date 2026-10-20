@extends('layouts.app')

@section('title', 'Редактирование статьи')
@section('content-width', 'max-w-6xl')

@section('content')
    <header class="mb-8">
        <a href="{{ route('articles.index') }}"
            class="text-sm text-slate-400 hover:text-white focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-slate-400">←
            Все статьи</a>
        <h1 class="mt-4">Редактирование статьи</h1>
        <p class="text-slate-400">Обновите текст и параметры статьи, затем сохраните изменения.</p>
    </header>

    <form action="{{ route('articles.update', $article) }}" method="POST" class="flex flex-col gap-6">
        @method('PUT')
        @include('articles._form', ['article' => $article, 'submitLabel' => 'Сохранить изменения'])
    </form>
@endsection
