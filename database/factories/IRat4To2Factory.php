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

$factory->define(\App\Models\CDR\Irat4to2::class, function (Faker\Generator $faker) {
    return [
        'date_id' => '2017-07-17',
        'ecgi' => $faker->text(10),
        'cellName' => $faker->text(10),
        'cgi' => $faker->text(10),
        'cell' => $faker->text(10),
        'occurs' => strval($faker->numberBetween(1, 100)),
        'isdefined' => strval($faker->randomElement(['1', '0'])),
        'distince' => number_format($faker->randomFloat(2, 1, 100), 2),
        'Longitude_4g'=> $faker->text(10),
        'Latitude_4g'=> $faker->text(10),
        'Longitude_2g'=> $faker->text(10),
        'Latitude_2g'=> $faker->text(10),
        'users' => strval($faker->numberBetween(0, 2)),
    ];
});
