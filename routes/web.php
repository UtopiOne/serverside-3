<?php

use App\Http\Controllers\MainController;
use Illuminate\Support\Facades\Route;



Route::get('/', [MainController::class, 'index']);

Route::get('/about', function () {
    return view('about');
});

Route::get('/contact', function () {
    $contacts = [
        'email' => 'contact@example.com',
        'telephone' => '+1 (555) 123-4567',
        'address' => '123 Main St, Anytown, USA',
    ];

    return view('contacts', ['contacts' => $contacts]);
});
