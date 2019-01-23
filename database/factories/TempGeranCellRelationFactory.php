<?php
/**
 * Created by PhpStorm.
 * User: efjlmmo
 * Date: 2017/6/5
 * Time: 17:07
 */
$factory->define(\App\TempGeranCellRelation::class, function (Faker\Generator $faker) {
    return [
        'ecgi' => $faker->text(10),
        'city' => $faker->randomElement(['changzhou', 'wuxi', 'suzhou', 'nantong', 'zhenjiang']),
        'GeranCellRelationId' => $faker->text(10),
        'sc_longitude' => $faker->text(10),
        'sc_latitude' => $faker->text(10),
        'sc_dir' => $faker->numberBetween(1, 100),
        'sc_channel' => $faker->numberBetween(1, 100),
        'sc_band' => $faker->text(10),
        'nc_longitude' => $faker->text(10),
        'nc_latitude' => $faker->text(10),
        'nc_dir' => $faker->numberBetween(1, 100),
        'nc_ARFCN' => $faker->numberBetween(1, 100),
        'nc_BAND' => $faker->numberBetween(1, 100),
    ];
});