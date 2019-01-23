<?php
/**
 * Created by PhpStorm.
 * User: efjlmmo
 * Date: 2017/6/19
 * Time: 16:24
 */

$factory->define(\App\Models\Mongs\AlarmInfo::class,function(\Faker\Generator $faker) {
    return [
        'alarmNameE' => $faker->text(10),
        'alarmNameC' => $faker->text(10),
        'levelE' => $faker->text(10),
        'levelC' => $faker->text(10),
        'interfere' => strval($faker->numberBetween(0, 100)),
        'access'=>strval($faker->numberBetween(0, 100)),
        'lost'=>strval($faker->numberBetween(0, 100)),
        'handover'=>strval($faker->numberBetween(0, 100)),
        'comments'=>$faker->text(10)
    ];
});