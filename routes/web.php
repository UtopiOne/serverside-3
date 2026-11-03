<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\MainController;
use App\Models\Article;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', [MainController::class, 'index']);

Route::resource('articles', ArticleController::class)->middleware('auth:sanctum')
    ->middlewareFor('index', 'can:viewAny,'.Article::class)
    ->middlewareFor('show', 'can:view,article')
    ->middlewareFor(['create', 'store'], 'can:create,'.Article::class)
    ->middlewareFor(['edit', 'update'], 'can:update,article')
    ->middlewareFor('destroy', 'can:delete,article');

Route::resource('article.comments', CommentController::class)
    ->only(['store', 'update', 'destroy'])
    ->scoped()
    ->middleware('auth:sanctum');

Route::get('/moderation/comments', [CommentController::class, 'index'])
    ->middleware(['auth:sanctum', 'can:viewAny,'.Comment::class])
    ->name('moderation.comments.index');

Route::patch('/moderation/comments/{comment}/approve', [CommentController::class, 'approve'])
    ->middleware('auth:sanctum')
    ->name('moderation.comments.approve');

Route::delete('/moderation/comments/{comment}', [CommentController::class, 'destroyPending'])
    ->middleware('auth:sanctum')
    ->name('moderation.comments.destroy');

Route::get('/about', function () {
    return view('about');
});

Route::get('/gallery', function (Request $request) {
    return view('gallery', ['image' => $request->query('image', 'full.jpeg')]);
});

Route::get('/contact', function () {
    $contacts = [
        'email' => 'contact@example.com',
        'telephone' => '+1 (555) 123-4567',
        'address' => '123 Main St, Anytown, USA',
    ];

    return view('contacts', ['contacts' => $contacts]);
});

Route::get('/signin', [AuthController::class, 'create']);
Route::post('/signin', [AuthController::class, 'registration']);

Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate']);

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
