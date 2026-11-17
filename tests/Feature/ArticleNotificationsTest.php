<?php

use App\Models\Article;
use App\Models\Roles;
use App\Models\User;
use App\Notifications\ArticlePostedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

test('new articles notify registered readers and moderators', function (): void {
    $readerRole = Roles::firstOrCreate(['name' => 'reader']);
    $moderatorRole = Roles::firstOrCreate(['name' => 'moderator']);
    $author = User::factory()->create(['role_id' => $moderatorRole->id]);
    $reader = User::factory()->create(['role_id' => $readerRole->id]);
    $otherModerator = User::factory()->create(['role_id' => $moderatorRole->id]);
    $userWithoutRole = User::factory()->create();

    Queue::fake();
    Event::fake();

    $this->actingAs($author)
        ->post(route('articles.store'), Article::factory()->raw(['title' => 'A new article']))
        ->assertRedirect();

    $article = Article::firstOrFail();

    foreach ([$author, $reader, $otherModerator] as $recipient) {
        $notification = $recipient->notifications()->sole();
        expect($notification->type)->toBe(ArticlePostedNotification::class)
            ->and($notification->data)->toBe([
                'article_id' => $article->id,
                'article_title' => 'A new article',
            ]);
    }

    expect($userWithoutRole->notifications()->count())->toBe(0);
});

test('the header shows only the signed-in readers unread article notifications', function (): void {
    $readerRole = Roles::firstOrCreate(['name' => 'reader']);
    $reader = User::factory()->create(['role_id' => $readerRole->id]);
    $otherReader = User::factory()->create(['role_id' => $readerRole->id]);
    $unreadArticle = Article::factory()->create(['title' => 'Unread article title']);
    $readArticle = Article::factory()->create(['title' => 'Read article title']);
    $otherArticle = Article::factory()->create(['title' => 'Other reader title']);

    $reader->notify(new ArticlePostedNotification($unreadArticle));
    $reader->notify(new ArticlePostedNotification($readArticle));
    $reader->unreadNotifications()->where('data->article_id', $readArticle->id)->firstOrFail()->markAsRead();
    $otherReader->notify(new ArticlePostedNotification($otherArticle));

    $this->get('/about')->assertDontSee('Уведомления');

    $this->actingAs($reader)->get('/about')
        ->assertOk()
        ->assertSee('Уведомления')
        ->assertSee('Unread article title')
        ->assertSee(route('articles.show', $unreadArticle), false)
        ->assertDontSee('Read article title')
        ->assertDontSee('Other reader title');
});

test('opening an article marks only its own notifications as read for that reader', function (): void {
    $readerRole = Roles::firstOrCreate(['name' => 'reader']);
    $reader = User::factory()->create(['role_id' => $readerRole->id]);
    $otherReader = User::factory()->create(['role_id' => $readerRole->id]);
    $openedArticle = Article::factory()->create();
    $otherArticle = Article::factory()->create();

    $reader->notify(new ArticlePostedNotification($openedArticle));
    $reader->notify(new ArticlePostedNotification($otherArticle));
    $otherReader->notify(new ArticlePostedNotification($openedArticle));

    $this->actingAs($reader)->get(route('articles.show', $openedArticle))->assertOk();

    expect($reader->unreadNotifications()->where('data->article_id', $openedArticle->id)->count())->toBe(0)
        ->and($reader->unreadNotifications()->where('data->article_id', $otherArticle->id)->count())->toBe(1)
        ->and($otherReader->unreadNotifications()->where('data->article_id', $openedArticle->id)->count())->toBe(1);
});
