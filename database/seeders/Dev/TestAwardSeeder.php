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
        Award::factory(2)
            ->sequence(fn (Sequence $sequence) => ['order' => $sequence->index])
            ->draft()
            ->create();

        Award::factory(5)
            ->sequence(fn (Sequence $sequence) => ['order' => $sequence->index + 2])
            ->published()
            ->create();
    }
}
