<?php

namespace Database\Seeders\Dev;

use App\Models\District;
use App\Models\Subdistrict;
use Illuminate\Database\Seeder;

class SubdistrictSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $amphoes = District::all();

        $tambonsThaiRaw = file_get_contents(storage_path('data/thai-province-data/json/thai_tombons.json'));
        $tambonsThaiArrayRaw = json_decode($tambonsThaiRaw, true)['RECORDS'];

        $now = now();

        foreach ($tambonsThaiArrayRaw as $sid => $thaiTambon) {
            $amphoe = $amphoes->firstWhere('sid', substr($thaiTambon['id'], 0, 4));

            if (! $amphoe) {
                echo 'amphor not found';
                dd($thaiTambon);
            }

            $tambon = [
                'district_id' => $amphoe->id,
                'name_th' => trim($thaiTambon['name_th']),
                'name_en' => trim($thaiTambon['name_en']),
                //                'latitude' => $thaiTambon['coordinates']['lat'],
                //                'longitude' => $thaiTambon['coordinates']['lng'],
                'zip' => $thaiTambon['zip_code'],
                'sid' => $thaiTambon['id'],
                'created_at' => $now,
                'updated_at' => $now,
            ];
            Subdistrict::create($tambon);
        }
    }
}
