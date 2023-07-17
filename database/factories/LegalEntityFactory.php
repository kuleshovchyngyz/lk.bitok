<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LegalEntity>
 */
class LegalEntityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
   
    public function definition()
    {
        return [
            'name' => $this->faker->company,
            'director_full_name' => $this->faker->name(),
            'birth_date' => '1990-05-28',
            'country_id' => rand(1,10),
            'address' => $this->faker->streetAddress,
            'hash' => $this->faker->unique()->sha256,
        ];
    }
}
