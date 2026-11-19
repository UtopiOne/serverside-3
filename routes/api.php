<?php

use App\Http\Controllers\API\ArticleController;
use App\Http\Controllers\API\AuthController;
use App\Models\Article;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'registration']);
Route::post('/login', [AuthController::class, 'authenticate']);
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth:sanctum');

Route::name('api.')->group(function (): void {
    Route::apiResource('articles', ArticleController::class)
        ->middleware('auth:sanctum')
        ->middlewareFor('index', 'can:viewAny,'.Article::class)
        ->middlewareFor('show', ['can:view,article', 'record.article.view'])
        ->middlewareFor(['store'], 'can:create,'.Article::class)
        ->middlewareFor(['update'], 'can:update,article')
        ->middlewareFor('destroy', 'can:delete,article');
});
