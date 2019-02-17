<?php

use Faker\Generator as Faker;

$factory->define(App\NotificationType::class, function (Faker $faker) {
    return [
        'machine_name' => uniqid(),
        'title' => $faker->title,
        'is_private' => false,
    ];
});
