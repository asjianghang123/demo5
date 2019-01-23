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

$factory->define(\App\Models\Mongs\Template_2G::class, function (Faker\Generator $faker) {
    return [
        'templateName' => $faker->text(10),
        'elementId' => implode(',', $faker->randomElements([1, 2, 3, 4, 5, 6, 7, 8, 9, 10], 3)),
        'description' => $faker->text(10),
        'user' => $faker->randomElement(['usr1','usr2','usr3','usr4']),
    ];
});
