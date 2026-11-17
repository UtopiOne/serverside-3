<?php

namespace App\Providers;

use App\Models\Article;
use App\Notifications\ArticlePostedNotification;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View as ViewContract;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.app', function (ViewContract $view): void {
            $user = auth()->user();

            $notifications = $user && Gate::forUser($user)->allows('viewAny', Article::class)
                ? $user->unreadNotifications()->where('type', ArticlePostedNotification::class)->latest()->get()
                : collect();

            $view->with('unreadArticleNotifications', $notifications);
        });
    }
}
