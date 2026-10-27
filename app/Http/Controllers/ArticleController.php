<?php

namespace App\Http\Controllers;

use App\Mail\ArticlePosted;
use App\Models\Article;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function index(): View
    {
        $articles = Article::orderBy('published_at')->paginate(6);

        return view('index', ['articles' => $articles]);
    }

    public function create(): View
    {
        return view('articles.create');
    }

    public function show(Article $article): View
    {
        $comments = $article->comments()->with('user')
            ->latest('published_at')->orderByDesc('id')
            ->paginate(10, ['*'], 'comments_page')->fragment('comments');

        return view('articles.show', ['article' => $article, 'comments' => $comments]);
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

        $article = new Article($data);
        $article->author()->associate($request->user());
        $article->save();
        Mail::to($request->user()->email)->send(new ArticlePosted($article));

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

    public function destroy(Article $article): RedirectResponse
    {
        $article->delete();

        return redirect()->route('articles.index');
    }
}
