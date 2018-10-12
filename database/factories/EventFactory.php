<?php

use Faker\Generator as Faker;

$factory->define(App\Event::class, function (Faker $faker) {
    return [
        'description' => $faker->text,
        'date' => $faker->dateTime
    ];
});
