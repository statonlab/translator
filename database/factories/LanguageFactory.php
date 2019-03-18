<?php

use Faker\Generator as Faker;

$factory->define(\App\Language::class, function (Faker $faker) {
    return [
        'language' => $faker->languageCode,
        'language_code' => \Illuminate\Support\Str::random(5),
        'image' => null,
        'platform_id' => factory(\App\Platform::class)->create()->id,
    ];
});
