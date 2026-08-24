<?php

namespace Database\Factories;

use App\Helper\Helper;
use App\Models\BoardDirector;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BoardDirector>
 */
class BoardDirectorFactory extends Factory
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
        return $this->afterCreating(function (BoardDirector $boardDirector) {
            $name = $this->faker->name();
            $position = $this->faker->jobTitle();

            $boardDirector->setTranslation('name', $name, 'en');
            $boardDirector->setTranslation('name', '(th) '.$name, 'th');
            $boardDirector->setTranslation('position', $position, 'en');
            $boardDirector->setTranslation('position', '(th) '.$position, 'th');
            $boardDirector->save();
        });
    }

    public function withImage(): static
    {
        return $this->afterCreating(function (BoardDirector $boardDirector) {
            $imageUrl = Helper::getPlaceholderImageUrl($boardDirector->getTranslation('name', 'en'), 300, 300);
            $boardDirector->addMediaFromUrl($imageUrl)
                ->toMediaCollection(BoardDirector::MEDIA_COLLECTION_IMAGE);
        });
    }
}
