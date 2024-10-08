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
        $type1 = Category::create(['type' => CategoryType::CAREER_TYPE]);
        $type1->setTranslation('name', 'งานเต็มเวลา', 'th');
        $type1->setTranslation('name', 'Full-Time', 'en');
        $type1->save();
        $type2 = Category::create(['type' => CategoryType::CAREER_TYPE]);
        $type2->setTranslation('name', 'งานพาร์ทไทม์', 'th');
        $type2->setTranslation('name', 'Part-Time', 'en');
        $type2->save();

        $department1 = Category::create(['type' => CategoryType::DEPARTMENT]);
        $department1->setTranslation('name', 'ฝ่าย IT', 'th');
        $department1->setTranslation('name', 'IT', 'en');
        $department1->save();
        $department2 = Category::create(['type' => CategoryType::DEPARTMENT]);
        $department2->setTranslation('name', 'ฝ่ายบุคคล', 'th');
        $department2->setTranslation('name', 'HR', 'en');
        $department2->save();

        $location1 = Category::create(['type' => CategoryType::LOCATION]);
        $location1->setTranslation('name', 'กรุงเทพฯ', 'th');
        $location1->setTranslation('name', 'Bangkok', 'en');
        $location1->save();
        $location2 = Category::create(['type' => CategoryType::LOCATION]);
        $location2->setTranslation('name', 'เชียงใหม่', 'th');
        $location2->setTranslation('name', 'Chiangmai', 'en');
        $location2->save();

        Career::factory(25)
            ->state(new Sequence(
                fn (Sequence $sequence) => [
                    'department_id' => fake()->randomElement([$department1->id, $department2->id]),
                    'location_id' => fake()->randomElement([$location1->id, $location2->id]),
                ],
            ))
            ->create();
    }
}
