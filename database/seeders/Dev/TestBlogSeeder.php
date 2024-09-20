<?php

namespace Database\Seeders\Dev;

use App\Models\Blog;
use Illuminate\Database\Seeder;

class TestBlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Blog::factory(20)->create();
    }
}
