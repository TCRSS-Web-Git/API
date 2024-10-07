<?php

namespace Database\Factories;

use App\Enums\CategoryType;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => fake()->randomElement(CategoryType::cases()),
            'slug' => fake()->slug(),
            'sort' => fake()->numberBetween(0, 100),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Category $category) {
            $name = $this->faker->word();
            $description = $this->faker->paragraphs(2, true);

            $category->setTranslation('name', $name, 'en');
            $category->setTranslation('name', '(th) ' . $name, 'th');
            $category->setTranslation('description', $description, 'en');
            $category->setTranslation('description', '(th) ' . $description, 'th');
            $category->save();
        });
    }

    public function blog(): Factory
    {
        return $this->state(fn(array $attributes) => [
            'type' => CategoryType::BLOG,
        ]);
    }

    public function jobPost(): Factory
    {
        return $this->state(fn(array $attributes) => [
            'type' => CategoryType::CAREER,
        ]);
    }

    public function location(): Factory
    {
        return $this->state(fn(array $attributes) => [
            'type' => CategoryType::LOCATION,
        ]);
    }
}
