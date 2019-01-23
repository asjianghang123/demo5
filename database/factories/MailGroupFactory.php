<?php
/**
 * Created by PhpStorm.
 * User: efjlmmo
 * Date: 2017/6/5
 * Time: 17:07
 */
$factory->define(\App\Models\Mongs\MailGroup::class, function (Faker\Generator $faker) {
    return [
        'id' => $faker->unique()->randomNumber(8),
        'scope' => $faker->randomElement(['lte_kpi_15_min','kget_paracheck','kgetpart_bulkcm','intefere_cell','eniq_2g','database_update_monitor']),
        'scopeName' => $faker->randomElement(['lte_kpi_15_min','kget_paracheck','kgetpart_bulkcm','intefere_cell','eniq_2g','database_update_monitor']),
        'role' => $faker->randomElement(['admin','tl','customer','engineer','developer','user']),
        'roleName' => $faker->randomElement(['admin','tl','customer','engineer','developer','user'])
    ];
});