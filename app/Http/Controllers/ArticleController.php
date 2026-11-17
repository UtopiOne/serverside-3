<?php

namespace App\Http\Controllers;

use App\Events\NewArticleEvent;
use App\Jobs\VeryLongJob;
use App\Mail\ArticlePosted;
use App\Models\Article;
use App\Models\Comment;
use App\Models\User;
use App\Notifications\ArticlePostedNotification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function index(Request $request): View
    {
        $page = max(1, $request->integer('page', 1));
        $version = Cache::get('articles:version', 'initial');
        $articlePage = Cache::remember("articles:v2:{$version}:page:{$page}", 60, function () use ($page): array {
            $articles = Article::orderBy('published_at')->paginate(6, ['*'], 'page', $page);

            return [
                'items' => $articles->getCollection()->map(fn (Article $article): array => $article->getAttributes())->all(),
                'total' => $articles->total(),
            ];
        });
        $articles = new LengthAwarePaginator(Article::hydrate($articlePage['items']), $articlePage['total'], 6, $page, [
            'path' => $request->url(),
            'pageName' => 'page',
        ]);

        return view('index', ['articles' => $articles]);
    }

    public function create(): View
    {
        return view('articles.create');
    }

    public function show(Request $request, Article $article): View
    {
        $articleData = Cache::remember("article:v2:{$article->id}", 60, function () use ($article): array {
            $article->load('author');

            return [
                'attributes' => $article->getAttributes(),
                'author' => $article->author?->only(['id', 'name']),
            ];
        });
        $article = (new Article)->newFromBuilder($articleData['attributes']);
        $article->setRelation('author', $articleData['author'] === null
            ? null
            : (new User)->newFromBuilder($articleData['author']));

        $request->user()->unreadNotifications()
            ->where('type', ArticlePostedNotification::class)
            ->where('data->article_id', $article->id)
            ->update(['read_at' => now()]);
        $userId = $request->user()->getAuthIdentifier();
        $commentsPage = max(1, $request->integer('comments_page', 1));
        $commentsVersion = Cache::get("article:{$article->id}:comments:version", 'initial');
        $commentPage = Cache::remember("article:v3:{$article->id}:comments:version:{$commentsVersion}:user:{$userId}:page:{$commentsPage}", 60, function () use ($request, $article, $commentsPage): array {
            $comments = $article->comments()
                ->where(function (Builder $query) use ($request): void {
                    $query->where('approved', true)
                        ->orWhere('user_id', $request->user()?->getAuthIdentifier());
                })
                ->latest('published_at')->orderByDesc('id')
                ->paginate(10, ['*'], 'comments_page', $commentsPage)->fragment('comments');

            return [
                'items' => $comments->getCollection()->map(fn (Comment $comment): array => $comment->getAttributes())->all(),
                'total' => $comments->total(),
            ];
        });

        $comments = new LengthAwarePaginator(Comment::hydrate($commentPage['items'])->load('user'), $commentPage['total'], 10, $commentsPage, [
            'path' => $request->url(),
            'pageName' => 'comments_page',
        ]);
        $comments->fragment('comments');

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
        User::whereHas('role', function (Builder $query): void {
            $query->whereIn('name', ['reader', 'moderator']);
        })->chunkById(100, function (Collection $users) use ($article): void {
            Notification::send($users, new ArticlePostedNotification($article));
        });
        NewArticleEvent::dispatch($article);
        // Mail::to($request->user()->email)->send(new ArticlePosted($article));
        VeryLongJob::dispatch($article, $request->user()->email);

        Cache::put('articles:version', (string) Str::uuid(), 60);

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
        Cache::flush();

        return redirect()->route('articles.show', $article);
    }

    public function destroy(Article $article): RedirectResponse
    {
        $article->delete();
        Cache::flush();

        return redirect()->route('articles.index');
    }
}
