<?php

namespace Database\Seeders\Dev;

use App\Models\Province;
use Exception;
use Illuminate\Database\Seeder;

class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $provinceThaiRaw = file_get_contents(storage_path('data/thai-province-data/json/thai_provinces.json'));
        $provinceThaiArrayRaw = json_decode($provinceThaiRaw, true)['RECORDS'];

        $provinceThaiWithIsoRaw = file_get_contents(storage_path('data/thai-tambons/changwats.json'));
        $provinceThaiWithIsoArrayRaw = json_decode($provinceThaiWithIsoRaw, true);

        $now = now();
        $provinces = [];
        foreach ($provinceThaiArrayRaw as $thaiProvince) {
            $provinceIso = null;
            foreach ($provinceThaiWithIsoArrayRaw as $pid => $thaiProvinceiso) {
                if ($thaiProvinceiso['name']['th'] == $thaiProvince['name_th']) {
                    $provinceIso = $pid;
                    break;
                }
            }

            if (! $provinceIso) {
                throw new Exception('Province not found');
            }

            $provinces[] = [
                'region_id' => $thaiProvince['geography_id'],
                'name_th' => $thaiProvince['name_th'],
                'name_en' => $thaiProvince['name_en'],
                'iso3166_2' => 'TH-'.$provinceIso,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        Province::insert($provinces);
    }
}
