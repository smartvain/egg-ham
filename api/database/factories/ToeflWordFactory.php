<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ToeflWordFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'text' => $this->faker->word
        ];
    }
}
