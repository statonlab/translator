<?php

use Faker\Generator as Faker;

$factory->define(App\File::class, function (Faker $faker) {
    return [
        'name' => $faker->word,
        'path' => $faker->word,
        'platform_id' => factory(\App\Platform::class)->create()->id,
        'app_version' => $faker->word,
    ];
});
