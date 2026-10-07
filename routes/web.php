<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('about');
});

Route::get('/about', function () {
    return view('about');
});

Route::get('/contact', function () {
    return view('contacts');
});
