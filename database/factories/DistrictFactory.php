<?php

namespace Database\Factories;

use App\Models\District;
use App\Models\Province;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<District>
 */
class DistrictFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'province_id' => Province::inRandomOrder()->first() ?? Province::factory(),
            'name_th' => 'ราชเทวี',
            'name_en' => 'Ratchathewi',
            'sid' => '1010',
        ];
    }
}
