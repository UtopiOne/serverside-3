<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Comment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $comments = Comment::where('approved', false)
            ->with(['user', 'article'])
            ->orderBy('created_at')
            ->orderBy('id')
            ->paginate(10);

        return view('moderation.comments', ['comments' => $comments]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Article $article): RedirectResponse
    {
        Gate::authorize('view', $article);
        Gate::authorize('create', Comment::class);

        $data = $request->validateWithBag('createComment', [
            'contents' => ['required', 'string', 'max:1023'],
        ]);

        $comment = new Comment($data);
        $comment->published_at = now();
        $comment->article()->associate($article);
        $comment->user()->associate($request->user());
        $comment->save();

        return redirect()->to(route('articles.show', $article).'#comments');
    }

    /**
     * Display the specified resource.
     */
    public function show(Comment $comment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Comment $comment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Article $article, Comment $comment): RedirectResponse
    {
        Gate::authorize('view', $article);
        Gate::authorize('update', $comment);

        $data = $request->validateWithBag('updateComment'.$comment->id, [
            'contents' => ['required', 'string', 'max:1023'],
        ]);

        $comment->update($data);

        return redirect()->to(route('articles.show', $article).'#comments');
    }

    public function approve(Comment $comment): RedirectResponse
    {
        Gate::authorize('approve', $comment);

        $comment->approved = true;
        $comment->save();

        return redirect()->route('moderation.comments.index');
    }

    public function destroyPending(Comment $comment): RedirectResponse
    {
        Gate::authorize('viewAny', Comment::class);
        Gate::authorize('delete', $comment);
        abort_if($comment->approved, 404);

        $comment->delete();

        return redirect()->route('moderation.comments.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Article $article, Comment $comment): RedirectResponse
    {
        Gate::authorize('view', $article);
        Gate::authorize('delete', $comment);

        $comment->delete();

        return redirect()->to(route('articles.show', $article).'#comments');
    }
}
