<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class WordFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $faker = $this->faker;
        
        return [
            'user_id'      => $faker->numberBetween(1, 20),
            'text'         => $faker->sentence(),
            'mean'         => $faker->sentence(),
            'video_title'  => $faker->jobTitle,
            'url'          => $faker->url,
        ];
    }
}
