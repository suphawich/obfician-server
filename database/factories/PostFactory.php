<?php

use Faker\Generator as Faker;

$factory->define(App\Post::class, function (Faker $faker) {
    return [
        'title' => $faker->text(100),
        'description' => $faker->text,
        'path_cover' => $faker->image,
        'token' => str_random(64)
    ];
});
