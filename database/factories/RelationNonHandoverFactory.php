<?php
/**
 * Created by PhpStorm.
 * User: efjlmmo
 * Date: 2017/7/27
 * Time: 15:14
 */
$factory->define(\App\Models\AutoKPI\RelationNonHandover::class, function (\Faker\Generator $faker) {
    return [
        'day_from' => $faker->dateTimeBetween('-2 months', '-1 months')->format('Y-m-d'),
        'day_to' => $faker->dateTimeBetween('-1 months', 'now')->format('Y-m-d'),
        'city' => $faker->text(10),
        'subNetwork' => $faker->text(10),
        'cell' => $faker->text(10),
        'EutranCellRelation' => $faker->text(10),
        '切换成功率' => number_format($faker->randomFloat(2, 1, 100), 2),
        '同频切换成功率' => number_format($faker->randomFloat(2, 1, 100), 2),
        '异频切换成功率' => number_format($faker->randomFloat(2, 1, 100), 2),
        '同频准备切换尝试数' => number_format($faker->numberBetween(1, 100), 0),
        '同频准备切换成功数' => number_format($faker->numberBetween(1, 100), 0),
        '同频执行切换尝试数' => number_format($faker->numberBetween(1, 100), 0),
        '同频执行切换成功数' => number_format($faker->numberBetween(1, 100), 0),
        '异频准备切换尝试数' => number_format($faker->numberBetween(1, 100), 0),
        '异频准备切换成功数' => number_format($faker->numberBetween(1, 100), 0),
        '异频执行切换尝试数' => number_format($faker->numberBetween(1, 100), 0),
        '异频执行切换成功数' => number_format($faker->numberBetween(1, 100), 0),
        '准备切换成功率' => number_format($faker->randomFloat(2, 1, 100), 2),
        '执行切换成功率' => number_format($faker->randomFloat(2, 1, 100), 2),
        '准备切换尝试数' => number_format($faker->numberBetween(1, 100), 0),
        '准备切换成功数' => number_format($faker->numberBetween(1, 100), 0),
        '准备切换失败数' => number_format($faker->numberBetween(1, 100), 0),
        '执行切换尝试数' => number_format($faker->numberBetween(1, 100), 0),
        '执行切换成功数' => number_format($faker->numberBetween(1, 100), 0),
        '执行切换失败数' => number_format($faker->numberBetween(1, 100), 0),
        'mlongitude' => number_format($faker->longitude, 2),
        'mlatitude' => number_format($faker->latitude, 2),
        'mdir' => $faker->numberBetween(1, 100),
        'mband' => $faker->text(10),
        'slongitude' => number_format($faker->longitude, 2),
        'slatitude' => number_format($faker->latitude, 2),
        'sdir' => $faker->numberBetween(1, 100),
        'sband' => $faker->text(10),
        'scell' => $faker->text(10),
        'distince' => $faker->text(10)
    ];
});