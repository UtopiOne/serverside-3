<?php

use App\Models\Article;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('articles', function (User $user): bool {
    return $user->can('viewAny', Article::class);
});
