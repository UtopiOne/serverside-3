@extends('layouts.app')

@section('title', 'Статьи')
@section('content-width', 'max-w-7xl')

@section('content')
    <header class="mb-8 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1>Статьи</h1>
            <p class="text-slate-400">Истории, заметки и идеи.</p>
        </div>
        <a href="{{ route('articles.create') }}"
            class="rounded-lg bg-slate-200 px-5 py-3 text-sm font-semibold text-slate-950 hover:bg-white focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-slate-400">Новая статья</a>
    </header>

    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
        @forelse ($articles as $article)
            <article class="overflow-hidden rounded-xl border border-slate-800 bg-slate-900">
                <div class="flex flex-col">
                    <a href="{{ route('articles.show', $article) }}" class="focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-slate-400">
                        <img class="aspect-video w-full object-cover" src="{{ asset($article->preview_image) }}"
                            alt="{{ $article->title }}" loading="lazy">
                    </a>

                    <div class="flex min-w-0 flex-col gap-3 p-6">
                        <time datetime="{{ $article->published_at->format('Y-m-d') }}" class="text-xs font-medium tracking-wide text-slate-400">{{ $article->published_at->format('d.m.Y') }}</time>
                        <h2 class="break-words text-xl font-semibold leading-snug text-white">
                            <a href="{{ route('articles.show', $article) }}" class="hover:text-slate-300 focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-slate-400">{{ $article->title }}</a>
                        </h2>
                        <p class="break-words text-sm leading-7 text-slate-400">{{ $article->brief_description }}</p>
                        <a href="{{ route('articles.edit', $article) }}" class="self-start rounded-lg border border-slate-700 px-4 py-2 text-sm text-slate-300 hover:bg-slate-800 hover:text-white focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-slate-400">Редактировать</a>
                    </div>
                </div>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-700 bg-slate-900 p-8 text-center text-slate-400 md:col-span-2 lg:col-span-3">
                Статей пока нет. Нажмите «Новая статья», чтобы написать первую.
            </div>
        @endforelse
    </div>

    <div class="flex flex-col min-w-0 gap-3 p-6">{{ $articles->links() }}</div>
@endsection
