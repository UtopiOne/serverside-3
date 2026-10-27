<?php

use App\Models\Article;
use App\Models\Comment;
use App\Models\Roles;
use App\Models\User;
use Database\Seeders\ArticleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('article seeding reuses existing authors and commenters', function (): void {
    $moderator = Roles::firstOrCreate(['name' => 'moderator']);
    $reader = Roles::firstOrCreate(['name' => 'reader']);
    $author = User::factory()->create(['role_id' => $moderator->id]);
    $commenter = User::factory()->create(['role_id' => $reader->id]);

    $this->seed(ArticleSeeder::class);

    $this->assertDatabaseCount('articles', 20);
    $this->assertDatabaseCount('comments', 60);
    $this->assertDatabaseCount('users', 2);
    expect($commenter->fresh()->role_id)->toBe($reader->id);

    foreach (Article::with(['author', 'comments.user'])->get() as $article) {
        expect($article->author->is($author))->toBeTrue()
            ->and($article->comments)->toHaveCount(3);

        foreach ($article->comments as $comment) {
            expect($comment->user_id)->toBeIn([$author->id, $commenter->id]);
        }
    }
});

test('article seeding can bootstrap authors in an empty database', function (): void {
    $this->seed(ArticleSeeder::class);

    $this->assertDatabaseCount('articles', 20);
    $this->assertDatabaseCount('comments', 60);
    expect(Article::whereNull('user_id')->exists())->toBeFalse()
        ->and(Comment::whereNull('user_id')->exists())->toBeFalse()
        ->and(User::firstOrFail()->role->name)->toBe('moderator');
});
