<?php

use App\Events\NewArticleEvent;
use App\Models\Article;
use App\Models\Roles;
use App\Models\User;
use Illuminate\Broadcasting\BroadcastEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

test('creating an article broadcasts its title to article readers', function (): void {
    $moderatorRole = Roles::firstOrCreate(['name' => 'moderator']);
    $moderator = User::factory()->create(['role_id' => $moderatorRole->id]);
    $articleData = Article::factory()->raw(['title' => 'A newly posted article']);

    Event::fake([NewArticleEvent::class]);
    Queue::fake();

    $this->actingAs($moderator)
        ->post(route('articles.store'), $articleData)
        ->assertRedirect();

    $article = Article::firstOrFail();

    Event::assertDispatched(NewArticleEvent::class, function (NewArticleEvent $event) use ($article): bool {
        expect($event->broadcastOn()[0]->name)->toBe('private-articles');
        expect($event->broadcastWith())->toBe([
            'id' => $article->id,
            'title' => 'A newly posted article',
        ]);

        return true;
    });
});

test('only article readers can subscribe to new article broadcasts', function (): void {
    config()->set('broadcasting.default', 'pusher');
    config()->set('broadcasting.connections.pusher.key', 'test-key');
    config()->set('broadcasting.connections.pusher.secret', 'test-secret');
    config()->set('broadcasting.connections.pusher.app_id', 'test-app');
    require base_path('routes/channels.php');

    $request = [
        'socket_id' => '1234.5678',
        'channel_name' => 'private-articles',
    ];

    $this->post('/broadcasting/auth', $request)->assertRedirect(route('login'));

    $readerRole = Roles::firstOrCreate(['name' => 'reader']);
    $reader = User::factory()->create(['role_id' => $readerRole->id]);
    $this->actingAs($reader)->post('/broadcasting/auth', $request)->assertOk();

    $userWithoutRole = User::factory()->create();
    $this->actingAs($userWithoutRole)->post('/broadcasting/auth', $request)->assertForbidden();
});

test('creating an article queues the broadcast for the worker', function (): void {
    $moderatorRole = Roles::firstOrCreate(['name' => 'moderator']);
    $moderator = User::factory()->create(['role_id' => $moderatorRole->id]);
    Queue::fake();

    $this->actingAs($moderator)
        ->post(route('articles.store'), Article::factory()->raw())
        ->assertRedirect();

    Queue::assertPushed(BroadcastEvent::class);
});

test('article readers get the notification mount with public Pusher settings', function (): void {
    config()->set('broadcasting.default', 'pusher');
    config()->set('broadcasting.connections.pusher.key', 'test-key');
    config()->set('broadcasting.connections.pusher.options.cluster', 'mt1');

    $this->get('/about')->assertDontSee('article-notifications');

    $readerRole = Roles::firstOrCreate(['name' => 'reader']);
    $reader = User::factory()->create(['role_id' => $readerRole->id]);

    $this->actingAs($reader)->get('/about')
        ->assertSee('<meta name="csrf-token" content="', false)
        ->assertSee('id="article-notifications"', false)
        ->assertSee('data-pusher-key="test-key"', false)
        ->assertSee('data-pusher-cluster="mt1"', false);
});
