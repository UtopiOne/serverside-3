<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @vite('resources/css/app.css')

    <title>@yield('title')</title>
</head>

<body class="flex min-h-screen flex-col bg-slate-950 font-sans text-slate-200 antialiased">
    <header class="border-b border-slate-800 bg-slate-900">

        <nav class="mx-auto max-w-3xl px-6 py-4" aria-label="Main navigation">
            <ul class="flex gap-6">
                <li><a class="text-slate-300 hover:text-white focus-visible:rounded focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-slate-400"
                        href="/">Home Page</a></li>
                <li><a class="text-slate-300 hover:text-white focus-visible:rounded focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-slate-400"
                        href="/about">About Us</a></li>
                <li><a class="text-slate-300 hover:text-white focus-visible:rounded focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-slate-400"
                        href="/contact">Contact</a></li>
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
