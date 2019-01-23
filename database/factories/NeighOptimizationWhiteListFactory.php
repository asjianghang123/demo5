<?php
/**
 * Created by PhpStorm.
 * User: efjlmmo
 * Date: 2017/6/5
 * Time: 17:07
 */
$factory->define(\App\Models\Mongs\NeighOptimizationWhiteList::class, function (Faker\Generator $faker) {
    return [
        'subNetwork' => $faker->text(10),
        'site' => $faker->text(10),
        'EUtranCellTDD' => $faker->text(10),
        'ecgi' => $faker->text(10),
        'city' => $faker->randomElement(['changzhou', 'wuxi', 'suzhou', 'nantong', 'zhenjiang']),
        'dataType' => $faker->text(10),
        'OptimizationType' => $faker->randomElement(['补2G邻区分析'])
    ];
});