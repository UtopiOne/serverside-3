<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class MainController extends Controller
{
    function index()
    {
        $articles = Article::orderBy('published_at')->paginate(6);

        return view('index', ['articles' => $articles]);
    }
}
