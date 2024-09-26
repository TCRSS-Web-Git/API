<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Blog>
 */
class BlogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => Category::factory()->blog(),
            'slug' => $this->faker->slug(),
            'published_at' => $this->faker->randomElement([null, $this->faker->dateTimeBetween('-1 year', 'now')]),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (\App\Models\Blog $blog) {
            $title = $this->faker->sentence();
            $body = $this->faker->paragraphs(3, true);

            $blog->setTranslation('title', $title, 'en');
            $blog->setTranslation('title', '(th) '.$title, 'th');
            $blog->setTranslation('body', $body, 'en');
            $blog->setTranslation('body', '(th) '.$body, 'th');
            $blog->setTranslation('meta_title', $title, 'en');
            $blog->setTranslation('meta_title', '(th) '.$title, 'th');
            $metaDescription = mb_substr($body, 0, 250);
            $blog->setTranslation('meta_description', $metaDescription, 'en');
            $blog->setTranslation('meta_description', '(th) '.$metaDescription, 'th');
            $blog->save();
        });
    }
}
