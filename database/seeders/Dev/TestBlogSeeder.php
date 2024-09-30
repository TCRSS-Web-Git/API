<?php

namespace Database\Seeders\Dev;

use App\Enums\CategoryType;
use App\Models\Blog;
use App\Models\Category;
use Illuminate\Database\Seeder;

class TestBlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $category1 = Category::create(['type' => CategoryType::BLOG, 'name' => 'บทความ', 'slug' => 'articles']);
        $category1->setTranslation('name', 'บทความ', 'th');
        $category1->setTranslation('name', 'Articles', 'en');
        $category1->save();
        $category2 = Category::create(['type' => CategoryType::BLOG, 'name' => 'โปรโมชั่น', 'slug' => 'promotions']);
        $category2->setTranslation('name', 'โปรโมชั่น', 'th');
        $category2->setTranslation('name', 'Promotions', 'en');
        $category2->save();

        Blog::factory(20)->recycle(Category::blog()->get())->create();
    }
}
