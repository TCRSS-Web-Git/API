<?php

namespace Database\Factories;

use App\Models\Career;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Career>
 */
class CareerFactory extends Factory
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
            'department_id' => Category::factory()->department(),
            'type_id' => Category::factory()->careerType(),
            'published_at' => $this->faker->randomElement([null, $this->faker->dateTimeBetween('-1 year', 'now')]),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Career $career) {
            $title = $this->faker->sentence();
            $body = $this->faker->paragraphs(3, true);

            $career->setTranslation('title', $title, 'en');
            $career->setTranslation('title', '(th) '.$title, 'th');
            $career->setTranslation('body', $body, 'en');
            $career->setTranslation('body', '(th) '.$body, 'th');
            $career->setTranslation('meta_title', $title, 'en');
            $career->setTranslation('meta_title', '(th) '.$title, 'th');
            $metaDescription = mb_substr($body, 0, 250);

            $career->setTranslation('meta_description', $metaDescription, 'en');
            $career->setTranslation('meta_description', '(th) '.$metaDescription, 'th');
            $career->save();
        });
    }
}
