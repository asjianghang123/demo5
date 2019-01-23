<?php
/**
 * Created by PhpStorm.
 * User: efjlmmo
 * Date: 2017/6/5
 * Time: 15:52
 */
$factory->define(\App\Models\Mongs\Alarm4GServerInfo::class, function (Faker\Generator $faker) {
    return [
        'serverName' => $faker->randomElement(['changzhou', 'nantong', 'wuxi', 'suzhou', 'zhenjiang']),
        'city' => $faker->randomElement(['无锡', '常州', '苏州', '南通', '镇江']),
        'host' => $faker->randomElement(['10.40.123.154', '10.40.57.144', '10.39.244.208', '10.40.84.144', '10.40.83.200']),
        'port' => "2640",
        'dbName' => 'fmadb_1_1',
        'userName' => 'sa',
        'password' => 'sybase11',
    ];
});

$factory->define(\App\Models\Mongs\Alarm2GServerInfo::class, function (Faker\Generator $faker) {
    return [
        'serverName' => $faker->randomElement(['changzhou', 'nantong', 'wuxi', 'suzhou', 'zhenjiang']),
        'city' => $faker->randomElement(['无锡', '常州', '苏州', '南通', '镇江']),
        'host' => $faker->randomElement(['10.40.123.154', '10.40.57.144', '10.39.244.208', '10.40.84.144', '10.40.83.200']),
        'port' => "2640",
        'dbName' => 'fmadb_1_1',
        'userName' => 'sa',
        'password' => 'sybase11',
    ];
});