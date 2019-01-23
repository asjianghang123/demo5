<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(\App\Models\Mongs\UserGroup::class, function (Faker\Generator $faker) {
    return [
//        'id' => $faker->unique()->numberBetween(1, 4),
        'type' => $faker->unique()->randomElement(['engineer', 'customer', 'admin', 'unaudited']),
        'menu_id' => $faker->word,
    ];
});
