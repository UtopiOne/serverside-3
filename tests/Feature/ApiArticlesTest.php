<?php

use App\Models\Article;
use App\Models\Roles;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

uses(RefreshDatabase::class);

test('API registration creates a reader and returns a bearer token', function (): void {
    $readerRole = Roles::firstOrCreate(['name' => 'reader']);

    $this->postJson('/api/register', [
        'name' => 'New reader',
        'email' => 'new-reader@example.test',
        'password' => 'secret-pass-123',
    ])->assertCreated()
        ->assertJsonPath('user.role_id', $readerRole->id)
        ->assertJsonStructure(['token']);

    $this->assertDatabaseHas('users', [
        'email' => 'new-reader@example.test',
        'role_id' => $readerRole->id,
    ]);
});

test('API login accepts email and password and logout revokes the bearer token', function (): void {
    $readerRole = Roles::firstOrCreate(['name' => 'reader']);
    $reader = User::factory()->create(['role_id' => $readerRole->id]);

    $token = $this->postJson('/api/login', [
        'email' => $reader->email,
        'password' => 'password',
    ])->assertOk()->json('token');

    $this->withToken($token)->getJson('/api/articles')->assertOk();
    $this->postJson('/api/logout')->assertOk();
    Auth::forgetGuards();
    $this->getJson('/api/articles')->assertUnauthorized();
});

test('API article show returns JSON and records an authorized view', function (): void {
    $readerRole = Roles::firstOrCreate(['name' => 'reader']);
    $reader = User::factory()->create(['role_id' => $readerRole->id]);
    $article = Article::factory()->create();
    $token = $reader->createToken('test')->plainTextToken;

    $this->withToken($token)->getJson(route('api.articles.show', $article))
        ->assertOk()
        ->assertJsonPath('article.id', $article->id)
        ->assertJsonStructure(['comments']);

    $this->assertDatabaseHas('article_views', ['article_id' => $article->id]);
});

test('API article show does not record a view without authentication', function (): void {
    $article = Article::factory()->create();

    $this->getJson(route('api.articles.show', $article))->assertUnauthorized();
    $this->assertDatabaseCount('article_views', 0);
});

test('API article show rejects a signed-in user without an allowed role', function (): void {
    $user = User::factory()->create();
    $article = Article::factory()->create();
    $token = $user->createToken('test')->plainTextToken;

    $this->withToken($token)->getJson(route('api.articles.show', $article))
        ->assertForbidden();

    $this->assertDatabaseCount('article_views', 0);
});

test('web article show also rejects a signed-in user without an allowed role', function (): void {
    $user = User::factory()->create();
    $article = Article::factory()->create();

    $this->actingAs($user)->get(route('articles.show', $article))
        ->assertForbidden();

    $this->assertDatabaseCount('article_views', 0);
});
