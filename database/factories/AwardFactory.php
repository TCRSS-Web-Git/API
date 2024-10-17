<?php

namespace Database\Factories;

use App\Models\Award;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Award>
 */
class AwardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order' => 0,
            'published_at' => $this->faker->randomElement([null, $this->faker->dateTimeBetween('-1 year', 'now')]),
        ];
    }

    public function published(): static
    {
        return $this->state([
            'published_at' => now(),
        ]);
    }

    public function draft(): static
    {
        return $this->state([
            'published_at' => $this->faker->randomElement([null, $this->faker->dateTimeBetween('+1 day', '+1 year')]),
        ]);
    }

    public function configure()
    {
        return $this->afterCreating(function (Award $award) {
            $title = $this->faker->sentence();
            $description = $this->faker->paragraphs(3, true);

            $award->setTranslation('title', $title, 'en');
            $award->setTranslation('title', '(th) '.$title, 'th');
            $award->setTranslation('description', $description, 'en');
            $award->setTranslation('description', '(th) '.$description, 'th');
            $award->save();
        });
    }
}
