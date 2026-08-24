<?php

namespace Database\Factories;

use App\Helper\Helper;
use App\Models\Executive;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Executive>
 */
class ExecutiveFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'group_order' => 0,
            'order' => 0,
            'published_at' => $this->faker->randomElement([null, $this->faker->dateTimeBetween('-1 year', 'now')]),
        ];
    }

    public function published(): static
    {
        return $this->state([
            'published_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ]);
    }

    public function draft(): static
    {
        return $this->state([
            'published_at' => null,
        ]);
    }

    public function configure()
    {
        return $this->afterCreating(function (Executive $executive) {
            $name = $this->faker->name();
            $position = $this->faker->jobTitle();

            $executive->setTranslation('name', $name, 'en');
            $executive->setTranslation('name', '(th) '.$name, 'th');
            $executive->setTranslation('position', $position, 'en');
            $executive->setTranslation('position', '(th) '.$position, 'th');
            $executive->save();
        });
    }

    public function withImage(): static
    {
        return $this->afterCreating(function (Executive $executive) {
            $imageUrl = Helper::getPlaceholderImageUrl($executive->getTranslation('name', 'en'), 300, 300);
            $executive->addMediaFromUrl($imageUrl)
                ->toMediaCollection(Executive::MEDIA_COLLECTION_IMAGE);
        });
    }
}
