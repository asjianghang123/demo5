<?php
/**
 * Created by PhpStorm.
 * User: efjlmmo
 * Date: 2017/7/26
 * Time: 16:37
 */
$factory->define(\App\Models\MR\MroPciMod3::class, function (\Faker\Generator $faker) {
    return [
        'datetime_id' => date('Y-m-d H:i:s'),
        'userLabel' => $faker->text(10),
        'ecgi' => $faker->text(10),
        'EutrancellTddName' => $faker->text(10),
        'mr_LteScEarfcn' => strval($faker->numberBetween(1, 100)),
        'mr_LteScPci' => strval($faker->numberBetween(1, 100)),
        'mr_LteNcPci' => strval($faker->numberBetween(1, 100)),
        'ecgiNeigh' => $faker->text(10),
        'distance' => number_format($faker->randomFloat(2, 1, 100), 2),
        'mr_LteNcEarfcn' => strval($faker->numberBetween(1, 100)),
        'mr_LteScPciMod3' => strval($faker->numberBetween(1, 100)),
        'mr_LteNcPciMod3' => strval($faker->numberBetween(1, 100)),
        'CountOfOverlapSample' => strval($faker->numberBetween(1, 100)),
        'TotalNumOfSample' => strval($faker->numberBetween(1, 100)),
        'OverlapRate' => number_format($faker->randomFloat(1, 100, 4), 4)
    ];
});