@extends('layouts.app')

@section('title', 'Комментарии на проверке')

@section('content')
    <div class="flex flex-col gap-2">
        <h1>Комментарии на проверке</h1>
        <p class="text-slate-400">Ожидают одобрения: {{ $comments->total() }}</p>
    </div>

    <div class="mt-8 flex flex-col gap-4">
        @forelse ($comments as $comment)
            <article class="rounded-2xl border border-slate-800 bg-slate-900 p-6 sm:p-8">
                <div class="mb-4 flex flex-wrap items-center justify-between gap-2 text-sm text-slate-400">
                    <span>{{ $comment->user?->name ?? 'Пользователь удалён' }}</span>
                    <time
                        datetime="{{ $comment->published_at->toIso8601String() }}">{{ $comment->published_at->format('d.m.Y H:i') }}</time>
                </div>

                <p class="whitespace-pre-wrap wrap-break-word text-slate-200">{{ $comment->contents }}</p>

                <a class="mt-4 inline-block text-sm text-slate-300 underline underline-offset-4 hover:text-white focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-slate-400"
                    href="{{ route('articles.show', $comment->article) }}">{{ $comment->article->title }}</a>

                <div class="mt-4 flex flex-wrap gap-3">
                    <form action="{{ route('moderation.comments.approve', $comment) }}" method="POST"
                        onsubmit="return confirm('Одобрить комментарий?');">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                            class="cursor-pointer rounded-lg bg-slate-200 px-4 py-2 text-sm font-semibold text-slate-950 hover:bg-white focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-slate-400">Одобрить</button>
                    </form>

                    <form action="{{ route('moderation.comments.destroy', $comment) }}" method="POST"
                        onsubmit="return confirm('Удалить комментарий? Это действие нельзя отменить.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="cursor-pointer rounded-lg border border-red-400/30 px-4 py-2 text-sm text-red-300 hover:bg-red-400/10 focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-red-400">Удалить</button>
                    </form>
                </div>
            </article>
        @empty
            <p class="rounded-2xl border border-dashed border-slate-800 p-6 text-center text-sm text-slate-400">Нет комментариев на проверке.</p>
        @endforelse
    </div>

    <div class="mt-6">{{ $comments->onEachSide(1)->links() }}</div>
@endsection
