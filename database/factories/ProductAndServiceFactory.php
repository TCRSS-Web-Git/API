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
            'order' => 1,
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (ProductAndService $service) {
            $title = $this->faker->sentence();

            $service->setTranslation('title', $title, 'en');
            $service->setTranslation('title', '(th) '.$title, 'th');
            $service->save();
        });
    }
}
