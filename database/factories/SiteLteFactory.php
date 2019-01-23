<?php
/**
 * Created by PhpStorm.
 * User: efjlmmo
 * Date: 2017/6/5
 * Time: 17:07
 */
$factory->define(\App\Models\Mongs\SiteLte::class, function (Faker\Generator $faker) {
    return [
//        'id' => $faker->unique()->randomNumber(8),
        'ecgi' => $faker->text(10),
        'cellName' => $faker->text(10),
        'cellNameChinese' => $faker->text(10),
        'siteName' => $faker->text(10),
        'siteNameChinese' => $faker->text(10),
        'duplexMode' => $faker->text(10),
        'rsi' => $faker->randomNumber(8),
        'tac' => $faker->randomNumber(8),
        'longitudeBD' => (string)$faker->randomFloat(2, 0, 180),
        'latitudeBD' => (string)$faker->randomFloat(2, 0, 90),
        'longitude' => (string)$faker->randomFloat(2, 0, 180),
        'latitude' => (string)$faker->randomFloat(2, 0, 90),
        'dir' => $faker->randomNumber(8),
        'pci' => $faker->randomNumber(8),
        'earfcn' => $faker->randomNumber(8),
        'siteType' => $faker->text(10),
        'cellType' => $faker->text(10),
        'tiltM' => $faker->randomNumber(8),
        'tiltE' => $faker->randomNumber(8),
        'antHeight' => $faker->randomNumber(8),
        'dualBandNetwork' => $faker->text(10),
        'CANetwork' => $faker->text(10),
        'address' => $faker->text(10),
        'band' => $faker->text(10),
        'channelBandWidth' => $faker->randomNumber(8),
        'noofTxAntennas(Site)' => $faker->text(10),
        'highTraffic' => $faker->text(10),
        'highInterference' => $faker->text(10),
        'HST' => $faker->text(10),
        'cluster' => $faker->text(10),
        'subNetwork' => $faker->text(10),
        'currentOSS' => $faker->text(10),
        '覆盖属性' => $faker->text(10),
        'city' => $faker->randomElement(['changzhou', 'wuxi']),
        'importDate' => $faker->dateTimeThisMonth->format('Y-m-d H:i:s')
    ];
});