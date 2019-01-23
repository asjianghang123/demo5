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

$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'user' => $faker->randomElement(['usr1','usr2','usr3','usr4']),
        'name' => $faker->name,
        'email' => $faker->safeEmail,
        'pwd' => bcrypt(str_random(10)),
        'type' => $faker->randomElement(['custom', 'admin', 'engineer']),
        'remember_token' => str_random(10),
        'province' => $faker->text(10),
        'operator' => $faker->text(10)
    ];
});
