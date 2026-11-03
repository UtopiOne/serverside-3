<?php

use App\Models\Article;
use App\Models\Comment;
use App\Models\Roles;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guests are sent to login and readers cannot see the moderation queue', function (): void {
    $this->get('/moderation/comments')->assertRedirect(route('login'));

    $reader = User::factory()->create();
    $reader->role()->associate(Roles::firstOrCreate(['name' => 'reader']));
    $reader->save();

    $this->actingAs($reader)->get('/moderation/comments')->assertForbidden();
});

test('moderators see only pending comments with their author and article', function (): void {
    $moderator = User::factory()->create();
    $moderator->role()->associate(Roles::firstOrCreate(['name' => 'moderator']));
    $moderator->save();

    $article = Article::factory()->create(['title' => 'Article awaiting review']);
    $pending = Comment::factory()->for($article)->create(['contents' => '<script>Pending comment</script>']);
    Comment::factory()->for($article)->create(['contents' => 'Already approved', 'approved' => true]);

    $this->actingAs($moderator)->get('/moderation/comments')
        ->assertOk()
        ->assertSee($pending->user->name)
        ->assertSee('Article awaiting review')
        ->assertSee('<script>Pending comment</script>')
        ->assertSee('Одобрить')
        ->assertDontSee('<script>Pending comment</script>', false)
        ->assertDontSee('Already approved');
});

test('moderation queue paginates pending comments and handles an empty queue', function (): void {
    $moderator = User::factory()->create();
    $moderator->role()->associate(Roles::firstOrCreate(['name' => 'moderator']));
    $moderator->save();

    $this->actingAs($moderator)->get('/moderation/comments')
        ->assertOk()->assertSee('Нет комментариев на проверке.');

    Comment::factory()->count(11)->create();

    $this->get('/moderation/comments')
        ->assertOk()
        ->assertViewHas('comments', fn ($comments): bool => $comments->count() === 10 && $comments->total() === 11)
        ->assertSee('page=2', false);
});

test('only moderators can approve a pending comment', function (): void {
    $article = Article::factory()->create();
    $comment = Comment::factory()->for($article)->create(['contents' => 'Waiting for approval']);
    $approvalUrl = "/moderation/comments/{$comment->id}/approve";

    $this->patch($approvalUrl)->assertRedirect(route('login'));

    $reader = User::factory()->create();
    $reader->role()->associate(Roles::firstOrCreate(['name' => 'reader']));
    $reader->save();
    $this->actingAs($reader)->patch($approvalUrl)->assertForbidden();
    expect($comment->fresh()->approved)->toBeFalse();

    $moderator = User::factory()->create();
    $moderator->role()->associate(Roles::firstOrCreate(['name' => 'moderator']));
    $moderator->save();

    $this->actingAs($moderator)->get('/moderation/comments')
        ->assertOk()->assertSee('Waiting for approval');

    $this->patch($approvalUrl, ['approved' => false])
        ->assertRedirect('/moderation/comments');

    expect($comment->fresh()->approved)->toBeTrue();
    $this->get('/moderation/comments')->assertOk()->assertDontSee('Waiting for approval');
    $this->get(route('articles.show', $article))->assertOk()->assertSee('Waiting for approval');
});

test('only moderators can delete a pending comment from the queue', function (): void {
    $reader = User::factory()->create();
    $reader->role()->associate(Roles::firstOrCreate(['name' => 'reader']));
    $reader->save();

    $comment = Comment::factory()->for($reader)->create(['contents' => 'Comment to remove']);
    $deleteUrl = "/moderation/comments/{$comment->id}";

    $this->delete($deleteUrl)->assertRedirect(route('login'));
    $this->actingAs($reader)->delete($deleteUrl)->assertForbidden();
    $this->assertModelExists($comment);

    $moderator = User::factory()->create();
    $moderator->role()->associate(Roles::firstOrCreate(['name' => 'moderator']));
    $moderator->save();

    $this->actingAs($moderator)->get('/moderation/comments')
        ->assertOk()->assertSee('Comment to remove')->assertSee('Удалить');

    $this->delete($deleteUrl)->assertRedirect('/moderation/comments');
    $this->assertModelMissing($comment);
    $this->get('/moderation/comments')->assertOk()->assertDontSee('Comment to remove');
});

test('the moderation delete action does not remove approved comments', function (): void {
    $moderator = User::factory()->create();
    $moderator->role()->associate(Roles::firstOrCreate(['name' => 'moderator']));
    $moderator->save();

    $approvedComment = Comment::factory()->create(['approved' => true]);

    $this->actingAs($moderator)
        ->delete("/moderation/comments/{$approvedComment->id}")
        ->assertNotFound();

    $this->assertModelExists($approvedComment);
});
