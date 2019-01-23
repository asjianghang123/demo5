<?php
/**
 * Created by PhpStorm.
 * User: efjlmmo
 * Date: 2017/6/5
 * Time: 17:07
 */
$factory->define(\App\Models\Mongs\MailList::class, function (Faker\Generator $faker) {
    return [
        'id' => $faker->unique()->randomNumber(8),
        'mailAddress' => $faker->email,
        'name' => $faker->name,
        'role' => $faker->randomElement(['admin', 'tl', 'customer', 'engineer', 'developer', 'user']),
        'scope' => $faker->randomElement(['lte_kpi_15_min', 'kget_paracheck', 'kgetpart_bulkcm', 'intefere_cell', 'eniq_2g', 'database_update_monitor']),
        'city' => $faker->randomElement(['changzhou', 'wuxi', 'suzhou', 'nantong', 'zhenjiang'])
    ];
});