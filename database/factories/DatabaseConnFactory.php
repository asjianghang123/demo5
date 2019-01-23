<?php
/**
 * Created by PhpStorm.
 * User: efjlmmo
 * Date: 2017/6/5
 * Time: 15:52
 */
$factory->define(\App\Models\Mongs\Databaseconns::class, function (Faker\Generator $faker) {
    return [
        'connName' => $faker->name,
        'cityChinese' => $faker->randomElement(['changzhou', 'nantong', 'wuxi', 'suzhou', 'zhenjiang']),
        'host' => $faker->randomElement(['10.40.57.148', '10.40.123.158', '10.40.83.204', '10.40.84.148', '10.39.244.212']),
        'port' => "2640",
        'dbName' => 'dwhdb',
        'userName' => 'dcbo',
        'password' => 'dcbo',
        'subNetwork' => $faker->word,
        'subNetworkFdd' => $faker->word
    ];
});