<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MainController extends Controller
{
    function index()
    {
        $json = file_get_contents(public_path('articles.json'));
        $articles = json_decode($json, true);

        return view('index', ['articles' => $articles]);
    }
}
