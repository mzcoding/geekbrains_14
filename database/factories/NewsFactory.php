<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class NewsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
		$title = $this->faker->sentence(3);
        return [
            'title' => $title,
			'slug'  => \Str::slug($title),
			'author' => $this->faker->userName(),
			'status' => 'DRAFT',
			'description' => $this->faker->text(100)
        ];
    }
}
