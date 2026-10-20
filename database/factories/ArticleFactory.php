<?php

namespace Database\Factories;

use App\Models\Article;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Article>
 */
class ArticleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'published_at' => fake()->date(),
            'title' => fake()->sentence(6),
            'preview_image' => 'preview.jpg',
            'full_image' => 'full.jpeg',
            'brief_description' => fake()->paragraph(),
            'description' => fake()->paragraph(4, true)
        ];
    }
}
