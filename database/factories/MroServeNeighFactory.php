<?php
/**
 * Created by PhpStorm.
 * User: efjlmmo
 * Date: 2017/7/25
 * Time: 11:47
 */
$factory->define(\App\MroServeNeigh::class, function (\Faker\Generator $faker) {
    return [
        'id' => $faker->unique()->randomNumber(8),
        'datetime_id' => date('Y-m-d H:i:s'),
        'ecgi' => $faker->text(10),
        'city' => $faker->text(10),
        'longitude' => number_format($faker->longitude, 6),
        'latitude' => number_format($faker->latitude, 6),
        'mr_LteScEarfcn' => $faker->numberBetween(1, 100),
        'mr_LteNcEarfcn' => $faker->numberBetween(1, 100),
        'mr_LteNcPci' => $faker->numberBetween(1,100),
        'ecgiNeigh_direct' => $faker->text(10),
        'isdefined_direct' => $faker->numberBetween(1,100),
        'distance_direct' => $faker->text(10),
        'ecgiNeigh_angle' => $faker->text(10),
        'isdefined_angle' => $faker->numberBetween(1,100),
        'distance_angle' => number_format($faker->randomFloat(3,1,100),3),
        'distance_angle_direct' => number_format($faker->randomFloat(3,1,100),3),
        'ncFreq_session' => $faker->numberBetween(1,100),
        'nc_session_num' => $faker->numberBetween(1,100),
        'nc_session_num_avg' => number_format($faker->randomFloat(2,1,100),2),
        'nc_session_num_max' => $faker->numberBetween(1,100),
        'nc_session_num_min' => $faker->numberBetween(1,100),
        'nc_session_ratio' => number_format($faker->randomFloat(3,1,100),3),
        'ncFreq_times_num' => $faker->numberBetween(1,100),
        'nc_times_num' => $faker->numberBetween(1,100),
        'nc_sc_rsrp2_times' => $faker->numberBetween(1,100),
        'nc_sc_rsrp2_times_ratio' => number_format($faker->randomFloat(3,1,100),3),
        'nc_times_ratio' => number_format($faker->randomFloat(3,1,100),3),
        'ncTop2_times_num' => $faker->numberBetween(1,100),
        'ncTop2_times_ratio' => number_format($faker->randomFloat(2,1,100),3),
        'avg_mr_LteScRSRP' => number_format($faker->randomFloat(2,1,100),3),
        'avg_mr_LteScRSRQ' => number_format($faker->randomFloat(2,1,100),3),
        'avg_mr_LteNcRSRP' => number_format($faker->randomFloat(2,1,100),3),
        'avg_mr_LteNcRSRQ' => number_format($faker->randomFloat(2,1,100),3),
    ];
});