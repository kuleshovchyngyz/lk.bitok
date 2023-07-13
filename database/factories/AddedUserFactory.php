<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AddedUser>
 */
class AddedUserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    public function definition()
    {
        return [
            'last_name'=>explode(' ',$this->faker->name())[rand(0,1)],
            'first_name'=>explode(' ',$this->faker->name())[rand(0,1)],
            'middle_name'=>explode(' ',$this->faker->name())[rand(0,1)],
            'birth_date'=>$this->faker->dateTimeBetween('1990-01-01', '2022-12-31'),
            'country_id'=>rand(1,10),
            'pass_num_inn'=>$this->faker->unique()->ean13(),
            //'created_at'=>$this->faker()->unique()->ean13,

        ];
    }
}
