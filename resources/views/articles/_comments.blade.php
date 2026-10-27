<section id="comments" class="mt-10 flex flex-col gap-6" aria-labelledby="comments-heading">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h2 id="comments-heading" class="text-2xl font-semibold text-white">Комментарии</h2>
        <span class="text-sm text-slate-400">Всего: {{ $comments->total() }}</span>
    </div>

    @can('create', \App\Models\Comment::class)
        <form action="{{ route('article.comments.store', $article) }}" method="POST"
            class="flex flex-col gap-4 rounded-2xl border border-slate-800 bg-slate-900 p-6 sm:p-8">
            @csrf
            <div class="flex flex-col gap-2">
                <label for="comment-contents" class="text-sm font-medium text-slate-200">Ваш комментарий</label>
                <textarea id="comment-contents" name="contents" rows="4" maxlength="1023" required
                    aria-invalid="{{ $errors->createComment->has('contents') ? 'true' : 'false' }}"
                    @error('contents', 'createComment') aria-describedby="comment-contents-error" @enderror
                    placeholder="Поделитесь своим мнением о статье…"
                    class="w-full resize-y rounded-lg border border-slate-700 bg-slate-950 px-4 py-3 text-base leading-7 text-white placeholder:text-slate-500 focus:border-slate-400 focus:outline-2 focus:outline-offset-2 focus:outline-slate-400">{{ $errors->hasBag('createComment') ? old('contents') : '' }}</textarea>
                @error('contents', 'createComment')
                    <span id="comment-contents-error" role="alert" class="text-sm text-red-400">{{ $message }}</span>
                @enderror
                <span class="text-xs text-slate-400">До 1023 символов.</span>
            </div>
            <button type="submit"
                class="cursor-pointer self-start rounded-lg bg-slate-200 px-4 py-3 text-sm font-semibold text-slate-950 hover:bg-white focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-slate-400">Опубликовать комментарий</button>
        </form>
    @endcan

    <div class="flex flex-col gap-4">
        @forelse ($comments as $comment)
            <article id="comment-{{ $comment->id }}"
                class="min-w-0 rounded-2xl border border-slate-800 bg-slate-900 p-6 sm:p-8">
                <header class="mb-4 flex flex-wrap items-center justify-between gap-2">
                    <span class="wrap-break-word font-semibold text-slate-200">{{ $comment->user?->name ?? 'Пользователь удалён' }}</span>
                    <time datetime="{{ $comment->published_at->toIso8601String() }}"
                        class="text-xs text-slate-400">{{ $comment->published_at->format('d.m.Y H:i') }}</time>
                </header>
                <p class="whitespace-pre-wrap wrap-break-word text-sm leading-7 text-slate-300">{{ $comment->contents }}</p>

                @can('update', $comment)
                    @php($editBag = 'updateComment'.$comment->id)
                    <details class="mt-4" @if ($errors->getBag($editBag)->any()) open @endif>
                        <summary class="cursor-pointer text-sm text-slate-400 hover:text-white focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-slate-400">Редактировать комментарий</summary>
                        <form action="{{ route('article.comments.update', [$article, $comment]) }}" method="POST"
                            class="mt-4 flex flex-col gap-3">
                            @csrf
                            @method('PUT')
                            <label for="comment-edit-{{ $comment->id }}" class="text-sm font-medium text-slate-200">Текст комментария</label>
                            <textarea id="comment-edit-{{ $comment->id }}" name="contents" rows="4" maxlength="1023" required
                                aria-invalid="{{ $errors->getBag($editBag)->has('contents') ? 'true' : 'false' }}"
                                @error('contents', $editBag) aria-describedby="comment-edit-error-{{ $comment->id }}" @enderror
                                class="w-full resize-y rounded-lg border border-slate-700 bg-slate-950 px-4 py-3 text-sm leading-7 text-white focus:border-slate-400 focus:outline-2 focus:outline-offset-2 focus:outline-slate-400">{{ $errors->getBag($editBag)->any() ? old('contents', $comment->contents) : $comment->contents }}</textarea>
                            @error('contents', $editBag)
                                <span id="comment-edit-error-{{ $comment->id }}" role="alert" class="text-sm text-red-400">{{ $message }}</span>
                            @enderror
                            <button type="submit"
                                class="cursor-pointer self-start rounded-lg bg-slate-200 px-4 py-2 text-sm font-semibold text-slate-950 hover:bg-white focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-slate-400">Сохранить комментарий</button>
                        </form>
                    </details>
                @endcan

                @can('delete', $comment)
                    <form action="{{ route('article.comments.destroy', [$article, $comment]) }}" method="POST"
                        class="mt-4" onsubmit="return confirm('Удалить комментарий? Это действие нельзя отменить.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="cursor-pointer rounded-lg border border-red-400/30 px-4 py-2 text-sm text-red-300 hover:bg-red-400/10 focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-red-400">Удалить комментарий</button>
                    </form>
                @endcan
            </article>
        @empty
            <p class="rounded-2xl border border-dashed border-slate-800 p-6 text-center text-sm text-slate-400">Комментариев пока нет.</p>
        @endforelse
    </div>

    {{ $comments->onEachSide(1)->links() }}
</section>
