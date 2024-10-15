<?php

namespace Database\Seeders\Dev;

use App\Models\ProductAndService;
use Illuminate\Database\Seeder;

class TestProductAndServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProductAndService::factory(20)->create();
    }
}
