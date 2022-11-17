<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserOperation>
 */
class UserOperationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $op = ['Купил','Продал'];
        return [

            'user_id'=>rand(1,1000),
            'operation_date'=>$this->faker->dateTime($max = 'now'),
            'operation_direction'=>$op[rand(0,1)],
            'operation_sum'=>rand(55,20000),
        ];
    }
}
