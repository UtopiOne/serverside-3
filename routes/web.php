<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MainController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Article;


Route::get('/', [MainController::class, 'index']);

Route::resource('articles', ArticleController::class)->middleware('auth:sanctum')
    ->middlewareFor('index', 'can:viewAny,' . Article::class)
    ->middlewareFor('show', 'can:view,article')
    ->middlewareFor(['create', 'store'], 'can:create,' . Article::class)
    ->middlewareFor(['edit', 'update'], 'can:update,article')
    ->middlewareFor('destroy', 'can:delete,article');

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

Route::get("/signin", [AuthController::class, 'create']);
Route::post('/signin', [AuthController::class, 'registration']);

Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate']);

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
