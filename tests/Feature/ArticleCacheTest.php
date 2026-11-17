<?php

use App\Events\NewArticleEvent;
use App\Models\Article;
use App\Models\Comment;
use App\Models\Roles;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    config()->set('cache.default', 'database');
});

test('article list cache keeps pagination pages separate', function (): void {
    $readerRole = Roles::firstOrCreate(['name' => 'reader']);
    $reader = User::factory()->create(['role_id' => $readerRole->id]);

    for ($day = 0; $day < 7; $day++) {
        $article = Article::factory()->create(['published_at' => now()->addDays($day)]);
    }

    $this->actingAs($reader)->get(route('articles.index'))
        ->assertOk()
        ->assertViewHas('articles', fn ($articles): bool => $articles->currentPage() === 1 && $articles->count() === 6);

    $this->get(route('articles.index', ['page' => 2]))
        ->assertOk()
        ->assertViewHas('articles', fn ($articles): bool => $articles->currentPage() === 2 && $articles->count() === 1 && $articles->first()->is($article));
});

test('creating an article refreshes the cached list', function (): void {
    $readerRole = Roles::firstOrCreate(['name' => 'reader']);
    $moderatorRole = Roles::firstOrCreate(['name' => 'moderator']);
    $reader = User::factory()->create(['role_id' => $readerRole->id]);
    $moderator = User::factory()->create(['role_id' => $moderatorRole->id]);

    $this->actingAs($reader)->get(route('articles.index'))
        ->assertOk()
        ->assertDontSee('Fresh article');

    Event::fake([NewArticleEvent::class]);
    Queue::fake();

    $this->actingAs($moderator)
        ->post(route('articles.store'), Article::factory()->raw(['title' => 'Fresh article']))
        ->assertRedirect();

    $this->actingAs($reader)->get(route('articles.index'))
        ->assertOk()
        ->assertSee('Fresh article');
});

test('cached comments remain private to the signed-in reader', function (): void {
    $readerRole = Roles::firstOrCreate(['name' => 'reader']);
    $firstReader = User::factory()->create(['role_id' => $readerRole->id]);
    $secondReader = User::factory()->create(['role_id' => $readerRole->id]);
    $article = Article::factory()->create();

    Comment::factory()->for($article)->for($firstReader)->create(['contents' => 'First pending comment']);
    Comment::factory()->for($article)->for($secondReader)->create(['contents' => 'Second pending comment']);

    $this->actingAs($firstReader)->get(route('articles.show', $article))
        ->assertOk()
        ->assertSee('First pending comment')
        ->assertDontSee('Second pending comment');

    $this->actingAs($secondReader)->get(route('articles.show', $article))
        ->assertOk()
        ->assertSee('Second pending comment')
        ->assertDontSee('First pending comment');
});

test('a newly posted comment appears after the article page was cached', function (): void {
    $readerRole = Roles::firstOrCreate(['name' => 'reader']);
    $reader = User::factory()->create(['role_id' => $readerRole->id]);
    $article = Article::factory()->create();

    $this->actingAs($reader)->get(route('articles.show', $article))
        ->assertOk()
        ->assertSee('Комментариев пока нет.');

    $this->post(route('article.comments.store', $article), ['contents' => 'My new comment'])
        ->assertRedirect(route('articles.show', $article).'#comments');

    $this->get(route('articles.show', $article))
        ->assertOk()
        ->assertSee('My new comment')
        ->assertSee('Ожидает модерации');
});

test('edited and deleted comments replace cached article comments', function (): void {
    $readerRole = Roles::firstOrCreate(['name' => 'reader']);
    $reader = User::factory()->create(['role_id' => $readerRole->id]);
    $article = Article::factory()->create();
    $comment = Comment::factory()->for($article)->for($reader)->create([
        'contents' => 'Original comment',
        'approved' => true,
    ]);

    $this->actingAs($reader)->get(route('articles.show', $article))
        ->assertOk()
        ->assertSee('Original comment');

    $comment->update(['contents' => 'Edited comment']);
    $this->get(route('articles.show', $article))
        ->assertOk()
        ->assertSee('Edited comment')
        ->assertDontSee('Original comment');

    $comment->delete();
    $this->get(route('articles.show', $article))
        ->assertOk()
        ->assertDontSee('Edited comment')
        ->assertSee('Комментариев пока нет.');
});

test('article page caches the article with its author', function (): void {
    $readerRole = Roles::firstOrCreate(['name' => 'reader']);
    $reader = User::factory()->create(['role_id' => $readerRole->id]);
    $author = User::factory()->create();
    $article = Article::factory()->for($author, 'author')->create();

    $this->actingAs($reader)->get(route('articles.show', $article))->assertOk();

    $cachedArticle = Cache::get("article:v2:{$article->id}");
    expect($cachedArticle)->toBeArray()
        ->and($cachedArticle['attributes']['id'])->toBe($article->id)
        ->and($cachedArticle['author']['name'])->toBe($author->name);
});

test('article pages can be read repeatedly from the database cache', function (): void {
    $readerRole = Roles::firstOrCreate(['name' => 'reader']);
    $reader = User::factory()->create(['role_id' => $readerRole->id]);
    $article = Article::factory()->create();
    Comment::factory()->for($article)->for($reader)->create(['approved' => true]);
    Cache::store('database')->forever("article:{$article->id}", $article);

    $this->actingAs($reader)->get(route('articles.index'))->assertOk();
    $this->get(route('articles.index'))->assertOk();
    $this->get(route('articles.show', $article))->assertOk();
    $this->get(route('articles.show', $article))->assertOk();
});
