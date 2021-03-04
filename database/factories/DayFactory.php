<?php

namespace Database\Factories;

use App\Category;
use App\Day;
use Illuminate\Database\Eloquent\Factories\Factory;

class DayFactory extends Factory
{

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Day::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'category_id' => Category::factory(),
            'date' => $this->faker->date(),
            'comment' => $this->faker->optional()->text
        ];
    }
}

