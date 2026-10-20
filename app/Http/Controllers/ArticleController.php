<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function index(): View
    {
        $articles = Article::orderBy('published_at')->paginate(6);
        return view("index", ['articles' => $articles]);
    }

    public function create(): View
    {
        return view("articles.create");
    }

    public function show(Article $article): View
    {
        return view('articles.show', ['article' => $article]);
    }

    public function edit(Article $article): View
    {
        return view('articles.edit', ['article' => $article]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'published_at' => ['required', 'date'],
            'title' => ['required', 'string', 'max:255'],
            'preview_image' => ['required', 'string', 'max:255'],
            'full_image' => ['required', 'string', 'max:255'],
            'brief_description' => ['required', 'string'],
            'description' => ['required', 'string'],
        ]);

        $article = Article::create($data);

        return redirect()->route('articles.show', $article);
    }

    public function update(Request $request, Article $article): RedirectResponse
    {
        $data = $request->validate([
            'published_at' => ['required', 'date'],
            'title' => ['required', 'string', 'max:255'],
            'preview_image' => ['required', 'string', 'max:255'],
            'full_image' => ['required', 'string', 'max:255'],
            'brief_description' => ['required', 'string'],
            'description' => ['required', 'string'],
        ]);

        $article->update($data);

        return redirect()->route('articles.show', $article);
    }

    public function delete(Article $article): RedirectResponse
    {
        $article->delete();

        return redirect()->route('articles.index');
    }
}
