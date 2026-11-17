<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <title>@yield('title')</title>

    @yield('head-extra')
</head>

<body class="flex min-h-screen flex-col bg-slate-950 font-sans text-slate-200 antialiased">
    @if (config('broadcasting.default') === 'pusher')
        @can('viewAny', \App\Models\Article::class)
            <div id="article-notifications"
                data-pusher-key="{{ config('broadcasting.connections.pusher.key') }}"
                data-pusher-cluster="{{ config('broadcasting.connections.pusher.options.cluster') }}"></div>
        @endcan
    @endif
    <header class="border-b border-slate-800 bg-slate-900">

        <nav class="mx-auto max-w-3xl px-6 py-4" aria-label="Main navigation">
            <ul class="flex gap-6">
                <li><a class="text-slate-300 hover:text-white focus-visible:rounded focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-slate-400"
                        href="/">Home Page</a></li>
                <li><a class="text-slate-300 hover:text-white focus-visible:rounded focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-slate-400"
                        href="/about">About Us</a></li>
                <li><a class="text-slate-300 hover:text-white focus-visible:rounded focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-slate-400"
                        href="/contact">Contact</a></li>

                @can('viewAny', \App\Models\Comment::class)
                    <li><a class="text-slate-300 hover:text-white focus-visible:rounded focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-slate-400"
                            href="{{ route('moderation.comments.index') }}">Moderation</a></li>
                @endcan

                @auth
                    <li><span class="text-slate-300">{{ auth()->user()->name }}</span></li>
                    @can('viewAny', \App\Models\Article::class)
                        <li>
                            <details class="relative">
                                <summary class="cursor-pointer list-none text-slate-300 hover:text-white focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-slate-400">
                                    Уведомления
                                    <span class="rounded-full bg-slate-700 px-2 py-0.5 text-xs text-white">{{ $unreadArticleNotifications->count() }}</span>
                                </summary>
                                <div class="absolute right-0 top-full z-50 mt-2 max-h-80 w-80 max-w-[calc(100vw-2rem)] overflow-y-auto rounded-lg border border-slate-700 bg-slate-900 p-2 shadow-xl shadow-black/40">
                                    @forelse ($unreadArticleNotifications as $notification)
                                        <a href="{{ route('articles.show', $notification->data['article_id']) }}"
                                            class="block rounded-md px-3 py-2 text-sm text-slate-200 hover:bg-slate-800 focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-slate-400">
                                            {{ $notification->data['article_title'] }}
                                        </a>
                                    @empty
                                        <p class="px-3 py-2 text-sm text-slate-400">Непрочитанных уведомлений нет.</p>
                                    @endforelse
                                </div>
                            </details>
                        </li>
                    @endcan
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf

                        <button type="submit" class="cursor-pointer text-sm text-slate-200 hover:text-white">Log
                            out</button>
                    </form>
                @else
                    <li><a class="text-slate-300 hover:text-white focus-visible:rounded focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-slate-400"
                            href="/signin">Sign
                            In</a></li>
                @endauth
            </ul>
        </nav>
    </header>

    <main
        class="mx-auto w-full @yield('content-width', 'max-w-3xl') flex-1 px-6 py-12 leading-relaxed [&_h1]:mb-4 [&_h1]:text-3xl [&_h1]:font-semibold [&_h1]:text-white [&_p]:mb-3">
        @yield('content')
    </main>

    <footer class="border-t border-slate-800 bg-slate-900">
        <p class="mx-auto max-w-3xl px-6 py-4 text-sm text-slate-400">Верповский Арсений 251-3210</p>
    </footer>
</body>

</html>
