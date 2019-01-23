<?php
/**
 * Created by PhpStorm.
 * User: efjlmmo
 * Date: 2017/8/15
 * Time: 18:04
 */

$factory->define(\App\Models\Mongs\Users::class, function (Faker\Generator $faker) {
    return [
        'user' => $faker->randomElement(['usr1', 'usr2', 'usr3', 'usr4']),
        'name' => $faker->name,
        'email' => $faker->safeEmail,
        'pwd' => bcrypt(str_random(10)),
        'type' => $faker->randomElement(['custom', 'engineer']),
        'remember_token' => str_random(10),
        'province' => $faker->text(10),
        'operator' => $faker->text(10)
    ];
});
