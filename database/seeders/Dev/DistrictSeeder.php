<?php

namespace Database\Seeders\Dev;

use App\Models\District;
use Illuminate\Database\Seeder;

class DistrictSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $amphoesThaiRaw = file_get_contents(storage_path('data/thai-province-data/json/thai_amphures.json'));
        $amphoesThaiArrayRaw = json_decode($amphoesThaiRaw, true)['RECORDS'];

        $now = now();
        $amphoes = [];
        foreach ($amphoesThaiArrayRaw as $sid => $thaiAmphoe) {
            $amphoes[] = [
                'province_id' => $thaiAmphoe['province_id'],
                'name_th' => $thaiAmphoe['name_th'],
                'name_en' => $thaiAmphoe['name_en'],
                'sid' => $thaiAmphoe['id'],
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        District::insert($amphoes);
    }
}
