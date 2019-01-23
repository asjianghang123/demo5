<?php
/**
 * Created by PhpStorm.
 * User: efjlmmo
 * Date: 2017/7/25
 * Time: 11:47
 */
$factory->define(\App\Models\MR\MroServeNeigh_day::class, function (\Faker\Generator $faker) {
    return [
//        'id' => $faker->unique()->randomNumber(8),
        'dateId' => date('Y-m-d'),
        'ecgi' => $faker->text(10),
        'longitude' => number_format($faker->longitude, 6),
        'latitude' => number_format($faker->latitude, 6),
        'mr_LteScEarfcn' => strval($faker->numberBetween(1, 100)),
        'mr_LteNcEarfcn' => strval($faker->numberBetween(1, 100)),
        'mr_LteNcPci' => strval($faker->numberBetween(1, 100)),
        'ecgiNeigh_direct' => $faker->text(10),
        'isdefined_direct' => strval($faker->numberBetween(1, 100)),
        'distance_direct' => $faker->text(10),
        'ncFreq_session' => strval($faker->numberBetween(1, 100)),
        'nc_session_num' => strval($faker->numberBetween(1, 100)),
        'nc_session_ratio' => number_format($faker->randomFloat(3, 1, 100), 3),
        'ncFreq_times_num' => strval($faker->numberBetween(1, 100)),
        'nc_times_num' => strval($faker->numberBetween(1, 100)),
        'nc_times_ratio' => number_format($faker->randomFloat(3, 1, 100), 3),
        'avg_mr_LteScRSRP' => number_format($faker->randomFloat(2, 1, 100), 3),
        'avg_mr_LteScRSRQ' => number_format($faker->randomFloat(2, 1, 100), 3),
        'avg_mr_LteNcRSRP' => number_format($faker->randomFloat(2, 1, 100), 3),
        'avg_mr_LteNcRSRQ' => number_format($faker->randomFloat(2, 1, 100), 3),
    ];
});