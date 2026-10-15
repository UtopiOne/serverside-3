@extends('layouts.app')

@section('title', 'Articles')

@section('content')
    <h1>Articles</h1>

    <div class="grid gap-6">
        @foreach ($articles as $article)
            <article class="overflow-hidden rounded-xl border border-slate-800 bg-slate-900">
                <div class="grid sm:grid-cols-[12rem_minmax(0,1fr)]">
                    <a href="/gallery?image={{ $article['full_image'] }}">
                        <img class="aspect-video w-full object-cover sm:aspect-square"
                            src="{{ asset($article['preview_image']) }}" alt="{{ $article['Название'] }}" loading="lazy">
                    </a>

                    <div class="flex min-w-0 flex-col gap-3 p-6">
                        <span class="text-xs font-medium tracking-wide text-slate-400">{{ $article['Дата'] }}</span>
                        <h2 class="text-xl font-semibold leading-snug text-white">{{ $article['Название'] }}</h2>
                        <p class="text-sm leading-7 text-slate-300">{{ $article['Описание'] }}</p>
                    </div>
                </div>
            </article>
        @endforeach
    </div>
@endsection
