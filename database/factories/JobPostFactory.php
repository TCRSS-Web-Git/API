<?php

namespace Database\Factories;

use App\Enums\JobType;
use App\Models\Category;
use App\Models\JobPost;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JobPost>
 */
class JobPostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'location_id' => Category::factory()->location(),
            'department_id' => Category::factory()->jobPost(),
            'type' => fake()->randomElement([JobType::FULL_TIME, JobType::PART_TIME]),
            'published_at' => $this->faker->randomElement([null, $this->faker->dateTimeBetween('-1 year', 'now')]),
        ];
    }

    public function configure()
    {

        return $this->afterCreating(function (JobPost $jobPost) {
            $title = $this->faker->sentence();
            $body = $this->faker->paragraphs(3, true);

            $jobPost->setTranslation('title', $title, 'en');
            $jobPost->setTranslation('title', '(th) ' . $title, 'th');
            $jobPost->setTranslation('body', $body, 'en');
            $jobPost->setTranslation('body', '(th) ' . $body, 'th');
            $jobPost->setTranslation('meta_title', $title, 'en');
            $jobPost->setTranslation('meta_title', '(th) ' . $title, 'th');
            $metaDescription = mb_substr($body, 0, 250);

            $jobPost->setTranslation('meta_description', $metaDescription, 'en');
            $jobPost->setTranslation('meta_description', '(th) ' . $metaDescription, 'th');
            $jobPost->save();
        });
    }
}
