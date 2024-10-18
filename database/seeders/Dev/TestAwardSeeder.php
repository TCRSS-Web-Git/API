<?php

namespace Database\Seeders\Dev;

use App\Models\Award;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class TestAwardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Award::factory(4)
            ->sequence(fn (Sequence $sequence) => ['order' => $sequence->index])
            ->create();
    }
}
