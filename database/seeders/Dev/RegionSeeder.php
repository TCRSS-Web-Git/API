<?php

namespace Database\Seeders\Dev;

use App\Models\Region;
use Illuminate\Database\Seeder;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();
        Region::insert([
            [
                'id' => 1,
                'name_th' => 'ภาคเหนือ',
                'name_en' => 'Northern',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'name_th' => 'ภาคกลาง',
                'name_en' => 'Central',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'name_th' => 'ภาคตะวันออกเฉียงเหนือ',
                'name_en' => 'Northeastern',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 4,
                'name_th' => 'ภาคตะวันตก',
                'name_en' => 'Western',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 5,
                'name_th' => 'ภาคตะวันออก',
                'name_en' => 'Eastern',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 6,
                'name_th' => 'ภาคใต้',
                'name_en' => 'Southern',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
