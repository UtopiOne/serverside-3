<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Comment;
use App\Models\Roles;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArticleSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function (): void {
            $this->call(RolesSeeder::class);

            $authors = User::whereHas('role', function (Builder $query): void {
                $query->where('name', 'moderator');
            })->get();

            if ($authors->isEmpty()) {
                $moderator = Roles::where('name', 'moderator')->firstOrFail();
                $authors = User::factory()->count(1)->create(['role_id' => $moderator->id]);
            }

            $commenters = User::all();

            Article::factory()->count(5)
                ->state(fn(): array => ['user_id' => $authors->random()->id])
                ->has(Comment::factory()->count(3)->recycle($commenters), 'comments')
                ->create();
        });
    }
}
