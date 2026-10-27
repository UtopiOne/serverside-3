<?php

use App\Models\Article;
use App\Models\Comment;
use App\Models\Roles;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    $this->reader = User::factory()->create();
    $this->reader->role()->associate(Roles::firstOrCreate(['name' => 'reader']));
    $this->reader->save();
    $this->article = Article::factory()->create();
});

test('article pages show only their comments and escape comment text', function (): void {
    Comment::factory()->for($this->article)->for($this->reader)->create([
        'contents' => '<script>alert("comment")</script>',
    ]);
    Comment::factory()->create(['contents' => 'Comment on another article']);

    $this->actingAs($this->reader)->get(route('articles.show', $this->article))
        ->assertOk()
        ->assertSee($this->reader->name)
        ->assertSee('<script>alert("comment")</script>')
        ->assertDontSee('<script>alert("comment")</script>', false)
        ->assertDontSee('Comment on another article')
        ->assertSee('Опубликовать комментарий')
        ->assertSee('Редактировать комментарий')
        ->assertDontSee('Удалить комментарий');
});

test('article pages show an empty comments state', function (): void {
    $this->actingAs($this->reader)->get(route('articles.show', $this->article))
        ->assertOk()->assertSee('Комментариев пока нет.');
});

test('readers can post comments with server assigned ownership', function (): void {
    $other = User::factory()->create();

    $this->actingAs($this->reader)
        ->post(route('article.comments.store', $this->article), [
            'contents' => 'A new comment',
            'user_id' => $other->id,
            'article_id' => Article::factory()->create()->id,
        ])
        ->assertRedirect(route('articles.show', $this->article).'#comments');

    $this->assertDatabaseHas('comments', [
        'contents' => 'A new comment',
        'user_id' => $this->reader->id,
        'article_id' => $this->article->id,
    ]);
});

test('comment validation is shown beside the posting form', function (): void {
    $this->actingAs($this->reader)->followingRedirects()
        ->from(route('articles.show', $this->article))
        ->post(route('article.comments.store', $this->article), ['contents' => ''])
        ->assertOk()->assertSee('comment-contents-error', false);

    $this->assertDatabaseCount('comments', 0);
});

test('authors can edit their comments but other readers cannot', function (): void {
    $comment = Comment::factory()->for($this->article)->for($this->reader)->create();
    $url = route('article.comments.update', [$this->article, $comment]);

    $this->actingAs($this->reader)->put($url, ['contents' => 'Updated comment'])
        ->assertRedirect(route('articles.show', $this->article).'#comments');
    expect($comment->fresh()->contents)->toBe('Updated comment');

    $other = User::factory()->create(['role_id' => $this->reader->role_id]);
    $this->actingAs($other)->put($url, ['contents' => 'Forbidden change'])->assertForbidden();
    $this->get(route('articles.show', $this->article))
        ->assertDontSee('Редактировать комментарий');
});

test('only moderators can delete comments', function (): void {
    $comment = Comment::factory()->for($this->article)->for($this->reader)->create();
    $url = route('article.comments.destroy', [$this->article, $comment]);
    $this->actingAs($this->reader)->delete($url)->assertForbidden();

    $moderator = User::factory()->create();
    $moderator->role()->associate(Roles::firstOrCreate(['name' => 'moderator']));
    $moderator->save();
    $this->actingAs($moderator)->get(route('articles.show', $this->article))
        ->assertOk()->assertSee('Удалить комментарий');
    $this->delete($url)->assertRedirect(route('articles.show', $this->article).'#comments');
    $this->assertModelMissing($comment);
});

test('comments cannot be changed through another article', function (): void {
    $comment = Comment::factory()->for($this->reader)->create();
    $this->actingAs($this->reader)
        ->put(route('article.comments.update', [$this->article, $comment]), ['contents' => 'Wrong article'])
        ->assertNotFound();
});

test('guests cannot post comments', function (): void {
    $this->post(route('article.comments.store', $this->article), ['contents' => 'Guest comment'])
        ->assertRedirect(route('login'));
    $this->assertDatabaseCount('comments', 0);
});

test('comments are paginated on the article page', function (): void {
    Comment::factory()->count(11)->for($this->article)->for($this->reader)->create();

    $this->actingAs($this->reader)->get(route('articles.show', $this->article))
        ->assertOk()
        ->assertViewHas('comments', fn ($comments): bool => $comments->count() === 10 && $comments->total() === 11)
        ->assertSee('comments_page=2', false);

    $this->get(route('articles.show', [$this->article, 'comments_page' => 2]))
        ->assertOk()
        ->assertViewHas('comments', fn ($comments): bool => $comments->count() === 1);
});

test('edit validation preserves input only in the failing comment form', function (): void {
    $comment = Comment::factory()->for($this->article)->for($this->reader)->create();
    $other = Comment::factory()->for($this->article)->for($this->reader)->create();
    $invalid = str_repeat('x', 1024);

    $this->actingAs($this->reader)->followingRedirects()
        ->from(route('articles.show', $this->article))
        ->put(route('article.comments.update', [$this->article, $comment]), ['contents' => $invalid])
        ->assertOk()
        ->assertSee('comment-edit-error-'.$comment->id, false)
        ->assertDontSee('comment-edit-error-'.$other->id, false)
        ->assertDontSee('comment-contents-error', false)
        ->assertSee($invalid);
});
