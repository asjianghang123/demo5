<?php
/**
 * Created by PhpStorm.
 * User: efjlmmo
 * Date: 2017/6/5
 * Time: 17:07
 */
$factory->define(\App\Models\MR\MreServerNeighIrat_day::class, function (Faker\Generator $faker) {
    return [
        'dateId' => date('Y-m-d'),
        'ecgi' => $faker->text(10),
        'cellName' => $faker->text(10),
        'siteName' => $faker->text(10),
        'mr_LteScEarfcn' => strval($faker->randomNumber()),
        'eventType' => $faker->text(6),
        'mr_GsmNcellBcch' => strval($faker->randomNumber()),
        'mr_GsmNcellNcc' => strval($faker->randomNumber()),
        'mr_GsmNcellBcc' => strval($faker->randomNumber()),
        'cell' => $faker->text(10),
        'cellID' => $faker->text(10),
        'cgi' => $faker->text(10),
        'isdefined' => strval($faker->randomNumber()),
        'distance' => number_format($faker->randomFloat(3, 1, 100), 3),
        'longitude_4g' => $faker->text(10),
        'latitude_4g' => $faker->text(10),
        'longitude_2g' => $faker->text(10),
        'latitude_2g' => $faker->text(10),
        'sc_session_num' => strval($faker->randomNumber()),
        'nc_session_num' => strval($faker->randomNumber()),
        'nc_session_ratio' => number_format($faker->randomFloat(2, 1, 100), 2),
        'sc_times_num' => strval($faker->randomNumber()),
        'nc_times_num' => strval($faker->randomNumber()),
        'nc_times_ratio' => number_format($faker->randomFloat(2, 1, 100), 2),
        'avg_mr_LteScRSRP' => number_format($faker->randomFloat(3, 1, 100), 3),
        'avg_mr_LteScRSRQ' => number_format($faker->randomFloat(3, 1, 100), 3),
        'avg_mr_GsmNcellCarrierRSSI' => number_format($faker->randomFloat(3, 1, 100), 3),
    ];
});