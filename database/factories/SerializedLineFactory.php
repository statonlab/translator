<?php

use Faker\Generator as Faker;

$factory->define(App\SerializedLine::class, function (Faker $faker) {
    $file = factory(\App\File::class)->create();

    return [
        'file_id' => $file->id,
        'key' => $faker->word,
        'value' => $faker->word,
    ];
});
