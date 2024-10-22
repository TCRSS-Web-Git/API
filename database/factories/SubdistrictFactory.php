<?php

namespace Database\Factories;

use App\Models\District;
use App\Models\Subdistrict;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Subdistrict>
 */
class SubdistrictFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'district_id' => District::inRandomOrder()->first() ?? District::factory(),
            'name_th' => 'ถนนพญาไท',
            'name_en' => 'Thanon Phaya Thai',
            'zip' => '10400',
            'sid' => '101010',
        ];
    }
}
