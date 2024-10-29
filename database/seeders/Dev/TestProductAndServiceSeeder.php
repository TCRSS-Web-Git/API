<?php

namespace Database\Seeders\Dev;

use App\Models\ProductAndService;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class TestProductAndServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProductAndService::factory(10)
            ->withCover()
            ->withFile()
            ->draft()
            ->sequence(fn (Sequence $sequence) => ['order' => $sequence->index])
            ->create();

        ProductAndService::factory(20)
            ->withCover()
            ->withFile()
            ->published()
            ->sequence(fn (Sequence $sequence) => ['order' => $sequence->index])
            ->create();
    }
}
