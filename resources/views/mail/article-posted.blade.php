<div>
    <p>Your article was posted successfully</p>
    <h1>{{ $article->title }}</h1>
    <a href="{{ route('articles.show', $article) }}">Read the article</a>
</div>
