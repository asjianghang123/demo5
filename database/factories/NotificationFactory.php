<?php
/**
 * Created by PhpStorm.
 * User: efjlmmo
 * Date: 2017/6/5
 * Time: 17:07
 */
$factory->define(\App\Models\Mongs\Notification::class, function (Faker\Generator $faker) {
    return [
        'id' => $faker->unique()->randomNumber(8),
        'publishTime' => $faker->dateTimeThisMonth,
        'publisher' => $faker->name,
        'title' => $faker->word,
        'content' => $faker->word,
        'readed' => (string)$faker->boolean(),
        'userGroup' => strval($faker->numberBetween(1,4))
    ];
});