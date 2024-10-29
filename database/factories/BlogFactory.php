<?php

namespace Database\Factories;

use App\Helper\Helper;
use App\Models\Blog;
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
            $metaDescription = mb_substr($body, 0, 150);
            $blog->setTranslation('meta_description', $metaDescription, 'en');
            $blog->setTranslation('meta_description', '(th) '.$metaDescription, 'th');
            $blog->save();
        });
    }

    public function withCover(): static
    {
        return $this->afterCreating(function (Blog $blog) {
            $coverUrl = Helper::getPlaceholderImageUrl($blog->getTranslation('title', 'en'), 1818, 1212);
            $blog->addMediaFromUrl($coverUrl)
                ->toMediaCollection(Blog::MEDIA_COLLECTION_COVER);
        });
    }

    public function withThumbnail(): static
    {
        return $this->afterCreating(function (Blog $blog) {
            $coverUrl = Helper::getPlaceholderImageUrl($blog->getTranslation('title', 'en'), 886, 572);
            $blog->addMediaFromUrl($coverUrl)
                ->toMediaCollection(Blog::MEDIA_COLLECTION_THUMBNAIL);
        });
    }
}
