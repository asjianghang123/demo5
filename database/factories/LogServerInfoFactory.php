<?php
/**
 * Created by PhpStorm.
 * User: efjlmmo
 * Date: 2017/6/5
 * Time: 17:07
 */
$factory->define(\App\LogServerInfo::class, function (Faker\Generator $faker) {
    return [
        'nodeName' => $faker->text(10),
        'kpiName' => $faker->text(10),
        'user' => $faker->randomElement(['usr1', 'usr2', 'usr3', 'usr4']),
        'kpiFormula' => $faker->text(10),
        'kpiPrecision' => 2
    ];
});