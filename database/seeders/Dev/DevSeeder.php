<?php

namespace Database\Seeders\Dev;

use Illuminate\Database\Seeder;

class DevSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call(TestUserSeeder::class);
        $this->call(TestBlogSeeder::class);
        $this->call(TestCareerSeeder::class);
    }
}
