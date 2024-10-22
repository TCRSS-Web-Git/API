<?php

namespace Database\Seeders\Dev;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ThailandGeographySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $regionsSql = file_get_contents(storage_path('data/regions_seeder.sql'));
        $provincesSql = file_get_contents(storage_path('data/provinces_seeder.sql'));
        $districtsSql = file_get_contents(storage_path('data/districts_seeder.sql'));
        $subdistrictsSql = file_get_contents(storage_path('data/subdistricts_seeder.sql'));

        DB::statement($regionsSql);
        DB::statement($provincesSql);
        DB::statement($districtsSql);
        DB::statement($subdistrictsSql);
    }
}
