@extends('layouts.app')

@section('title', 'Регистрация')

@section('content')
    <section
        class="mx-auto w-full max-w-md rounded-2xl border border-slate-800 bg-slate-900 p-6 shadow-xl shadow-black/20 sm:p-8"
        aria-labelledby="auth-title">
        <header class="mb-8">
            <h1 id="auth-title">Регистрация</h1>
            <p class="text-sm text-slate-400">Заполните данные, чтобы создать аккаунт.</p>
        </header>

        <form action="/signin" method="POST" class="flex flex-col gap-6">
            @csrf

            <div class="flex flex-col gap-2">
                <label for="name" class="text-sm font-medium text-slate-200">Имя</label>
                <input type="text" name="name" id="name" required autocomplete="name" placeholder="Ваше имя"
                    class="w-full rounded-lg border border-slate-700 bg-slate-950 px-4 py-3 text-base text-white placeholder:text-slate-500 focus:border-slate-400 focus:outline-2 focus:outline-offset-2 focus:outline-slate-400"
                    value="{{ old('name') }}">
                @error('name')
                    <p class="text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex flex-col gap-2">
                <label for="email" class="text-sm font-medium text-slate-200">Email</label>
                <input type="email" name="email" id="email" required autocomplete="email"
                    placeholder="you@example.com"
                    class="w-full rounded-lg border border-slate-700 bg-slate-950 px-4 py-3 text-base text-white placeholder:text-slate-500 focus:border-slate-400 focus:outline-2 focus:outline-offset-2 focus:outline-slate-400"
                    value="{{ old('email') }}">
                @error('email')
                    <p class="text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex flex-col gap-2">
                <label for="password" class="text-sm font-medium text-slate-200">Пароль</label>
                <input type="password" name="password" id="password" autocomplete="new-password"
                    placeholder="Придумайте пароль"
                    class="w-full rounded-lg border border-slate-700 bg-slate-950 px-4 py-3 text-base text-white placeholder:text-slate-500 focus:border-slate-400 focus:outline-2 focus:outline-offset-2 focus:outline-slate-400">
                @error('password')
                    <p class="text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <p>Уже есть аккаунт? <span><a href="login">Войти.</a></span></p>
            </div>

            <button type="submit"
                class="w-full cursor-pointer rounded-lg bg-slate-200 px-4 py-3 text-sm font-semibold text-slate-950 hover:bg-white focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-slate-400 active:bg-slate-300">
                Зарегистрироваться
            </button>
        </form>
    </section>
@endsection
