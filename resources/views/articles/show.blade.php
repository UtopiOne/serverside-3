@extends('layouts.app')

@section('title', $article->title)
@section('content-width', 'max-w-5xl')

@section('content')
    <nav class="mb-8 flex flex-wrap items-center justify-between gap-4" aria-label="Действия со статьёй">
        <a href="{{ route('articles.index') }}"
            class="text-sm text-slate-400 hover:text-white focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-slate-400">←
            Все статьи</a>
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('articles.edit', $article) }}"
                class="rounded-lg bg-slate-200 px-4 py-2 text-sm font-semibold text-slate-950 hover:bg-white focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-slate-400">Редактировать</a>
            <form action="{{ route('articles.destroy', $article) }}" method="POST"
                onsubmit="return confirm('Удалить статью? Это действие нельзя отменить.');">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="cursor-pointer rounded-lg border border-red-400/30 px-4 py-2 text-sm text-red-300 hover:bg-red-400/10 focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-red-400">Удалить</button>
            </form>
        </div>
    </nav>

    <article class="overflow-hidden rounded-2xl border border-slate-800 bg-slate-900 shadow-xl shadow-black/20">
        <header class="mx-auto max-w-3xl wrap-break-word p-6 sm:p-10">
            <time datetime="{{ $article->published_at->format('Y-m-d') }}"
                class="text-sm text-slate-400">{{ $article->published_at->format('d.m.Y') }}</time>
            <h1 class="mt-4">{{ $article->title }}</h1>
            <p class="whitespace-pre-line text-lg leading-8 text-slate-300">{{ $article->brief_description }}</p>
        </header>
        <img src="{{ asset($article->full_image) }}" alt="{{ $article->title }}"
            class="max-h-128 w-full object-contain bg-slate-950">
        <div class="mx-auto max-w-3xl p-6 sm:p-10">
            <div class="whitespace-pre-wrap wrap-break-word text-base leading-8 text-slate-200">{{ $article->description }}
            </div>
        </div>
    </article>
@endsection
