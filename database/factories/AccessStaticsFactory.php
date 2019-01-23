<?php
/**
 * Created by PhpStorm.
 * User: efjlmmo
 * Date: 2017/6/19
 * Time: 16:24
 */

$factory->define(\App\Models\Mongs\AccessDetail::class,function(\Faker\Generator $faker) {
    return [
        'date_id' => $faker->date('Y-m-d'),
        'user' => $faker->name,
        'url' => $faker->randomElement(['www.baidu.com','www.google.com','www.ericsson.com']),
        'urlChinese' => 'test',
        'createTime' => $faker->dateTimeThisMonth
    ];
});