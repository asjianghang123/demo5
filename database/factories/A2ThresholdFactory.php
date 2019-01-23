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

$factory->define(\App\Models\MR\MreA2Threshold::class, function (Faker\Generator $faker) {
    return [
        'datetime_id' => $faker->dateTimeThisMonth->format('Y-m-d 08:00:00'),
        'ecgi' => $faker->text(10),
        'city' => $faker->randomElement(['changzhou', 'wuxi', 'suzhou', 'nantong', 'zhenjiang']),
        'mr_LteScRSRQ_No90_percent' => strval($faker->numberBetween(80, 90))
    ];
});
