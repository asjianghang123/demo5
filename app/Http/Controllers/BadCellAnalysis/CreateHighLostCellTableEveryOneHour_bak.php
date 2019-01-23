<?php
    //php CreateTemp_HighLostCellTableEveryOneHour.php changzhou    
    $city = trim($argv[1]);
    $date = date("Y-m-d");
    $hour = date("H", strtotime("-1 hour"));
    $pdo = new PDO("mysql:host=10.39.148.187;dbname=AutoKPI", "root", "mongs");
    //建表
    $sql = "CREATE TABLE IF NOT EXISTS `Temp_HighLostCellTableEveryOneHour` (
              `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
              `city` varchar(25) DEFAULT NULL,
              `subNetwork` varchar(25) DEFAULT NULL,
              `cell` varchar(25) DEFAULT NULL,
              `hour_id` varchar(25) DEFAULT NULL,
              `前天小时数` varchar(25) DEFAULT NULL,
              `昨天小时数` varchar(25) DEFAULT NULL,
              `今天小时数` varchar(25) DEFAULT NULL,
              `无线掉线次数(最新)` decimal(18,0) DEFAULT NULL,
              `无线掉线次数(总)` decimal(18,0) DEFAULT NULL,
              `无线掉线次数(今日)` decimal(18,0) DEFAULT NULL,
              `严重程度` decimal(18,2) DEFAULT NULL,
              `告警数量` decimal(18,0) DEFAULT NULL,
              `平均PRB` decimal(18,2) DEFAULT NULL,
              `RSRP<-116的比例` decimal(18,2) DEFAULT NULL,
              `最高RRC用户数` decimal(18,2) DEFAULT NULL,
              `重叠覆盖度` decimal(18,2) DEFAULT NULL,
              `需要加邻区数量` decimal(18,2) DEFAULT NULL,
              `RSRQ<-15.5的比例` decimal(18,2) DEFAULT NULL,
              `参数` decimal(18,2) DEFAULT NULL,
              `Polar-告警` decimal(18,2) DEFAULT NULL,
              `Polar-弱覆盖` decimal(18,2) DEFAULT NULL,
              `Polar-重叠覆盖` decimal(18,2) DEFAULT NULL,
              `Polar-质差` decimal(18,2) DEFAULT NULL,
              `Polar-邻区` decimal(18,2) DEFAULT NULL,
              `Polar-最高RRC用户数` decimal(18,2) DEFAULT NULL,
              `Polar-干扰` decimal(18,2) DEFAULT NULL,
              `Polar-参数` decimal(18,2) DEFAULT NULL,
              `无线掉线率_干扰` decimal(18,2) DEFAULT NULL,
              `无线掉线率_质差` decimal(18,2) DEFAULT NULL,
              `MAC层时延` decimal(18,2) DEFAULT NULL,
              `SRcongestion数` decimal(18,0) DEFAULT NULL,
              `SR拥塞比` decimal(18,2) DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `hour_index` (`hour_id`),
              KEY `city_index` (`city`),
              KEY `subNet_index` (`subNetwork`),
              KEY `cell_index` (`cell`)
            ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
    $pdo->query($sql);
    echo 'create table success！';

    $sql = "TRUNCATE TABLE Temp_HighLostCellTableEveryOneHour;";
    $pdo->query($sql);
    echo 'Truncate table success!';

    $sql = "INSERT INTO Temp_HighLostCellTableEveryOneHour (id,city,subNetwork,cell,hour_id,前天小时数,昨天小时数,今天小时数,`无线掉线次数(最新)`,`无线掉线次数(总)`,`无线掉线次数(今日)`,`严重程度`) SELECT
                a.id,
                a.city,
                a.subNetwork,
                a.cell,
                e.hour_id,
                b.`前天小时数`,
                c.`昨天小时数`,
                d.`今天小时数`,
                e.`无线掉线次数(最新)`,
                a.`无线掉线次数(总)`,
                d.`无线掉线次数(今日)`,
                CASE WHEN c.`昨天小时数` IS NULL THEN 0 ELSE c.`昨天小时数` END + 
                CASE WHEN d.`今天小时数` IS NULL THEN 0 ELSE d.`今天小时数` END - 
                CASE WHEN b.`前天小时数` IS NULL THEN 0 ELSE b.`前天小时数` END + a.`无线掉线次数(总)`/1000 AS 严重程度
            FROM
                (
                    SELECT
                        id,
                        city,
                        subNetwork,
                        cell,
                        sum(无线掉线次数) AS `无线掉线次数(总)`
                    FROM
                        highLostCell_ex
                    WHERE
                        city = '$city'
                    AND cell IN (SELECT DISTINCT cell FROM highLostCell_ex WHERE day_id=DATE_FORMAT(NOW(), '%Y-%m-%d') AND hour_id=(SELECT hour_id FROM highLostCell_ex ORDER BY id DESC LIMIT 1))
                    AND day_id >= DATE_ADD(
                        DATE_FORMAT(NOW(), '%Y-%m-%d'),
                        INTERVAL - 2 DAY
                    )
                    AND day_id <= DATE_FORMAT(NOW(), '%Y-%m-%d')
                    GROUP BY
                        subNetwork,
                        cell
                ) a
            LEFT JOIN (
                SELECT
                    cell,
                    COUNT(*) AS 前天小时数
                FROM
                    highLostCell_ex
                WHERE
                    city = '$city'
                AND cell IN (SELECT DISTINCT cell FROM highLostCell_ex WHERE day_id=DATE_FORMAT(NOW(), '%Y-%m-%d') AND hour_id=(SELECT hour_id FROM highLostCell_ex ORDER BY id DESC LIMIT 1))
                AND day_id = DATE_ADD(
                    DATE_FORMAT(NOW(), '%Y-%m-%d'),
                    INTERVAL - 2 DAY
                )
                GROUP BY
                    subNetwork,
                    cell
            ) b ON a.cell = b.cell
            LEFT JOIN (
                SELECT
                    cell,
                    COUNT(*) AS 昨天小时数
                FROM
                    highLostCell_ex
                WHERE
                    city = '$city'
                AND cell IN (SELECT DISTINCT cell FROM highLostCell_ex WHERE day_id=DATE_FORMAT(NOW(), '%Y-%m-%d') AND hour_id=(SELECT hour_id FROM highLostCell_ex ORDER BY id DESC LIMIT 1))
                AND day_id = DATE_ADD(
                    DATE_FORMAT(NOW(), '%Y-%m-%d'),
                    INTERVAL - 1 DAY
                )
                GROUP BY
                    subNetwork,
                    cell
            ) c ON a.cell = c.cell
            LEFT JOIN (
                SELECT
                    cell,
                    sum(无线掉线次数) AS `无线掉线次数(今日)`,
                    COUNT(*) AS 今天小时数
                FROM
                    highLostCell_ex
                WHERE
                    city = '$city'
                AND cell IN (SELECT DISTINCT cell FROM highLostCell_ex WHERE day_id=DATE_FORMAT(NOW(), '%Y-%m-%d') AND hour_id=(SELECT hour_id FROM highLostCell_ex ORDER BY id DESC LIMIT 1))
                AND day_id = DATE_FORMAT(NOW(), '%Y-%m-%d')
                GROUP BY
                    subNetwork,
                    cell
            ) d ON a.cell = d.cell
            LEFT JOIN (
                SELECT
                    cell,
                    hour_id,
                    sum(无线掉线次数) AS `无线掉线次数(最新)`
                FROM
                    highLostCell_ex
                WHERE
                    city = '$city'
                AND cell IN (SELECT DISTINCT cell FROM highLostCell_ex WHERE day_id=DATE_FORMAT(NOW(), '%Y-%m-%d') AND hour_id=(SELECT hour_id FROM highLostCell_ex ORDER BY id DESC LIMIT 1))
                AND day_id = DATE_FORMAT(NOW(), '%Y-%m-%d')
                AND hour_id = (SELECT hour_id FROM highLostCell_ex ORDER BY id DESC LIMIT 1)
                GROUP BY
                    subNetwork,
                    cell
            )e ON a.cell = e.cell
            ORDER BY
                严重程度 DESC
            LIMIT 30;";
    $pdo->query($sql);
    echo 'insert 30 data success!';

    sleep(5);

    $sql = "SELECT cell FROM Temp_HighLostCellTableEveryOneHour;";
    $rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $key => $value) {
        $cell = $value['cell'];
        $row = $pdo->query("SELECT * FROM mongs.siteLte WHERE cellName='".$cell."' LIMIT 1;")->fetchAll(PDO::FETCH_ASSOC);
        $num = count($row);
        if ($num == 0) {
            $pdo->query("UPDATE Temp_HighLostCellTableEveryOneHour SET `告警数量`=0 WHERE cell='$cell'");    
        } else {
            getNumOfDiagnosisDataFilter_alarm($cell, $row[0]['siteName'], $date, $hour, $pdo);    //当前告警--告警数量
        }
        getNumOfDiagnosisDataFilter_alarm($cell, $row[0]['siteName'], $date, $hour, $pdo);    //当前告警--告警数量
        getNumOfDiagnosisDataFilter_weakCover($cell, $date, $hour, $pdo);                      //弱覆盖--RSRP<-116的比例    
        getNumOfDiagnosisDataFilter_highTraffic($cell, $date, $hour, $pdo);                      //高话务--关联度-- >最高RRC用户数
        getNumOfDiagnosisDataFilter_overlapCover($city, $cell, $date, $hour, $pdo);              //重叠覆盖--重叠覆盖度
        getNumOfDiagnosisDataMR($city, $cell, $date, $hour, $pdo);                              //邻区--需要加邻区数量
        getNumOfDiagnosisDataFilter_zhicha($cell, $date, $hour, $pdo);                          //质差--RSRQ<-15.5的比例
        getNumOfDiagnosisDataFilter_AvgPRB($cell, $date, $hour, $pdo);                          //干扰--平均PRB
        getNumOfDiagnosisDataFilter_parameter($city, $cell, $date, $hour, $pdo);                      //参数--参数
        getPolarMapData($city, $cell, $date, $hour, $pdo);                                      //极地图
        geHighLost_interfere($cell, $date, $hour, $pdo);                           //相关性--无线掉线率&干扰
        geHighLost_zhicha($cell, $date, $hour, $pdo);                                 //相关性--无线掉线率&质差
        getNumOfDiagnosisDataFilter_highTraffic_1($city, $cell, $date, $hour, $pdo);                 //高话务 
    }    

    sleep(10);
    $sql = "TRUNCATE TABLE HighLostCellTableEveryOneHour;";
    $pdo->query($sql);
    $sql = "INSERT INTO HighLostCellTableEveryOneHour SELECT * FROM Temp_HighLostCellTableEveryOneHour;";
    $pdo->query($sql);
    // //当前告警--告警数量
    function getNumOfDiagnosisDataFilter_alarm($cell, $erbs, $date, $hour, $pdo)   
    {    
        $sql = "SELECT COUNT(*) as num FROM Alarm.FMA_alarm_list r LEFT JOIN mongs.AlarmInfo t ON r.SP_text= t.alarmNameE WHERE DATE_FORMAT(Event_time, '%Y-%m-%d %H') >= '" .$date."' '".$hour. "' AND meContext = '" . $erbs . "';";
        $rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        $num = $rows[0]['num'];
        $pdo->query("UPDATE Temp_HighLostCellTableEveryOneHour SET `告警数量`=$num WHERE cell='$cell'");    
    }
    //弱覆盖--RSRP<-116的比例
    function getNumOfDiagnosisDataFilter_weakCover($cell, $date, $hour, $pdo) 
       {
           $sql = "select AVG(`RSRP<-116的比例`) as NUM from highLostCell_ex where day_id >= '$date' AND cell='$cell' AND hour_id='$hour';";
           $row = $pdo->query($sql, PDO::FETCH_ASSOC)->fetchall();
           $num = $row[0]['NUM'];
           if ($num === null) {
            $pdo->query("UPDATE Temp_HighLostCellTableEveryOneHour SET `RSRP<-116的比例`=0 WHERE cell='$cell'");
        } else {
            $pdo->query("UPDATE Temp_HighLostCellTableEveryOneHour SET `RSRP<-116的比例`=$num WHERE cell='$cell'");
        }
       }
       //高话务--关联度    
       function getNumOfDiagnosisDataFilter_highTraffic($cell, $date, $hour, $pdo)
       {
           $sql = "SELECT SUM(`最大RRC连接用户数`) AS NUM FROM highLostCell_ex WHERE cell = '$cell' AND day_id = '$date' AND hour_id='$hour';";
           $row = $pdo->query($sql, PDO::FETCH_ASSOC)->fetchall();
           $num = $row[0]['NUM'];
           if ($num === null) {
            $pdo->query("UPDATE Temp_HighLostCellTableEveryOneHour SET `最高RRC用户数`=0 WHERE cell='$cell'");
        } else {
            $pdo->query("UPDATE Temp_HighLostCellTableEveryOneHour SET `最高RRC用户数`=$num WHERE cell='$cell'");
        }
       }
       //重叠覆盖--重叠覆盖度 notification(只能查询昨天的数据，不能实时)
       function getNumOfDiagnosisDataFilter_overlapCover($city, $cell, $date, $hour, $pdo)
       {
           $date_from = date("Y-m-d", strtotime("-1 day"));
           $db = getConn($city);
        $sql = "SELECT avg(rate) as NUM FROM mroOverCoverage_day , GLOBAL.siteLte WHERE mroOverCoverage_day.ecgi = siteLte.ecgi AND dateId = '$date_from' AND cellName = '$cell' LIMIT 1;";
        $num = '';
        $row = $db->query($sql, PDO::FETCH_ASSOC)->fetchall();
        $num = $row[0]['NUM'];
       
        $pdo->query("UPDATE Temp_HighLostCellTableEveryOneHour SET `重叠覆盖度`=0 WHERE cell='$cell'");

        if ($num === null) {
            $pdo->query("UPDATE Temp_HighLostCellTableEveryOneHour SET `重叠覆盖度`=0 WHERE cell='$cell'");
        } else {
            $pdo->query("UPDATE Temp_HighLostCellTableEveryOneHour SET `重叠覆盖度`=$num WHERE cell='$cell'");
        }
        
       }
       //邻区--需要加邻区数量 notification(只能查询昨天的数据，不能实时)
       function getNumOfDiagnosisDataMR($city, $cell, $date, $hour, $pdo)
       {
           $date_from = date("Y-m-d", strtotime("-1 day"));
           $db = getConn($city);
           $sql = "select ecgi from mongs.siteLte where cellName = '$cell'";
           $res = $pdo->query($sql);
        $row = $res->fetchAll(PDO::FETCH_ASSOC);
        if (count($row) == 0) {
            $pdo->query("UPDATE Temp_HighLostCellTableEveryOneHour SET `需要加邻区数量`=0 WHERE cell='$cell'");
        } else {
            $ecgi = $row[0]['ecgi'];
            $table = 'mreServeNeigh_day';
            $sql = "select count(*) as num from $table where isdefined_direct=0 and ecgi = '$ecgi' AND distance_direct<0.8 and dateId >= '" . $date_from . "';";
            $rows = $db->query($sql, PDO::FETCH_ASSOC)->fetchall();
            $num = $rows[0]['num'];
            if ($num === null) {
                $pdo->query("UPDATE Temp_HighLostCellTableEveryOneHour SET `需要加邻区数量`=0 WHERE cell='$cell'");
            } else {
                $pdo->query("UPDATE Temp_HighLostCellTableEveryOneHour SET `需要加邻区数量`=$num WHERE cell='$cell'");
            }
        }
       }
       //质差--RSRQ<-15.5的比例
       function getNumOfDiagnosisDataFilter_zhicha($cell, $date, $hour, $pdo) 
       {
           $sql = "select AVG(`RSRQ<-15.5的比例`) as NUM from highLostCell_ex where day_id = '$date' AND hour_id = '$hour' AND cell='$cell'";
           $row = $pdo->query($sql, PDO::FETCH_ASSOC)->fetchall();
           $num = $row[0]['NUM'];
           if ($num === null) {
            $pdo->query("UPDATE Temp_HighLostCellTableEveryOneHour SET `RSRQ<-15.5的比例`=0 WHERE cell='$cell'");
        } else {
            $pdo->query("UPDATE Temp_HighLostCellTableEveryOneHour SET `RSRQ<-15.5的比例`=$num WHERE cell='$cell'");
        }
       }
       //干扰--平均PRB
       function getNumOfDiagnosisDataFilter_AvgPRB($cell, $date, $hour, $pdo)
       {
           $sql = "SELECT
                AVG(PRB1上行干扰电平) AS PRB1上行干扰电平,
                AVG(PRB2上行干扰电平) AS PRB2上行干扰电平,
                AVG(PRB3上行干扰电平) AS PRB3上行干扰电平,
                AVG(PRB4上行干扰电平) AS PRB4上行干扰电平,
                AVG(PRB5上行干扰电平) AS PRB5上行干扰电平,
                AVG(PRB6上行干扰电平) AS PRB6上行干扰电平,
                AVG(PRB7上行干扰电平) AS PRB7上行干扰电平,
                AVG(PRB8上行干扰电平) AS PRB8上行干扰电平,
                AVG(PRB9上行干扰电平) AS PRB9上行干扰电平,
                AVG(PRB10上行干扰电平) AS PRB10上行干扰电平,
                AVG(PRB11上行干扰电平) AS PRB11上行干扰电平,
                AVG(PRB12上行干扰电平) AS PRB12上行干扰电平,
                AVG(PRB13上行干扰电平) AS PRB13上行干扰电平,
                AVG(PRB14上行干扰电平) AS PRB14上行干扰电平,
                AVG(PRB15上行干扰电平) AS PRB15上行干扰电平,
                AVG(PRB16上行干扰电平) AS PRB16上行干扰电平,
                AVG(PRB17上行干扰电平) AS PRB17上行干扰电平,
                AVG(PRB18上行干扰电平) AS PRB18上行干扰电平,
                AVG(PRB19上行干扰电平) AS PRB19上行干扰电平,
                AVG(PRB20上行干扰电平) AS PRB20上行干扰电平,
                AVG(PRB21上行干扰电平) AS PRB21上行干扰电平,
                AVG(PRB22上行干扰电平) AS PRB22上行干扰电平,
                AVG(PRB23上行干扰电平) AS PRB23上行干扰电平,
                AVG(PRB24上行干扰电平) AS PRB24上行干扰电平,
                AVG(PRB25上行干扰电平) AS PRB25上行干扰电平,
                AVG(PRB26上行干扰电平) AS PRB26上行干扰电平,
                AVG(PRB27上行干扰电平) AS PRB27上行干扰电平,
                AVG(PRB28上行干扰电平) AS PRB28上行干扰电平,
                AVG(PRB29上行干扰电平) AS PRB29上行干扰电平,
                AVG(PRB30上行干扰电平) AS PRB30上行干扰电平,
                AVG(PRB31上行干扰电平) AS PRB31上行干扰电平,
                AVG(PRB32上行干扰电平) AS PRB32上行干扰电平,
                AVG(PRB33上行干扰电平) AS PRB33上行干扰电平,
                AVG(PRB34上行干扰电平) AS PRB34上行干扰电平,
                AVG(PRB35上行干扰电平) AS PRB35上行干扰电平,
                AVG(PRB36上行干扰电平) AS PRB36上行干扰电平,
                AVG(PRB37上行干扰电平) AS PRB37上行干扰电平,
                AVG(PRB38上行干扰电平) AS PRB38上行干扰电平,
                AVG(PRB39上行干扰电平) AS PRB39上行干扰电平,
                AVG(PRB40上行干扰电平) AS PRB40上行干扰电平,
                AVG(PRB41上行干扰电平) AS PRB41上行干扰电平,
                AVG(PRB42上行干扰电平) AS PRB42上行干扰电平,
                AVG(PRB43上行干扰电平) AS PRB43上行干扰电平,
                AVG(PRB44上行干扰电平) AS PRB44上行干扰电平,
                AVG(PRB45上行干扰电平) AS PRB45上行干扰电平,
                AVG(PRB46上行干扰电平) AS PRB46上行干扰电平,
                AVG(PRB47上行干扰电平) AS PRB47上行干扰电平,
                AVG(PRB48上行干扰电平) AS PRB48上行干扰电平,
                AVG(PRB49上行干扰电平) AS PRB49上行干扰电平,
                AVG(PRB50上行干扰电平) AS PRB50上行干扰电平,
                AVG(PRB51上行干扰电平) AS PRB51上行干扰电平,
                AVG(PRB52上行干扰电平) AS PRB52上行干扰电平,
                AVG(PRB53上行干扰电平) AS PRB53上行干扰电平,
                AVG(PRB54上行干扰电平) AS PRB54上行干扰电平,
                AVG(PRB55上行干扰电平) AS PRB55上行干扰电平,
                AVG(PRB56上行干扰电平) AS PRB56上行干扰电平,
                AVG(PRB57上行干扰电平) AS PRB57上行干扰电平,
                AVG(PRB58上行干扰电平) AS PRB58上行干扰电平,
                AVG(PRB59上行干扰电平) AS PRB59上行干扰电平,
                AVG(PRB60上行干扰电平) AS PRB60上行干扰电平,
                AVG(PRB61上行干扰电平) AS PRB61上行干扰电平,
                AVG(PRB62上行干扰电平) AS PRB62上行干扰电平,
                AVG(PRB63上行干扰电平) AS PRB63上行干扰电平,
                AVG(PRB64上行干扰电平) AS PRB64上行干扰电平,
                AVG(PRB65上行干扰电平) AS PRB65上行干扰电平,
                AVG(PRB66上行干扰电平) AS PRB66上行干扰电平,
                AVG(PRB67上行干扰电平) AS PRB67上行干扰电平,
                AVG(PRB68上行干扰电平) AS PRB68上行干扰电平,
                AVG(PRB69上行干扰电平) AS PRB69上行干扰电平,
                AVG(PRB70上行干扰电平) AS PRB70上行干扰电平,
                AVG(PRB71上行干扰电平) AS PRB71上行干扰电平,
                AVG(PRB72上行干扰电平) AS PRB72上行干扰电平,
                AVG(PRB73上行干扰电平) AS PRB73上行干扰电平,
                AVG(PRB74上行干扰电平) AS PRB74上行干扰电平,
                AVG(PRB75上行干扰电平) AS PRB75上行干扰电平,
                AVG(PRB76上行干扰电平) AS PRB76上行干扰电平,
                AVG(PRB77上行干扰电平) AS PRB77上行干扰电平,
                AVG(PRB78上行干扰电平) AS PRB78上行干扰电平,
                AVG(PRB79上行干扰电平) AS PRB79上行干扰电平,
                AVG(PRB80上行干扰电平) AS PRB80上行干扰电平,
                AVG(PRB81上行干扰电平) AS PRB81上行干扰电平,
                AVG(PRB82上行干扰电平) AS PRB82上行干扰电平,
                AVG(PRB83上行干扰电平) AS PRB83上行干扰电平,
                AVG(PRB84上行干扰电平) AS PRB84上行干扰电平,
                AVG(PRB85上行干扰电平) AS PRB85上行干扰电平,
                AVG(PRB86上行干扰电平) AS PRB86上行干扰电平,
                AVG(PRB87上行干扰电平) AS PRB87上行干扰电平,
                AVG(PRB88上行干扰电平) AS PRB88上行干扰电平,
                AVG(PRB89上行干扰电平) AS PRB89上行干扰电平,
                AVG(PRB90上行干扰电平) AS PRB90上行干扰电平,
                AVG(PRB91上行干扰电平) AS PRB91上行干扰电平,
                AVG(PRB92上行干扰电平) AS PRB92上行干扰电平,
                AVG(PRB93上行干扰电平) AS PRB93上行干扰电平,
                AVG(PRB94上行干扰电平) AS PRB94上行干扰电平,
                AVG(PRB95上行干扰电平) AS PRB95上行干扰电平,
                AVG(PRB96上行干扰电平) AS PRB96上行干扰电平,
                AVG(PRB97上行干扰电平) AS PRB97上行干扰电平,
                AVG(PRB98上行干扰电平) AS PRB98上行干扰电平,
                AVG(PRB99上行干扰电平) AS PRB99上行干扰电平,
                AVG(PRB100上行干扰电平) AS PRB100上行干扰电平
            FROM
                interfereCell
            WHERE
                cell = '$cell'
            AND day_id >= '$date'
            AND hour_id <= '$hour';";
        $row = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);   //干扰
        $i = 0;
        $avg = 0;
        if (count($row) == 0) {
            $pdo->query("UPDATE Temp_HighLostCellTableEveryOneHour SET `平均PRB`=0 WHERE cell='$cell'");
        } else {
            foreach ($row[0] as $val) {
                if ($val == null) {
                    continue;
                }
                $avg = $avg + $val;
                $i++;
            }
            if ($i == 0) {
                $pdo->query("UPDATE Temp_HighLostCellTableEveryOneHour SET `平均PRB`=0 WHERE cell='$cell'");
            } else {
                $avg = $avg / $i;
                $pdo->query("UPDATE Temp_HighLostCellTableEveryOneHour SET `平均PRB`=$avg WHERE cell='$cell'");
            }
        }
       } 
       //参数--参数
       function getNumOfDiagnosisDataFilter_parameter($city, $cell, $date, $hour, $pdo)
       {
        $startTime = date("Y-m-d", strtotime("-1 day"));
        $endTime = date("Y-m-d");
        $value = 0;
        //MRE排名前20名的小区mod3重叠覆盖
        $db = getConn($city);
        $sql = "select count(*) from mroPciMod3 where datetime_id like '".$startTime."%' and userLabel = '$cell' and TotalNumOfSample > 100 and OverlapRate > 0.3";
        $rs  = $db->query($sql);
        $row = $rs->fetch(PDO::FETCH_NUM);
        if ($row[0] > 0) {
            $value = 100;
        }
        $date = new DateTime();    
        $date->sub(new DateInterval('P1D'));
        $yesDate = $date->format('ymd');
        $dbname = 'kget' . $yesDate;//获取昨天的kget数据库
        // $dbc = new DataBaseConnection();
        // $db = $dbc->getDB('mongs', $dbname);
        $db = getParamConn($dbname);
        $cityCh = getCHCity($city, $pdo);
        $subNetwork = getSubNets($cityCh, $pdo);
        //MRE邻区排名前30的邻区有PCI一二阶冲突
        if ($value == 0) {
            $filter = " where EutranCellTDD = '$cell'";
            if ($subNetwork != '') {
                $filter = $filter." and subNetwork in (" . $subNetwork . ")";
            }
            //一阶冲突
            $sql1 = "select count(*) from TempEUtranCellRelationNeighOfPci".$filter;
            $rs   = $db->query($sql1);
            $row  = $rs->fetch(PDO::FETCH_NUM);
            if ($row[0] > 0) {
                $value = 100;
            } else {
                //二阶冲突
                $sql2 = "select count(*) from TempEUtranCellRelationNeighOfNeighPci".$filter;
                $rs   = $db->query($sql2);
                $row  = $rs->fetch(PDO::FETCH_NUM);
                if ($row[0] > 0) {
                    $value = 100;
                }
            }
        }
        //没有定义本小区freqrel
        if ($value == 0) {
            $filter = " where EutranCellTDDId = '$cell'";
            if ($subNetwork != '') {
                $filter = $filter." and subNetwork in (" . $subNetwork . ")";
            }
            $sql = "select count(*) from TempMissEqualFrequency ".$filter;
            $rs   = $db->query($sql);
            $row  = $rs->fetch(PDO::FETCH_NUM);
            if ($row[0] > 0) {
                $value = 100;
            }
        }
        //未定邻区
        if ($value == 0) {
            $filter = " where EutranCellTDD = '$cell' and remark3 = 'NoneCellRelation'";
            if ($subNetwork != '') {
                $filter = $filter." and subNetwork in (" . $subNetwork . ")";
            }
            $sql = "select count(*) from TempEUtranCellRelationFewNeighborCell ".$filter;
            $rs   = $db->query($sql);
            $row  = $rs->fetch(PDO::FETCH_NUM);
            if ($row[0] > 0) {
                $value = 100;
            }
        }
        //没有定义同站同频邻区
        if ($value == 0) {
            $filter = " where EutranCellTDD = '$cell' and remark1 = 'co-SiteNeighborRelationMiss'";
            if ($subNetwork != '') {
                $filter = $filter." and subNetwork in (" . $subNetwork . ")";
            }
            $sql = "select count(*) from TempEUtranCellRelationFewNeighborCell ".$filter;
            $rs   = $db->query($sql);
            $row  = $rs->fetch(PDO::FETCH_NUM);
            if ($row[0] > 0) {
                $value = 50;
            }
        }
        //邻区过少
        if ($value == 0) {
            $filter = " where EutranCellTDD = '$cell'";
            if ($subNetwork != '') {
                $filter = $filter." and subNetwork in (" . $subNetwork . ")";
            }
            $sql = "select count(*) from TempEUtranCellRelationFewNeighborCell ".$filter;
            $rs   = $db->query($sql);
            $row  = $rs->fetch(PDO::FETCH_NUM);
            if ($row[0] > 0) {
                $value = 50;
            }
        }
        //baseline中A类参数配置不一致的
        if ($value == 0) {
            $sql = "select siteName from mongs.siteLte where cellName = '$cell'";
            $rs   = $pdo->query($sql);
            $row  = $rs->fetch(PDO::FETCH_NUM);
            $meContext = $row[0];

            $templateId = 53;
            $filter = " where templateId='$templateId' and category = 'A' and subNetwork in (".$subNetwork.") and ( cellId = '$cell' or (meContext = '$meContext' and cellId = ''))";
            $sql = "select count(*) from ParaCheckBaseline".$filter;
            $rs   = $db->query($sql);
            $row  = $rs->fetch(PDO::FETCH_NUM);
            if ($row[0] > 0) {
                $value = 50;
            }
        }
        $pdo->query("UPDATE Temp_HighLostCellTableEveryOneHour SET `参数`=$value WHERE cell='$cell'");
        $pdo->query("UPDATE Temp_HighLostCellTableEveryOneHour SET `Polar-参数`=$value WHERE cell='$cell'");
       }
       //极地图
       function getPolarMapData($city, $cell, $date, $hour, $pdo) 
       {
           //告警
           $sql = "SELECT siteName from mongs.siteLte WHERE cellName='$cell';";
           $row = $pdo->query($sql)->fetchall(PDO::FETCH_ASSOC);
           if (count($row)==0) {
               $pdo->query("UPDATE Temp_HighLostCellTableEveryOneHour SET `Polar-告警`=0 WHERE cell='$cell'");
           } else {
               $erbs = $row[0]['siteName'];
            $sql = "SELECT max(num) as num FROM( SELECT SP_text,t.access AS num,t.alarmNameE FROM Alarm.FMA_alarm_list r LEFT JOIN mongs.AlarmInfo t ON r.SP_text= t.alarmNameE WHERE DATE_FORMAT(Event_time, '%Y-%m-%d %H') >= '" .$date."' '".$hour. "' AND meContext = '" . $erbs . "' GROUP BY access ORDER BY access DESC)t; ";
            $row = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
            if (count($row) == 0) {
                $pdo->query("UPDATE Temp_HighLostCellTableEveryOneHour SET `Polar-告警`=0 WHERE cell='$cell'");
            } else {
                if ($row[0]['num'] > 100) {
                    $pdo->query("UPDATE Temp_HighLostCellTableEveryOneHour SET `Polar-告警`=100 WHERE cell='$cell'");
                } else {
                    $num = intval($row[0]['num']);
                    $pdo->query("UPDATE Temp_HighLostCellTableEveryOneHour SET `Polar-告警`=$num WHERE cell='$cell'");
                }
            }
           }
        
        //弱覆盖
        $sql = "SELECT AVG(`RSRP<-116的比例`) AS num FROM highLostCell_ex WHERE day_id = '$date' AND hour_id='$hour' AND cell = '$cell';";
        $row = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        $weakCoverRateFlag = '';             
        if (count($row) == 0) {
            $pdo->query("UPDATE Temp_HighLostCellTableEveryOneHour SET `Polar-弱覆盖`=0 WHERE cell='$cell'");
            $weakCoverRateFlag = 0;
        } else {
            if ($row[0]['num'] > 20) {
                $pdo->query("UPDATE Temp_HighLostCellTableEveryOneHour SET `Polar-弱覆盖`=100 WHERE cell='$cell'");
            } else if ($row[0]['num'] < 2) {
                $pdo->query("UPDATE Temp_HighLostCellTableEveryOneHour SET `Polar-弱覆盖`=0 WHERE cell='$cell'");
            } else {
                $num = round($row[0]['num']*100/18, 2);
                if ($num > 100) {
                    $pdo->query("UPDATE Temp_HighLostCellTableEveryOneHour SET `Polar-弱覆盖`=100 WHERE cell='$cell'");
                } else {
                    $pdo->query("UPDATE Temp_HighLostCellTableEveryOneHour SET `Polar-弱覆盖`=$num WHERE cell='$cell'");
                }               
            }
            if ($row[0]['num'] > 11) {
                $weakCoverRateFlag = 1;
            } else {
                $weakCoverRateFlag = 0;
            }
        }
        //重叠覆盖
        $date_from = date("Y-m-d", strtotime("-1 day"));
        $db = getConn($city);
        // $sql = "SELECT ROUND(rate*100*5, 0) AS num FROM mroOverCoverage_day , GLOBAL.siteLte WHERE mroOverCoverage_day.ecgi = siteLte.ecgi AND dateId = '$date_from' AND cellName='$cell';";  
        $sql = "SELECT rate AS num FROM mroOverCoverage_day , GLOBAL.siteLte WHERE mroOverCoverage_day.ecgi = siteLte.ecgi AND dateId = '$date_from' AND cellName='$cell';"; 
        $row = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        $overCoverFlag = '';

        if (count($row) == 0) {
            $pdo->query("UPDATE Temp_HighLostCellTableEveryOneHour SET `Polar-重叠覆盖`=0 WHERE cell='$cell'");
            $overCoverFlag = 0;
        } else {
            if ($row[0]['num'] < 0.2) {
                $pdo->query("UPDATE Temp_HighLostCellTableEveryOneHour SET `Polar-重叠覆盖`=0 WHERE cell='$cell'");
            } else if ($row[0]['num'] > 0.2 && $row[0]['num'] < 5) {
                $num = round($row[0]['num'] * (100/(5-0.2)), 2);
                if ($num > 100) {
                    $num = 100;
                }
                $pdo->query("UPDATE Temp_HighLostCellTableEveryOneHour SET `Polar-重叠覆盖`=$num WHERE cell='$cell'");
            } else {
                $pdo->query("UPDATE Temp_HighLostCellTableEveryOneHour SET `Polar-重叠覆盖`=100 WHERE cell='$cell'");
            }
            if ($row[0]['num'] > 5) {
                $weakCoverRateFlag = 1;
            } else {
                $weakCoverRateFlag = 0;
            }
        } 
        // $row = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        // $overCoverFlag = '';
        // if (count($row) == 0) {
        //     $pdo->query("UPDATE Temp_HighLostCellTableEveryOneHour SET `Polar-重叠覆盖`=0 WHERE cell='$cell'");
        //     $overCoverFlag = 0;
        // } else {
        //     if ($row[0]['num'] > 100) {
        //         $pdo->query("UPDATE Temp_HighLostCellTableEveryOneHour SET `Polar-重叠覆盖`=100 WHERE cell='$cell'");
        //     } else {
        //         $num = intval($row[0]['num']);
        //         $pdo->query("UPDATE Temp_HighLostCellTableEveryOneHour SET `Polar-重叠覆盖`=$num WHERE cell='$cell'");
        //     }
        //     if($row[0]['num'] > 5){
        //         $weakCoverRateFlag = 1;
        //     }else {
        //         $weakCoverRateFlag = 0;
        //     }
        // }  
        //质差
        $sql = "SELECT AVG(`RSRQ<-15.5的比例`) AS num FROM highLostCell_ex WHERE day_id = '$date' AND hour_id = '$hour' AND cell = '$cell';";
        $row = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        if (count($row) == 0) {
            $pdo->query("UPDATE Temp_HighLostCellTableEveryOneHour SET `Polar-质差`=0 WHERE cell='$cell'");
        } else {
            if ($row[0]['num'] == 0) {
                $pdo->query("UPDATE Temp_HighLostCellTableEveryOneHour SET `Polar-质差`=0 WHERE cell='$cell'");
            } else {
                if ($weakCoverRateFlag == 0) {
                    if ($overCoverFlag == 0) {
                        $point = $row[0]['num']*5;
                        if ($point >100) {
                            $pdo->query("UPDATE Temp_HighLostCellTableEveryOneHour SET `Polar-质差`=100 WHERE cell='$cell'");
                        } else {
                            $pdo->query("UPDATE Temp_HighLostCellTableEveryOneHour SET `Polar-质差`=$point WHERE cell='$cell'");
                        }        
                    } else {
                        $point = 50 + $row[0]['num']*2.5;
                        if ($point >100) {
                            $pdo->query("UPDATE Temp_HighLostCellTableEveryOneHour SET `Polar-质差`=100 WHERE cell='$cell'");
                        } else {
                            $pdo->query("UPDATE Temp_HighLostCellTableEveryOneHour SET `Polar-质差`=$point WHERE cell='$cell'");
                        }    
                    }
                } else {
                    $point = 50 + $row[0]['num']*2.5;
                    if ($point >100) {
                        $pdo->query("UPDATE Temp_HighLostCellTableEveryOneHour SET `Polar-质差`=100 WHERE cell='$cell'");
                    } else {
                        $pdo->query("UPDATE Temp_HighLostCellTableEveryOneHour SET `Polar-质差`=$point WHERE cell='$cell'");
                    }    
                }
            }
        }
        //邻区
        $date_from = date("Y-m-d", strtotime("-1 day"));
        $sql = "select ecgi from mongs.siteLte where cellName = '$cell'";
        $res = $pdo->query($sql);
        $row = $res->fetchAll(PDO::FETCH_ASSOC);
        if (count($row) == 0) {
            $pdo->query("UPDATE Temp_HighLostCellTableEveryOneHour SET `Polar-邻区`=0 WHERE cell='$cell'");
        } else {
            $ecgi = $row[0]['ecgi'];
            $db = getConn($city);
            $table = 'mreServeNeigh_day';
            $sqlNum = "select count(*) as num from $table where isdefined_direct=0 and ecgi = '$ecgi' AND distance_direct<0.8 and dateId >= '$date_from';";
            $row = $db->query($sqlNum)->fetchAll(PDO::FETCH_ASSOC);
            if (count($row) == 0) {
                $pdo->query("UPDATE Temp_HighLostCellTableEveryOneHour SET `Polar-邻区`=0 WHERE cell='$cell'");
            } else {
                if ($row[0]['num'] == 0) {
                    $pdo->query("UPDATE Temp_HighLostCellTableEveryOneHour SET `Polar-邻区`=0 WHERE cell='$cell'");
                } else {
                    if ($weakCoverRateFlag == 1) {    //非弱覆盖小区
                        $num = intval($row[0]['num']*10);
                        if ($num > 100) {
                            $pdo->query("UPDATE Temp_HighLostCellTableEveryOneHour SET `Polar-邻区`=100 WHERE cell='$cell'");
                        } else {
                            $pdo->query("UPDATE Temp_HighLostCellTableEveryOneHour SET `Polar-邻区`=$num WHERE cell='$cell'");
                        }                   
                    } else {                       //弱覆盖高于11%呈现弱覆盖校区
                        $num = intval(50+$row[0]['num']*5);
                        if ($num > 100) {
                            $pdo->query("UPDATE Temp_HighLostCellTableEveryOneHour SET `Polar-邻区`=100 WHERE cell='$cell'");
                        } else {
                            $pdo->query("UPDATE Temp_HighLostCellTableEveryOneHour SET `Polar-邻区`=$num WHERE cell='$cell'");
                        } 
                    }
                }
            }
        }
        //干扰
        $sql = "SELECT
                    AVG(PRB1上行干扰电平) AS PRB1上行干扰电平,
                    AVG(PRB2上行干扰电平) AS PRB2上行干扰电平,
                    AVG(PRB3上行干扰电平) AS PRB3上行干扰电平,
                    AVG(PRB4上行干扰电平) AS PRB4上行干扰电平,
                    AVG(PRB5上行干扰电平) AS PRB5上行干扰电平,
                    AVG(PRB6上行干扰电平) AS PRB6上行干扰电平,
                    AVG(PRB7上行干扰电平) AS PRB7上行干扰电平,
                    AVG(PRB8上行干扰电平) AS PRB8上行干扰电平,
                    AVG(PRB9上行干扰电平) AS PRB9上行干扰电平,
                    AVG(PRB10上行干扰电平) AS PRB10上行干扰电平,
                    AVG(PRB11上行干扰电平) AS PRB11上行干扰电平,
                    AVG(PRB12上行干扰电平) AS PRB12上行干扰电平,
                    AVG(PRB13上行干扰电平) AS PRB13上行干扰电平,
                    AVG(PRB14上行干扰电平) AS PRB14上行干扰电平,
                    AVG(PRB15上行干扰电平) AS PRB15上行干扰电平,
                    AVG(PRB16上行干扰电平) AS PRB16上行干扰电平,
                    AVG(PRB17上行干扰电平) AS PRB17上行干扰电平,
                    AVG(PRB18上行干扰电平) AS PRB18上行干扰电平,
                    AVG(PRB19上行干扰电平) AS PRB19上行干扰电平,
                    AVG(PRB20上行干扰电平) AS PRB20上行干扰电平,
                    AVG(PRB21上行干扰电平) AS PRB21上行干扰电平,
                    AVG(PRB22上行干扰电平) AS PRB22上行干扰电平,
                    AVG(PRB23上行干扰电平) AS PRB23上行干扰电平,
                    AVG(PRB24上行干扰电平) AS PRB24上行干扰电平,
                    AVG(PRB25上行干扰电平) AS PRB25上行干扰电平,
                    AVG(PRB26上行干扰电平) AS PRB26上行干扰电平,
                    AVG(PRB27上行干扰电平) AS PRB27上行干扰电平,
                    AVG(PRB28上行干扰电平) AS PRB28上行干扰电平,
                    AVG(PRB29上行干扰电平) AS PRB29上行干扰电平,
                    AVG(PRB30上行干扰电平) AS PRB30上行干扰电平,
                    AVG(PRB31上行干扰电平) AS PRB31上行干扰电平,
                    AVG(PRB32上行干扰电平) AS PRB32上行干扰电平,
                    AVG(PRB33上行干扰电平) AS PRB33上行干扰电平,
                    AVG(PRB34上行干扰电平) AS PRB34上行干扰电平,
                    AVG(PRB35上行干扰电平) AS PRB35上行干扰电平,
                    AVG(PRB36上行干扰电平) AS PRB36上行干扰电平,
                    AVG(PRB37上行干扰电平) AS PRB37上行干扰电平,
                    AVG(PRB38上行干扰电平) AS PRB38上行干扰电平,
                    AVG(PRB39上行干扰电平) AS PRB39上行干扰电平,
                    AVG(PRB40上行干扰电平) AS PRB40上行干扰电平,
                    AVG(PRB41上行干扰电平) AS PRB41上行干扰电平,
                    AVG(PRB42上行干扰电平) AS PRB42上行干扰电平,
                    AVG(PRB43上行干扰电平) AS PRB43上行干扰电平,
                    AVG(PRB44上行干扰电平) AS PRB44上行干扰电平,
                    AVG(PRB45上行干扰电平) AS PRB45上行干扰电平,
                    AVG(PRB46上行干扰电平) AS PRB46上行干扰电平,
                    AVG(PRB47上行干扰电平) AS PRB47上行干扰电平,
                    AVG(PRB48上行干扰电平) AS PRB48上行干扰电平,
                    AVG(PRB49上行干扰电平) AS PRB49上行干扰电平,
                    AVG(PRB50上行干扰电平) AS PRB50上行干扰电平,
                    AVG(PRB51上行干扰电平) AS PRB51上行干扰电平,
                    AVG(PRB52上行干扰电平) AS PRB52上行干扰电平,
                    AVG(PRB53上行干扰电平) AS PRB53上行干扰电平,
                    AVG(PRB54上行干扰电平) AS PRB54上行干扰电平,
                    AVG(PRB55上行干扰电平) AS PRB55上行干扰电平,
                    AVG(PRB56上行干扰电平) AS PRB56上行干扰电平,
                    AVG(PRB57上行干扰电平) AS PRB57上行干扰电平,
                    AVG(PRB58上行干扰电平) AS PRB58上行干扰电平,
                    AVG(PRB59上行干扰电平) AS PRB59上行干扰电平,
                    AVG(PRB60上行干扰电平) AS PRB60上行干扰电平,
                    AVG(PRB61上行干扰电平) AS PRB61上行干扰电平,
                    AVG(PRB62上行干扰电平) AS PRB62上行干扰电平,
                    AVG(PRB63上行干扰电平) AS PRB63上行干扰电平,
                    AVG(PRB64上行干扰电平) AS PRB64上行干扰电平,
                    AVG(PRB65上行干扰电平) AS PRB65上行干扰电平,
                    AVG(PRB66上行干扰电平) AS PRB66上行干扰电平,
                    AVG(PRB67上行干扰电平) AS PRB67上行干扰电平,
                    AVG(PRB68上行干扰电平) AS PRB68上行干扰电平,
                    AVG(PRB69上行干扰电平) AS PRB69上行干扰电平,
                    AVG(PRB70上行干扰电平) AS PRB70上行干扰电平,
                    AVG(PRB71上行干扰电平) AS PRB71上行干扰电平,
                    AVG(PRB72上行干扰电平) AS PRB72上行干扰电平,
                    AVG(PRB73上行干扰电平) AS PRB73上行干扰电平,
                    AVG(PRB74上行干扰电平) AS PRB74上行干扰电平,
                    AVG(PRB75上行干扰电平) AS PRB75上行干扰电平,
                    AVG(PRB76上行干扰电平) AS PRB76上行干扰电平,
                    AVG(PRB77上行干扰电平) AS PRB77上行干扰电平,
                    AVG(PRB78上行干扰电平) AS PRB78上行干扰电平,
                    AVG(PRB79上行干扰电平) AS PRB79上行干扰电平,
                    AVG(PRB80上行干扰电平) AS PRB80上行干扰电平,
                    AVG(PRB81上行干扰电平) AS PRB81上行干扰电平,
                    AVG(PRB82上行干扰电平) AS PRB82上行干扰电平,
                    AVG(PRB83上行干扰电平) AS PRB83上行干扰电平,
                    AVG(PRB84上行干扰电平) AS PRB84上行干扰电平,
                    AVG(PRB85上行干扰电平) AS PRB85上行干扰电平,
                    AVG(PRB86上行干扰电平) AS PRB86上行干扰电平,
                    AVG(PRB87上行干扰电平) AS PRB87上行干扰电平,
                    AVG(PRB88上行干扰电平) AS PRB88上行干扰电平,
                    AVG(PRB89上行干扰电平) AS PRB89上行干扰电平,
                    AVG(PRB90上行干扰电平) AS PRB90上行干扰电平,
                    AVG(PRB91上行干扰电平) AS PRB91上行干扰电平,
                    AVG(PRB92上行干扰电平) AS PRB92上行干扰电平,
                    AVG(PRB93上行干扰电平) AS PRB93上行干扰电平,
                    AVG(PRB94上行干扰电平) AS PRB94上行干扰电平,
                    AVG(PRB95上行干扰电平) AS PRB95上行干扰电平,
                    AVG(PRB96上行干扰电平) AS PRB96上行干扰电平,
                    AVG(PRB97上行干扰电平) AS PRB97上行干扰电平,
                    AVG(PRB98上行干扰电平) AS PRB98上行干扰电平,
                    AVG(PRB99上行干扰电平) AS PRB99上行干扰电平,
                    AVG(PRB100上行干扰电平) AS PRB100上行干扰电平
                FROM
                    interfereCell
                WHERE
                    cell = '$cell'
                AND day_id = '$date';";
        $row = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        $i = 0;
        $avg = 0;
        if (count($row) == 0) {
            $pdo->query("UPDATE Temp_HighLostCellTableEveryOneHour SET `Polar-干扰`=0 WHERE cell='$cell'");
        } else {
            foreach ($row[0] as $val) {
                if ($val == null) {
                    continue;
                }
                $avg = $avg + $val;
                $i++;
            }
            if ($i == 0) {
                $pdo->query("UPDATE Temp_HighLostCellTableEveryOneHour SET `Polar-干扰`=0 WHERE cell='$cell'");
            } else {
                $avg = $avg / $i;
                if ($avg >= -102) {
                    $pdo->query("UPDATE Temp_HighLostCellTableEveryOneHour SET `Polar-干扰`=100 WHERE cell='$cell'");
                } elseif ($avg <= -112) {
                    $pdo->query("UPDATE Temp_HighLostCellTableEveryOneHour SET `Polar-干扰`=0 WHERE cell='$cell'");
                } else {
                    $point = ($avg + 112)*10;
                    if ($point > 100) {
                        $pdo->query("UPDATE Temp_HighLostCellTableEveryOneHour SET `Polar-干扰`=100 WHERE cell='$cell'");
                    } else {
                        $pdo->query("UPDATE Temp_HighLostCellTableEveryOneHour SET `Polar-干扰`=$point WHERE cell='$cell'");
                    }
                }
            }
        }
        //高话务
        $sql = "SELECT SUM(`最大RRC连接用户数`) AS num FROM highLostCell_ex WHERE cell = '$cell' AND day_id = '$date' AND hour_id='$hour';";
        $row = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        if (count($row) == 0) {
            $pdo->query("UPDATE Temp_HighLostCellTableEveryOneHour SET `Polar-最高RRC用户数`=0 WHERE cell='$cell'");
        } else {
            if ($row[0]['num'] > 100) {
                $pdo->query("UPDATE Temp_HighLostCellTableEveryOneHour SET `Polar-最高RRC用户数`=100 WHERE cell='$cell'");
            } else {
                $point = intval($row[0]['num']);
                $pdo->query("UPDATE Temp_HighLostCellTableEveryOneHour SET `Polar-最高RRC用户数`=$point WHERE cell='$cell'");
            }
        }
        //参数
        // $yesDate = date("ymd",strtotime("-1 day"));
        // $dbname = 'kget' . $yesDate;
        // $table = 'ParaCheckBaseline';
        // $sql = "select siteName from mongs.siteLte where cellName='$cell';";
        // $row = $pdo->query($sql)->fetchall(PDO::FETCH_ASSOC);
        // $erbs = '';
        // if (count($row) == 0) {
        //     $pdo->query("UPDATE Temp_HighLostCellTableEveryOneHour SET `Polar-参数`=0 WHERE cell='$cell'");
        // }else {
        //     $erbs = $row[0]['siteName'];
        // }
        // $db = getParamConn($dbname);
        // $sql = "select highTraffic from  $table where meContext='$erbs';";
        // $row = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        // if (count($row) == 0) {
        //     $pdo->query("UPDATE Temp_HighLostCellTableEveryOneHour SET `Polar-参数`=0 WHERE cell='$cell'");
        // } else {
        //     if ($row[0]['highTraffic'] == 'YES') {
        //         $pdo->query("UPDATE Temp_HighLostCellTableEveryOneHour SET `Polar-参数`=100 WHERE cell='$cell'");
        //     } else {
        //         $sql = "select count(*) as num from  $table where cellId='$cell';";
        //         $row = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        //         if(count($row) == 0) {
        //             $pdo->query("UPDATE Temp_HighLostCellTableEveryOneHour SET `Polar-参数`=0 WHERE cell='$cell'");
        //         }else {
        //             if($row[0]['num'] == 0) {
        //                 $pdo->query("UPDATE Temp_HighLostCellTableEveryOneHour SET `Polar-参数`=0 WHERE cell='$cell'");
        //             }else {
        //                 $pdo->query("UPDATE Temp_HighLostCellTableEveryOneHour SET `Polar-参数`=50 WHERE cell='$cell'");
        //             }
        //         }    
        //     }
        // }
       }
    //相关性--无线掉线率&干扰
    function geHighLost_interfere($cell, $date, $hour, $pdo)                          
    {
        $startTime = date('Y-m-d', strtotime("-1 day")); 
        $endTime = date('Y-m-d');
        $value_x = [];
        $value_y = [];
        $sql = "SELECT day_id,hour_id,
                    AVG(PRB1上行干扰电平) AS PRB1上行干扰电平,
                    AVG(PRB2上行干扰电平) AS PRB2上行干扰电平,
                    AVG(PRB3上行干扰电平) AS PRB3上行干扰电平,
                    AVG(PRB4上行干扰电平) AS PRB4上行干扰电平,
                    AVG(PRB5上行干扰电平) AS PRB5上行干扰电平,
                    AVG(PRB6上行干扰电平) AS PRB6上行干扰电平,
                    AVG(PRB7上行干扰电平) AS PRB7上行干扰电平,
                    AVG(PRB8上行干扰电平) AS PRB8上行干扰电平,
                    AVG(PRB9上行干扰电平) AS PRB9上行干扰电平,
                    AVG(PRB10上行干扰电平) AS PRB10上行干扰电平,
                    AVG(PRB11上行干扰电平) AS PRB11上行干扰电平,
                    AVG(PRB12上行干扰电平) AS PRB12上行干扰电平,
                    AVG(PRB13上行干扰电平) AS PRB13上行干扰电平,
                    AVG(PRB14上行干扰电平) AS PRB14上行干扰电平,
                    AVG(PRB15上行干扰电平) AS PRB15上行干扰电平,
                    AVG(PRB16上行干扰电平) AS PRB16上行干扰电平,
                    AVG(PRB17上行干扰电平) AS PRB17上行干扰电平,
                    AVG(PRB18上行干扰电平) AS PRB18上行干扰电平,
                    AVG(PRB19上行干扰电平) AS PRB19上行干扰电平,
                    AVG(PRB20上行干扰电平) AS PRB20上行干扰电平,
                    AVG(PRB21上行干扰电平) AS PRB21上行干扰电平,
                    AVG(PRB22上行干扰电平) AS PRB22上行干扰电平,
                    AVG(PRB23上行干扰电平) AS PRB23上行干扰电平,
                    AVG(PRB24上行干扰电平) AS PRB24上行干扰电平,
                    AVG(PRB25上行干扰电平) AS PRB25上行干扰电平,
                    AVG(PRB26上行干扰电平) AS PRB26上行干扰电平,
                    AVG(PRB27上行干扰电平) AS PRB27上行干扰电平,
                    AVG(PRB28上行干扰电平) AS PRB28上行干扰电平,
                    AVG(PRB29上行干扰电平) AS PRB29上行干扰电平,
                    AVG(PRB30上行干扰电平) AS PRB30上行干扰电平,
                    AVG(PRB31上行干扰电平) AS PRB31上行干扰电平,
                    AVG(PRB32上行干扰电平) AS PRB32上行干扰电平,
                    AVG(PRB33上行干扰电平) AS PRB33上行干扰电平,
                    AVG(PRB34上行干扰电平) AS PRB34上行干扰电平,
                    AVG(PRB35上行干扰电平) AS PRB35上行干扰电平,
                    AVG(PRB36上行干扰电平) AS PRB36上行干扰电平,
                    AVG(PRB37上行干扰电平) AS PRB37上行干扰电平,
                    AVG(PRB38上行干扰电平) AS PRB38上行干扰电平,
                    AVG(PRB39上行干扰电平) AS PRB39上行干扰电平,
                    AVG(PRB40上行干扰电平) AS PRB40上行干扰电平,
                    AVG(PRB41上行干扰电平) AS PRB41上行干扰电平,
                    AVG(PRB42上行干扰电平) AS PRB42上行干扰电平,
                    AVG(PRB43上行干扰电平) AS PRB43上行干扰电平,
                    AVG(PRB44上行干扰电平) AS PRB44上行干扰电平,
                    AVG(PRB45上行干扰电平) AS PRB45上行干扰电平,
                    AVG(PRB46上行干扰电平) AS PRB46上行干扰电平,
                    AVG(PRB47上行干扰电平) AS PRB47上行干扰电平,
                    AVG(PRB48上行干扰电平) AS PRB48上行干扰电平,
                    AVG(PRB49上行干扰电平) AS PRB49上行干扰电平,
                    AVG(PRB50上行干扰电平) AS PRB50上行干扰电平,
                    AVG(PRB51上行干扰电平) AS PRB51上行干扰电平,
                    AVG(PRB52上行干扰电平) AS PRB52上行干扰电平,
                    AVG(PRB53上行干扰电平) AS PRB53上行干扰电平,
                    AVG(PRB54上行干扰电平) AS PRB54上行干扰电平,
                    AVG(PRB55上行干扰电平) AS PRB55上行干扰电平,
                    AVG(PRB56上行干扰电平) AS PRB56上行干扰电平,
                    AVG(PRB57上行干扰电平) AS PRB57上行干扰电平,
                    AVG(PRB58上行干扰电平) AS PRB58上行干扰电平,
                    AVG(PRB59上行干扰电平) AS PRB59上行干扰电平,
                    AVG(PRB60上行干扰电平) AS PRB60上行干扰电平,
                    AVG(PRB61上行干扰电平) AS PRB61上行干扰电平,
                    AVG(PRB62上行干扰电平) AS PRB62上行干扰电平,
                    AVG(PRB63上行干扰电平) AS PRB63上行干扰电平,
                    AVG(PRB64上行干扰电平) AS PRB64上行干扰电平,
                    AVG(PRB65上行干扰电平) AS PRB65上行干扰电平,
                    AVG(PRB66上行干扰电平) AS PRB66上行干扰电平,
                    AVG(PRB67上行干扰电平) AS PRB67上行干扰电平,
                    AVG(PRB68上行干扰电平) AS PRB68上行干扰电平,
                    AVG(PRB69上行干扰电平) AS PRB69上行干扰电平,
                    AVG(PRB70上行干扰电平) AS PRB70上行干扰电平,
                    AVG(PRB71上行干扰电平) AS PRB71上行干扰电平,
                    AVG(PRB72上行干扰电平) AS PRB72上行干扰电平,
                    AVG(PRB73上行干扰电平) AS PRB73上行干扰电平,
                    AVG(PRB74上行干扰电平) AS PRB74上行干扰电平,
                    AVG(PRB75上行干扰电平) AS PRB75上行干扰电平,
                    AVG(PRB76上行干扰电平) AS PRB76上行干扰电平,
                    AVG(PRB77上行干扰电平) AS PRB77上行干扰电平,
                    AVG(PRB78上行干扰电平) AS PRB78上行干扰电平,
                    AVG(PRB79上行干扰电平) AS PRB79上行干扰电平,
                    AVG(PRB80上行干扰电平) AS PRB80上行干扰电平,
                    AVG(PRB81上行干扰电平) AS PRB81上行干扰电平,
                    AVG(PRB82上行干扰电平) AS PRB82上行干扰电平,
                    AVG(PRB83上行干扰电平) AS PRB83上行干扰电平,
                    AVG(PRB84上行干扰电平) AS PRB84上行干扰电平,
                    AVG(PRB85上行干扰电平) AS PRB85上行干扰电平,
                    AVG(PRB86上行干扰电平) AS PRB86上行干扰电平,
                    AVG(PRB87上行干扰电平) AS PRB87上行干扰电平,
                    AVG(PRB88上行干扰电平) AS PRB88上行干扰电平,
                    AVG(PRB89上行干扰电平) AS PRB89上行干扰电平,
                    AVG(PRB90上行干扰电平) AS PRB90上行干扰电平,
                    AVG(PRB91上行干扰电平) AS PRB91上行干扰电平,
                    AVG(PRB92上行干扰电平) AS PRB92上行干扰电平,
                    AVG(PRB93上行干扰电平) AS PRB93上行干扰电平,
                    AVG(PRB94上行干扰电平) AS PRB94上行干扰电平,
                    AVG(PRB95上行干扰电平) AS PRB95上行干扰电平,
                    AVG(PRB96上行干扰电平) AS PRB96上行干扰电平,
                    AVG(PRB97上行干扰电平) AS PRB97上行干扰电平,
                    AVG(PRB98上行干扰电平) AS PRB98上行干扰电平,
                    AVG(PRB99上行干扰电平) AS PRB99上行干扰电平,
                    AVG(PRB100上行干扰电平) AS PRB100上行干扰电平
                FROM
                    interfereCell
                WHERE
                    cell = '$cell'
                AND day_id >= '" . $startTime . "'
                AND day_id <= '" . $endTime . "'
                GROUP BY day_id,hour_id;";
        $res = $pdo->query($sql);   //干扰
        $value_x = [];
        $value_y = [];
        while ($row = $res->fetch(PDO::FETCH_NUM)) {
            $avg = 0;
            $j = 0;
            for ($i=0; $i<count($row); $i++) {
                if ($i>1) {
                    if ($row[$i] == null) {
                        continue;
                    }
                    $avg = $avg + $row[$i];
                    $j++;
                }
            }
            if ($j == 0) {
                array_push($value_y, 0);
            } else {
                $avg = $avg / $j;
                array_push($value_y, $avg); 
            }
        }

        $sql = " SELECT
                    day_id,
                    hour_id,
                    无线掉线率
                FROM
                    highLostCell
                WHERE
                    cell = '$cell'
                AND day_id >= '" . $startTime . "'
                AND day_id <= '" . $endTime . "'
                GROUP BY
                    day_id,
                    hour_id";
        $res = $pdo->query($sql);
        while ($row = $res->fetch(PDO::FETCH_NUM)) {
            array_push($value_x, $row[2]);
        }           
        $data['data'] = getRelevanceData($value_x, $value_y);
        $data['date_from'] = $startTime;
        $dataStr = implode(',', $data);
        $pdo->query("UPDATE Temp_HighLostCellTableEveryOneHour SET `无线掉线率_干扰`='$dataStr' WHERE cell='$cell'");
    }
    //相关性--无线掉线率&质差
    function geHighLost_zhicha($cell, $date, $hour, $pdo)                               
    {
        $startTime = date('Y-m-d', strtotime("-1 day")); 
        $endTime = date('Y-m-d');
        $value_x = [];
        $value_y = [];
        $sql = "SELECT
                    day_id,
                    hour_id,
                    无线掉线率,
                    AVG(`RSRQ<-15.5的比例`) AS num
                FROM
                    highLostCell
                WHERE
                    day_id >= '" . $startTime . "'
                AND day_id <= '" . $endTime . "'
                AND cell = '" . $cell . "'
                GROUP BY day_id, hour_id;";
        $res = $pdo->query($sql);
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            array_push($value_x, $row['无线掉线率']);
            array_push($value_y, $row['num']);
        }
        $data['data'] = getRelevanceData($value_x, $value_y);
        $data['date_from'] = $startTime;
        $dataStr = implode(',', $data);
        $pdo->query("UPDATE Temp_HighLostCellTableEveryOneHour SET `无线掉线率_质差`='$dataStr' WHERE cell='$cell'");
    }

    //高话务
    function getNumOfDiagnosisDataFilter_highTraffic_1($city, $cell, $date, $hour, $pdo)    
    {
        $sql = "SELECT MAC层时延,SRcongestion数,SR拥塞比 FROM highLostCell WHERE day_id='$date' AND hour_id='$hour' AND cell='$cell' LIMIT 1;";
        $row = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        if (count($row) == 0) {
            $pdo->query("UPDATE Temp_HighLostCellTableEveryOneHour SET `MAC层时延`=0, `SRcongestion数`=0, `SR拥塞比`=0 WHERE cell='$cell'");
        } else {
            $v1 = $row[0]['MAC层时延'];
            $v2 = $row[0]['SRcongestion数'];
            $v3 = $row[0]['SR拥塞比'];
            $pdo->query("UPDATE Temp_HighLostCellTableEveryOneHour SET `MAC层时延`=$v1, `SRcongestion数`=$v2, `SR拥塞比`=$v3 WHERE cell='$cell'");
        }
    }  

    function getRelevanceData($x,$y) {
        $xSquare = [];
        $ySquare = [];
        $xySquare = [];
        $xySum = 0;
        $xSum = 0;
        $ySum = 0;
        $xxSum = 0;
        $yySum = 0;

        $count = count($x);
        for ($i=0; $i<count($x); $i++) {
            $xx = $x[$i] * $x[$i];
            $yy = $y[$i] * $y[$i];
            $xy = $x[$i] * $y[$i];
            $xySum = $xySum + $xy;  //xy之和
            $xSum = $xSum + $x[$i];     //x之和
            $ySum = $ySum + $y[$i];     //y之和
            $xxSum = $xxSum + $xx;  //xx之和
            $yySum = $yySum + $yy;  //yy之和
        }
        if ($xSum == 0 || $ySum == 0 || ($yySum*$count-$ySum*$ySum) == 0) {
            return '分母为0';
        }
        return abs(round((($xySum*$count-$xSum*$ySum)/(sqrt(abs($xxSum*$count-$xSum*$xSum))*sqrt(abs($yySum*$count-$ySum*$ySum)))), 2));
    }

    function getParamConn($kget)
    {
        return new PDO("mysql:host=10.39.148.187;dbname=$kget", "root", "mongs");
    }

    function getConn($city)
    {
        $dbname = '';
        if ($city == 'changzhou') {
           $dbname = 'MR_CZ';
        } elseif ($city == 'nantong') {
           $dbname = 'MR_NT';
        } elseif ($city == 'suzhou') {
           $dbname = 'MR_SZ';
        } elseif ($city == 'wuxi') {
           $dbname = 'MR_WX';
        } elseif ($city == 'zhenjiang') {
           $dbname = 'MR_ZJ';
        }
        return new PDO("mysql:host=10.40.57.134:8066;dbname=$dbname", "mr", "mr");
    }

    function getCHCity($city, $pdo)
    {
        $sql    = "select cityChinese from mongs.databaseconn where connName='$city'";
        $row    = $pdo->query($sql)->fetchcolumn();
        $CHCity = $row;
        return $CHCity;
    }

    function getSubNets($city, $pdo)
    {
        $SQL           = "select if(subNetworkFDD != '',CONCAT(subNetwork,',',subNetworkFDD),subNetwork) subNetwork from mongs.databaseconn where cityChinese = '$city'";
        // $res           = DB::select($SQL);
        $res = $pdo->query($SQL)->fetchAll(PDO::FETCH_ASSOC);
        $subNetworkArr = array();
        $subNetworkStr = '';
        foreach ($res as $value) {

            $subNetworkStr .= '"'.str_replace(',', '","', $value['subNetwork']).'",';
        }
        $subNetworkStr = substr($subNetworkStr, 0, -1);
        // return $this->reCombine($subNetworkStr);
        return $subNetworkStr;
    }
