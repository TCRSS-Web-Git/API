<?php

namespace Database\Seeders\Dev;

use App\Enums\CategoryType;
use App\Models\Career;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class TestCareerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $careerTypeIds = Category::where('type', CategoryType::CAREER_TYPE)->pluck('id');
        $departmentIds = Category::where('type', CategoryType::DEPARTMENT)->pluck('id');
        $locationIds = Category::where('type', CategoryType::LOCATION)->pluck('id');

        Career::factory(10)
            ->state(new Sequence(
                fn (Sequence $sequence) => [
                    'type_id' => fake()->randomElement($careerTypeIds),
                    'department_id' => fake()->randomElement($departmentIds),
                    'location_id' => fake()->randomElement($locationIds),
                ],
            ))
            ->create(['published_at' => null]);

        Career::factory(20)
            ->state(new Sequence(
                fn (Sequence $sequence) => [
                    'type_id' => fake()->randomElement($careerTypeIds),
                    'department_id' => fake()->randomElement($departmentIds),
                    'location_id' => fake()->randomElement($locationIds),
                ],
            ))
            ->published()
            ->create();
    }
}
