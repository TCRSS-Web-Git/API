<?php

namespace Database\Factories;

use App\Models\ProductAndService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductAndService>
 */
class ProductAndServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'published_at' => $this->faker->randomElement([null, $this->faker->dateTimeBetween('-1 year', 'now')]),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (ProductAndService $blog) {
            $title = $this->faker->sentence();

            $blog->setTranslation('title', $title, 'en');
            $blog->setTranslation('title', '(th) '.$title, 'th');
            $blog->save();
        });
    }
}
