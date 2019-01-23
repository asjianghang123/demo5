<?php
/**
 * Created by PhpStorm.
 * User: efjlmmo
 * Date: 2017/7/25
 * Time: 14:44
 */
$factory->define(\App\Models\G\TempEUtranCellRelation::class, function (\Faker\Generator $faker) {

    return [
        'EUtranCellTDD' => $faker->text(10),
        'ecgi' => $faker->text(10),
        'city' => $faker->randomElement(['changzhou', 'wuxi', 'suzhou', 'nantong', 'zhenjiang']),
        'EUtranCellRelationId' => $faker->text(10),
        'nc_cellName' => $faker->text(10),
        'sc_longitude' => $faker->text(10),
        'sc_latitude' => $faker->text(10),
        'sc_dir' => $faker->numberBetween(1, 10),
        'sc_channel' => $faker->numberBetween(1, 10),
        'sc_band' => $faker->text(10),
        'nc_longitude' => $faker->text(10),
        'nc_latitude' => $faker->text(10),
        'nc_dir' => $faker->numberBetween(1, 10),
        'nc_channel' => $faker->numberBetween(1, 10),
        'nc_band' => $faker->text(10)
    ];

});