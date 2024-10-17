<?php

namespace Database\Factories;

use App\Helper\Helper;
use App\Models\AwardImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AwardImage>
 */
class AwardImageFactory extends Factory
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
        ];
    }

    public function withImage(): static
    {
        return $this->afterCreating(function (AwardImage $awardImage) {
            $imageUrl = Helper::getPlaceholderImageUrl('image', 2218, 1110);
            $awardImage->addMediaFromUrl($imageUrl)
                ->toMediaCollection(AwardImage::MEDIA_COLLECTION_IMAGE);
        });
    }
}
