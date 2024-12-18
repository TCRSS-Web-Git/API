<?php

namespace Database\Factories;

use App\Models\Blog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Media>
 */
class MediaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'model_type' => $this->faker->randomElement([User::class, Blog::class]),
            'model_id' => 1,
            'uuid' => $this->faker->uuid(),
            'collection_name' => 'photo',
            'name' => $this->faker->text(50),
            'file_name' => $this->faker->text(50),
            'mime_type' => 'image/jpg',
            'disk' => 'local',
            'conversions_disk' => 'local',
            'size' => $this->faker->numerify('#####'),
            'manipulations' => [],
            'custom_properties' => [],
            'generated_conversions' => [],
            'responsive_images' => [],
            'order_column' => 1,
        ];
    }
}
