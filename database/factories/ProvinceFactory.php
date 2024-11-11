<?php

namespace Database\Factories;

use App\Models\Province;
use App\Models\Region;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Province>
 */
class ProvinceFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'region_id' => Region::inRandomOrder()->first() ?? Region::factory(),
            'name_th' => 'กรุงเทพมหานคร',
            'name_en' => 'Krung Thep Maha Nakhon',
            'iso3166_2' => 'TH-10',
        ];
    }
}
