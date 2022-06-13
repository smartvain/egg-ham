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
            'text'         => $faker->sentence(),
            'mean'         => $faker->sentence(),
            'video_title'  => $faker->jobTitle,
            'url'          => $faker->url,
            'start_second' => $faker->numberBetween(1, 5000),
        ];
    }
}
