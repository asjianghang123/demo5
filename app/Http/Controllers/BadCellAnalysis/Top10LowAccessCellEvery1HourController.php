<?php

/**
* Top10LowAccessCellEvery1HourController.php
*
* @category Top10LowAccessCellEvery1Hour
* @package  App\Http\Controllers\Top10LowAccessCellEvery1Hour
* @author   ericsson <genius@ericsson.com>
* @license  MIT License
* @link     https://laravel.com/docs/5.4/controllers
*/
namespace App\Http\Controllers\Top10LowAccessCellEvery1Hour;
use DateInterval;
use DateTime;
use PDO;

templateQuery();

function templateQuery()
{
    $time=date('YmdH', time()-3600); 
    $Collection = "CTR_" . $time;
    $cityFilter = 'city="changzhou"';
    $dsn = "mysql:host=10.39.148.187:3306;dbname=AutoKPI";
    $conn = new PDO($dsn, 'root', 'mongs');
    if ($conn == null) {
        die('Could not connect');
    }
    $rs = $conn->query("SELECT * FROM (SELECT a.id, a.city, a.subNetwork, a.cell, b.`前天小时数`, c.`昨天小时数`, d.`今天小时数`, a.`RRC建立失败次数`, a.`ERAB建立失败次数`, c.`昨天小时数` + d.`今天小时数` - b.`前天小时数` AS 小时数 FROM (SELECT id,   city,   subNetwork,   cell,   sum(RRC建立失败次数) AS RRC建立失败次数,   sum(ERAB建立失败次数) AS ERAB建立失败次数  FROM   lowAccessCell_ex  WHERE $cityFilter AND  day_id >= DATE_ADD(    DATE_FORMAT(NOW(), '%Y-%m-%d'),    INTERVAL - 2 DAY   )  AND day_id <= DATE_FORMAT(NOW(), '%Y-%m-%d')  GROUP BY   subNetwork,   cell ) a LEFT JOIN ( SELECT  cell,  COUNT(*) AS 前天小时数 FROM  lowAccessCell_ex WHERE  $cityFilter AND  day_id = DATE_ADD(   DATE_FORMAT(NOW(), '%Y-%m-%d'),   INTERVAL - 2 DAY  ) GROUP BY  subNetwork,  cell) b ON a.cell = b.cell LEFT JOIN ( SELECT  cell,  COUNT(*) AS 昨天小时数 FROM  lowAccessCell_ex WHERE  $cityFilter AND  day_id = DATE_ADD(   DATE_FORMAT(NOW(), '%Y-%m-%d'),   INTERVAL - 1 DAY  ) GROUP BY  subNetwork,  cell) c ON a.cell = c.cell LEFT JOIN ( SELECT  cell,  COUNT(*) AS 今天小时数 FROM  lowAccessCell_ex WHERE  $cityFilter AND  day_id = DATE_FORMAT(NOW(), '%Y-%m-%d') GROUP BY  subNetwork,  cell) d ON a.cell = d.cell WHERE d.`今天小时数` !=0 ORDER BY 小时数 DESC  LIMIT 10) e LEFT JOIN (SELECT siteName,cellName FROM mongs.siteLte)f ON e.cell=f.cellName;");
    $siteNameArr = [];
    while ($row = $rs->fetch(PDO::FETCH_ASSOC)) {
        array_push($siteNameArr, $row['siteName']);
    }
    $siteName = implode(',', $siteNameArr);
    $command = "../../../../public/common/sh/Top10LowAccessCellEvery1Hour.sh " . $Collection . " " . $time . " " .$siteName;   
    exec($command);
}

