<?php

use Faker\Generator as Faker;

$factory->define(\App\Language::class, function (Faker $faker) {
    return [
        'language' => $faker->languageCode,
        'language_code' => $faker->languageCode,
        'image' => null,
    ];
});
