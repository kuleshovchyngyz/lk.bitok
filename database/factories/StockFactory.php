<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Stock>
 */
class StockFactory extends Factory
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
            'address' => $this->faker->streetAddress,
            'iban' => $this->faker->iban(),
            'bank_account' => $this->faker->creditCardNumber(),
            'bank_name' => $this->faker->company.' Bank',
            'swift' => $this->faker->swiftBicNumber(),
            'account_code' => $this->faker->uuid(),
        ];
    }
}
