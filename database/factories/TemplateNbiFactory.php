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

$factory->define(\App\Models\Mongs\TemplateNbi::class, function (Faker\Generator $faker) {
    return [
        'id' => $faker->unique()->randomNumber(),
        'templateName' => $faker->text(10),
        'elementId' => $faker->text(10),
        'description' => $faker->text(10),
        'user' => $faker->randomElement(['usr1', 'usr2', 'usr3', 'usr4']),
    ];
});
