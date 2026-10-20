@extends('layouts.app')

@section('title', 'Articles')
@section('content-width', 'max-w-7xl')

@section('content')
    <h1>Articles</h1>

    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
        @foreach ($articles as $article)
            <article class="overflow-hidden rounded-xl border border-slate-800 bg-slate-900">
                <div class="flex flex-col">
                    <a href="/gallery?image={{ $article->full_image }}">
                        <img class="aspect-video w-full object-cover" src="{{ asset($article->preview_image) }}"
                            alt="{{ $article->name }}" loading="lazy">
                    </a>

                    <div class="flex min-w-0 flex-col gap-3 p-6">
                        <span class="text-xs font-medium tracking-wide text-slate-400">{{ $article->date }}</span>
                        <h2 class="text-xl font-semibold leading-snug text-white">{{ $article->name }}</h2>
                        <small class="text-xs text-slate-400">{{ $article->brief_description }}</small>
                        <p class="text-sm leading-7 text-slate-300">{{ $article->description }}</p>
                    </div>
                </div>
            </article>
        @endforeach
    </div>
@endsection
