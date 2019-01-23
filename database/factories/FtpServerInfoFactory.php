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

$factory->define(\App\Models\Mongs\FtpServerInfo::class, function (Faker\Generator $faker) {
    return [
        'serverName' => $faker->text(10),
        'city' => $faker->randomElement(['suzhou', 'wuxi', 'nantong', 'changzhou', 'zhenjiang']),
        'type' => $faker->randomElement(['nbi', 'mr', 'ctr', 'cdr', 'ebm', 'cdd']),
        'externalAddress' => $faker->text(10),
        'internalAddress' => $faker->text(10),
        'subNetwork' => $faker->text(10),
        'fileDir' => $faker->text(10),
        'userName' => $faker->text(10),
        'password' => $faker->password(6, 10)
    ];
});
