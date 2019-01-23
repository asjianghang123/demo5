<?php
/**
 * Created by PhpStorm.
 * User: efjlmmo
 * Date: 2017/6/5
 * Time: 17:07
 */
$factory->define(\App\Models\Mongs\Task::class, function (Faker\Generator $faker) {
    return [
        'taskName' => $faker->word,
        'status' => 'initial',
        'startTime' => $faker->dateTimeBetween('-2 Hours', '-1 Hours')->format('Y-m-d H:i:s'),
        'endTime' => $faker->dateTimeBetween('-1 Hours', 'now')->format('Y-m-d H:i:s'),
        'tracePath' => $faker->text(10),
        'owner' => $faker->randomElement(['admin', 'customer', 'user']),
        'createTime' => $faker->dateTimeBetween('-3 Hours', '-2 Hours')->format('Y-m-d H:i:s'),
        'type' => $faker->randomElement(['parameter', 'ctrsystem', 'cdrsystem', 'ebmsystem', 'pcapsystem', 'ctrfullsystem'])
    ];
});