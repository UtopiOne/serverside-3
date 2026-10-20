<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MainController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;


Route::get('/', [MainController::class, 'index']);

Route::resource('articles', ArticleController::class);

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
