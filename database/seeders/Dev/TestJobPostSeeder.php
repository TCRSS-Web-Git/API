<?php

namespace Database\Seeders\Dev;

use App\Enums\CategoryType;
use App\Models\Category;
use App\Models\JobPost;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class TestJobPostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $department1 = Category::create(['type' => CategoryType::CAREER]);
        $department1->setTranslation('name', 'IT', 'th');
        $department1->setTranslation('name', 'ฝ่าย IT', 'en');
        $department1->save();
        $department2 = Category::create(['type' => CategoryType::CAREER]);
        $department2->setTranslation('name', 'HR', 'th');
        $department2->setTranslation('name', 'ฝ่ายบุคคล', 'en');
        $department2->save();

        $location1 = Category::create(['type' => CategoryType::LOCATION]);
        $location1->setTranslation('name', 'Bangkok', 'th');
        $location1->setTranslation('name', 'กรุงเทพฯ', 'en');
        $location1->save();
        $location2 = Category::create(['type' => CategoryType::LOCATION]);
        $location2->setTranslation('name', 'Chiangmai', 'th');
        $location2->setTranslation('name', 'เชียงใหม่', 'en');
        $location2->save();

        JobPost::factory(25)
            ->state(new Sequence(
                fn (Sequence $sequence) => [
                    'department_id' => fake()->randomElement([$department1->id, $department2->id]),
                    'location_id' => fake()->randomElement([$location1->id, $location2->id]),
                ],
            ))
            ->create();
    }
}
