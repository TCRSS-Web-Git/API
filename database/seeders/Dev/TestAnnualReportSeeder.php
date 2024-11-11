<?php

namespace Database\Seeders\Dev;

use App\Models\AnnualReport;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class TestAnnualReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AnnualReport::factory(10)
            ->withCover()
            ->withFile()
            ->sequence(fn (Sequence $sequence) => ['order' => $sequence->index])
            ->create(['published_at' => null]);

        AnnualReport::factory(20)
            ->withCover()
            ->withFile()
            ->published()
            ->sequence(fn (Sequence $sequence) => ['order' => $sequence->index + 10])
            ->create();
    }
}
