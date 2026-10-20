@csrf

@if ($errors->any())
    <div role="alert" class="rounded-xl border border-red-400/30 bg-red-400/10 p-4 text-sm text-red-300">
        Проверьте отмеченные поля — статья ещё не сохранена.
    </div>
@endif

<div class="grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_18rem]">
    <section
        class="flex min-w-0 flex-col gap-6 rounded-2xl border border-slate-800 bg-slate-900 p-6 shadow-xl shadow-black/20 sm:p-8"
        aria-label="Текст статьи">
        <div class="flex flex-col gap-2">
            <label for="title" class="text-sm font-medium text-slate-200">Заголовок</label>
            <input type="text" id="title" name="title" required
                aria-invalid="{{ $errors->has('title') ? 'true' : 'false' }}"
                @error('title') aria-describedby="title-error" @enderror maxlength="255" placeholder="Название статьи"
                value="{{ old('title', $article?->title) }}"
                class="w-full rounded-lg border border-slate-700 bg-slate-950 px-4 py-3 text-base text-white placeholder:text-slate-500 focus:border-slate-400 focus:outline-2 focus:outline-offset-2 focus:outline-slate-400 text-xl font-semibold">
            @error('title')
                <span id="title-error" class="text-sm text-red-400">{{ $message }}</span>
            @enderror
        </div>
        <div class="flex flex-col gap-2">
            <label for="brief_description" class="text-sm font-medium text-slate-200">Краткое описание</label>
            <textarea id="brief_description" name="brief_description" required
                aria-invalid="{{ $errors->has('brief_description') ? 'true' : 'false' }}"
                @error('brief_description') aria-describedby="brief_description-error" @enderror rows="3"
                placeholder="О чём эта статья?"
                class="w-full rounded-lg border border-slate-700 bg-slate-950 px-4 py-3 text-base text-white placeholder:text-slate-500 focus:border-slate-400 focus:outline-2 focus:outline-offset-2 focus:outline-slate-400 resize-y leading-8">{{ old('brief_description', $article?->brief_description) }}</textarea>
            @error('brief_description')
                <span id="brief_description-error" class="text-sm text-red-400">{{ $message }}</span>
            @enderror
        </div>
        <div class="flex flex-col gap-2">
            <label for="description" class="text-sm font-medium text-slate-200">Текст статьи</label>
            <textarea id="description" name="description" required
                aria-invalid="{{ $errors->has('description') ? 'true' : 'false' }}"
                @error('description') aria-describedby="description-error" @enderror rows="18"
                placeholder="Начните писать здесь…"
                class="w-full rounded-lg border border-slate-700 bg-slate-950 px-4 py-3 text-base text-white placeholder:text-slate-500 focus:border-slate-400 focus:outline-2 focus:outline-offset-2 focus:outline-slate-400 resize-y leading-8">{{ old('description', $article?->description) }}</textarea>
            @error('description')
                <span id="description-error" class="text-sm text-red-400">{{ $message }}</span>
            @enderror
        </div>
        <span class="text-xs text-slate-400">Обычный текст. Переносы строк сохранятся при просмотре.</span>
    </section>

    <aside class="flex min-w-0 flex-col gap-6 rounded-2xl border border-slate-800 bg-slate-900 p-6"
        aria-labelledby="article-settings">
        <h2 id="article-settings" class="text-lg font-semibold text-white">Параметры статьи</h2>
        <div class="flex flex-col gap-2">
            <label for="published_at" class="text-sm font-medium text-slate-200">Дата публикации</label>
            <input type="date" id="published_at" name="published_at" required placeholder=""
                value="{{ old('published_at', $article?->published_at?->format('Y-m-d')) }}"
                aria-invalid="{{ $errors->has('published_at') ? 'true' : 'false' }}"
                @error('published_at') aria-describedby="published_at-error" @enderror
                class="w-full rounded-lg border border-slate-700 bg-slate-950 px-4 py-3 text-base text-white placeholder:text-slate-500 focus:border-slate-400 focus:outline-2 focus:outline-offset-2 focus:outline-slate-400 [color-scheme:dark]">
            @error('published_at')
                <span id="published_at-error" class="text-sm text-red-400">{{ $message }}</span>
            @enderror
        </div>
        <div class="flex flex-col gap-2">
            <label for="preview_image" class="text-sm font-medium text-slate-200">Изображение для списка</label>
            <input type="text" id="preview_image" name="preview_image" required maxlength="255"
                placeholder="preview.jpg" value="{{ old('preview_image', $article?->preview_image) }}"
                aria-invalid="{{ $errors->has('preview_image') ? 'true' : 'false' }}"
                @error('preview_image') aria-describedby="preview_image-error" @enderror
                class="w-full rounded-lg border border-slate-700 bg-slate-950 px-4 py-3 text-base text-white placeholder:text-slate-500 focus:border-slate-400 focus:outline-2 focus:outline-offset-2 focus:outline-slate-400 [color-scheme:dark]">
            @error('preview_image')
                <span id="preview_image-error" class="text-sm text-red-400">{{ $message }}</span>
            @enderror
        </div>
        <div class="flex flex-col gap-2">
            <label for="full_image" class="text-sm font-medium text-slate-200">Основное изображение</label>
            <input type="text" id="full_image" name="full_image" required maxlength="255" placeholder="full.jpeg"
                value="{{ old('full_image', $article?->full_image) }}"
                aria-invalid="{{ $errors->has('full_image') ? 'true' : 'false' }}"
                @error('full_image') aria-describedby="full_image-error" @enderror
                class="w-full rounded-lg border border-slate-700 bg-slate-950 px-4 py-3 text-base text-white placeholder:text-slate-500 focus:border-slate-400 focus:outline-2 focus:outline-offset-2 focus:outline-slate-400 [color-scheme:dark]">
            @error('full_image')
                <span id="full_image-error" class="text-sm text-red-400">{{ $message }}</span>
            @enderror
        </div>
        <span class="text-xs leading-6 text-slate-400">Укажите имена уже размещённых на сайте изображений, например
            preview.jpg.</span>
        <div class="flex flex-col gap-3 border-t border-slate-800 pt-6">
            <button type="submit"
                class="cursor-pointer rounded-lg bg-slate-200 px-4 py-3 text-sm font-semibold text-slate-950 hover:bg-white focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-slate-400 active:bg-slate-300">{{ $submitLabel }}</button>
            <a href="{{ $article ? route('articles.show', $article) : route('articles.index') }}"
                class="rounded-lg px-4 py-3 text-center text-sm text-slate-300 hover:text-white focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-slate-400">Отмена</a>
        </div>
    </aside>
</div>
