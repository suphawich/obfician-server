<?php

use Faker\Generator as Faker;

$factory->define(App\UserProfile::class, function (Faker $faker, $attr) {
    return [
        'user_id' => $attr['user_id'],
        'dob' => $faker->dateTime,
        'hometown' => 'Bangkok'
    ];
});
