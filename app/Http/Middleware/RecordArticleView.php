<?php

namespace App\Http\Middleware;

use App\Models\Article;
use App\Models\ArticleView;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RecordArticleView
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $article = $request->route('article');

        if ($request->isMethod('GET') && $response->isSuccessful() && $article instanceof Article) {
            ArticleView::create(['article_id' => $article->id, 'url' => $request->url()]);
        }

        return $response;
    }
}
