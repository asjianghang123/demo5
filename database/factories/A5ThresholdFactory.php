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

$factory->define(\App\Models\MR\MreA5Threshold::class, function (Faker\Generator $faker) {
    return [
        'datetime_id' => $faker->dateTimeThisMonth->format('Y-m-d 08:00:00'),
        'ecgi' => $faker->text(10),
        'mr_LteNcEarfcn' => strval($faker->numberBetween(1, 100)),
        'mr_LteScRSRQ_No90_percent' => number_format($faker->randomFloat(2, 90, 100), 2),
        'mr_LteNcRSRQ_No90_percent' => number_format($faker->randomFloat(2, 90, 100), 2),
        'comments' => $faker->text(10)
    ];
});
