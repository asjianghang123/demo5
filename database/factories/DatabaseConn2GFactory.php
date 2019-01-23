<?php
/**
 * Created by PhpStorm.
 * User: efjlmmo
 * Date: 2017/6/5
 * Time: 15:52
 */
$factory->define(\App\Models\Mongs\Databaseconn2G::class, function (Faker\Generator $faker) {
    return [
        'connName' => $faker->name,
        'cityChinese' => $faker->randomElement(['changzhou', 'nantong', 'wuxi', 'suzhou', 'zhenjiang']),
        'host' => $faker->randomElement(['10.40.56.236', '10.197.32.108', '10.197.12.236', '10.40.82.172', '10.197.128.44']),
        'port' => "2640",
        'dbName' => 'dwhdb',
        'userName' => 'dcbo',
        'password' => 'dcbo',
    ];
});