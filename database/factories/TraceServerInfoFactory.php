<?php
/**
 * Created by PhpStorm.
 * User: efjlmmo
 * Date: 2017/6/5
 * Time: 17:07
 */
$factory->define(\App\Models\Mongs\TraceServerInfo::class, function (Faker\Generator $faker) {
    return [
        'serverName' => $faker->text(10),
        'city' => $faker->randomElement(['changzhou', 'wuxi', 'suzhou', 'nantong', 'zhenjiang']),
        'type' => $faker->randomElement(['kget', 'kget16', 'kget_external', 'nbi', 'mr', 'mrs', 'mre', 'mro', 'ctr', 'ctum'
            , 'cdr', 'ebm', 'cdd', 'nbm', 'bulkcm']),
        'ipAddress' => $faker->ipv4,
        'sshUserName' => $faker->text(10),
        'sshPassword' => $faker->text(10),
        'ftpUserName' => $faker->text(10),
        'ftpPassword' => $faker->text(10),
        'fileDir' => $faker->text(10)
    ];
});