<?php

use Faker\Generator as Faker;

$factory->define(App\Day::class, function (Faker $faker) {
    return [
        'category_id' => function () {
            return factory(App\Category::class)->create()->id;
        },
        'date' => $faker->date(),
        'comment' => $faker->optional()->text
    ];
});
