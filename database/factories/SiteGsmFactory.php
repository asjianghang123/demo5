<?php
/**
 * Created by PhpStorm.
 * User: efjlmmo
 * Date: 2017/6/5
 * Time: 17:07
 */
$factory->define(\App\Models\Mongs\SiteGsm::class, function (Faker\Generator $faker) {
    return [
        'id' => $faker->unique()->randomNumber(8),
        'CELL' => $faker->text(10),
        'CellNameChinese' => $faker->text(10),
        'CellIdentity' => $faker->randomNumber(8),
        'BAND' => $faker->randomNumber(8),
        'ARFCN' => $faker->randomNumber(8),
        'LongitudeBD' => (string)$faker->randomFloat(2, 0, 180),
        'LatitudeBD' => (string)$faker->randomFloat(2, 0, 90),
        'Longitude' => (string)$faker->randomFloat(2, 0, 180),
        'Latitude' => (string)$faker->randomFloat(2, 0, 90),
        'plmnIdentity_mcc' => $faker->randomNumber(8),
        'plmnIdentity_mnc' => $faker->randomNumber(8),
        'LAC' => $faker->randomNumber(8),
        'BCCH' => $faker->randomNumber(8),
        'BCC' => $faker->randomNumber(8),
        'NCC' => $faker->randomNumber(8),
        'dtmSupport' => $faker->text(10),
        'city' => $faker->randomElement(['changzhou', 'wuxi', 'suzhou', 'nantong', 'zhenjiang']),
        'importDate' => $faker->dateTimeThisMonth->format('Y-m-d H:i:s'),
        'dir' => $faker->randomNumber(8),
        'height' => $faker->randomNumber(8),
        'cellType' => $faker->text(10)
    ];
});