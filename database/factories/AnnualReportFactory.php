<?php

namespace Database\Factories;

use App\Helper\Helper;
use App\Models\AnnualReport;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AnnualReport>
 */
class AnnualReportFactory extends Factory
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

    public function withCover(): static
    {
        return $this->afterCreating(function (AnnualReport $productAndService) {
            $coverUrl = Helper::getPlaceholderImageUrl($productAndService->getTranslation('title', 'en'), 978, 1224);
            $productAndService->addMediaFromUrl($coverUrl)
                ->toMediaCollection(AnnualReport::MEDIA_COLLECTION_COVER);
        });
    }

    public function withFile(): static
    {
        return $this->afterCreating(function (AnnualReport $productAndService) {
            $fileName = $this->faker->randomElement(['Product Specification.pdf', 'Applications.pdf', 'By Product.pdf']);
            $productAndService->addMedia(storage_path('data/product_and_service_files/'.$fileName))
                ->usingFileName($fileName)
                ->preservingOriginal()
                ->toMediaCollection(AnnualReport::MEDIA_COLLECTION_FILE);
        });
    }

    public function configure()
    {
        return $this->afterCreating(function (AnnualReport $service) {
            $title = $this->faker->sentence();

            $service->setTranslation('title', $title, 'en');
            $service->setTranslation('title', '(th) '.$title, 'th');
            $service->save();
        });
    }
}
