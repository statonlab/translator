<?php

use Faker\Generator as Faker;

$factory->define(\App\TranslatedLine::class, function (Faker $faker) {
    /** @var \App\SerializedLine $line */
    $line = factory(\App\SerializedLine::class)->create();

    /** @var \App\Language $language */
    $language = factory(\App\Language::class)->create(['platform_id' => $line->file->platform_id]);

    return [
        'serialized_line_id' => $line->id,
        'language_id' => $language->id,
        'file_id' => $line->file->id,
        'user_id' => factory(\App\User::class)->create()->id,
        'key' => $line->key,
        'value' => $faker->word,
        'needs_updating' => $faker->boolean,
    ];
});
