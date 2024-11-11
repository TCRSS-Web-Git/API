<?php

namespace Database\Seeders\Dev;

use App\Models\AwardImage;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class TestAwardImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AwardImage::factory(6)
            ->withImage()
            ->sequence(fn (Sequence $sequence) => ['order' => $sequence->index])
            ->create();
    }
}
