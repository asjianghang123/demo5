<?php
    //php CreateTemp_BadHandoverCellTableEveryOneHour.php changzhou    
    $city = trim($argv[1]);
    $date = date("Y-m-d");
    $hour = date("H", strtotime("-1 hour"));
    $pdo = new PDO("mysql:host=10.39.148.187;dbname=AutoKPI", "root", "mongs");
    //建表
    $sql = "CREATE TABLE IF NOT EXISTS `Temp_BadHandoverCellTableEveryOneHour` (
              `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
              `city` varchar(25) DEFAULT NULL,
              `subNetwork` varchar(25) DEFAULT NULL,
              `cell` varchar(25) DEFAULT NULL,
              `hour_id` varchar(25) DEFAULT NULL,
              `前天小时数` varchar(25) DEFAULT NULL,
              `昨天小时数` varchar(25) DEFAULT NULL,
              `今天小时数` varchar(25) DEFAULT NULL,
              `准备切换失败数(最新)` decimal(18,0) DEFAULT NULL,
              `执行切换失败数(最新)` decimal(18,0) DEFAULT NULL,
              `准备切换失败数(总)` decimal(18,0) DEFAULT NULL,
              `执行切换失败数(总)` decimal(18,0) DEFAULT NULL,
              `准备切换失败数(今日)` decimal(18,0) DEFAULT NULL,
              `执行切换失败数(今日)` decimal(18,0) DEFAULT NULL,
              `严重程度` decimal(18,2) DEFAULT NULL,
              `告警数量` decimal(18,0) DEFAULT NULL,
              `平均PRB` decimal(18,2) DEFAULT NULL,
              `RSRP<-116的比例` decimal(18,2) DEFAULT NULL,
              `关联度` decimal(18,2) DEFAULT NULL,
              `重叠覆盖度` decimal(18,2) DEFAULT NULL,
              `需要加邻区数量` decimal(18,2) DEFAULT NULL,
              `RSRQ<-15.5的比例` decimal(18,2) DEFAULT NULL,
              `参数` decimal(18,2) DEFAULT NULL,
              `Polar-告警` decimal(18,2) DEFAULT NULL,
              `Polar-弱覆盖` decimal(18,2) DEFAULT NULL,
              `Polar-重叠覆盖` decimal(18,2) DEFAULT NULL,
              `Polar-质差` decimal(18,2) DEFAULT NULL,
              `Polar-邻区` decimal(18,2) DEFAULT NULL,
              `Polar-高话务` decimal(18,2) DEFAULT NULL,
              `Polar-干扰` decimal(18,2) DEFAULT NULL,
              `Polar-参数` decimal(18,2) DEFAULT NULL,
              `切换成功率_准备切换成功率` decimal(18,2) DEFAULT NULL,
              `切换成功率_执行切换成功率` decimal(18,2) DEFAULT NULL,
              `切换成功率_干扰` decimal(18,2) DEFAULT NULL,
              `切换成功率_质差` decimal(18,2) DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `hour_index` (`hour_id`),
              KEY `city_index` (`city`),
              KEY `subNet_index` (`subNetwork`),
              KEY `cell_index` (`cell`)
            ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";
    $pdo->query($sql);
    echo 'create table success！';

$sql="select count(distinct city) as num from Temp_BadHandoverCellTableEveryOneHour";
    $res=$pdo->query($sql);
    $row = $res->fetch();
    if ($row&&$row['num']>=5) {
        $sql = "TRUNCATE TABLE Temp_BadHandoverCellTableEveryOneHour;";
         $pdo->query($sql);
         echo 'Truncate table success!';
    }

    $sql = "INSERT INTO Temp_BadHandoverCellTableEveryOneHour (id,city,subNetwork,cell,hour_id,前天小时数,昨天小时数,今天小时数,`准备切换失败数(最新)`,`执行切换失败数(最新)`,`准备切换失败数(总)`,`执行切换失败数(总)`,`准备切换失败数(今日)`,`执行切换失败数(今日)`,`严重程度`) SELECT
                a.id,
                a.city,
                a.subNetwork,
                a.cell,
                e.hour_id,
                b.`前天小时数`,
                c.`昨天小时数`,
                d.`今天小时数`,
                e.`准备切换失败数(最新)`,
                e.`执行切换失败数(最新)`,
                a.`准备切换失败数(总)`,
                a.`执行切换失败数(总)`,
                d.`准备切换失败数(今日)`,
                d.`执行切换失败数(今日)`,
                CASE WHEN c.`昨天小时数` IS NULL THEN 0 ELSE c.`昨天小时数` END + 
                CASE WHEN d.`今天小时数` IS NULL THEN 0 ELSE d.`今天小时数` END - 
                CASE WHEN b.`前天小时数` IS NULL THEN 0 ELSE b.`前天小时数` END + (a.`准备切换失败数(总)`+a.`执行切换失败数(总)`)/1000 AS 严重程度
            FROM
                (
                    SELECT
                        id,
                        city,
                        subNetwork,
                        cell,
                        sum(准备切换失败数) AS `准备切换失败数(总)`,
                        sum(执行切换失败数) AS `执行切换失败数(总)`
                    FROM
                        badHandoverCell_ex
                    WHERE
                        city = '$city'
                    AND cell IN (SELECT DISTINCT cell FROM badHandoverCell_ex WHERE day_id=DATE_FORMAT(NOW(), '%Y-%m-%d') AND hour_id=(SELECT hour_id FROM badHandoverCell_ex ORDER BY id DESC LIMIT 1))
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
                    badHandoverCell_ex
                WHERE
                    city = '$city'
                AND cell IN (SELECT DISTINCT cell FROM badHandoverCell_ex WHERE day_id=DATE_FORMAT(NOW(), '%Y-%m-%d') AND hour_id=(SELECT hour_id FROM badHandoverCell_ex ORDER BY id DESC LIMIT 1))
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
                    badHandoverCell_ex
                WHERE
                    city = '$city'
                AND cell IN (SELECT DISTINCT cell FROM badHandoverCell_ex WHERE day_id=DATE_FORMAT(NOW(), '%Y-%m-%d') AND hour_id=(SELECT hour_id FROM badHandoverCell_ex ORDER BY id DESC LIMIT 1))
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
                    sum(准备切换失败数) AS `准备切换失败数(今日)`,
                    sum(执行切换失败数) AS `执行切换失败数(今日)`,
                    COUNT(*) AS 今天小时数
                FROM
                    badHandoverCell_ex
                WHERE
                    city = '$city'
                AND cell IN (SELECT DISTINCT cell FROM badHandoverCell_ex WHERE day_id=DATE_FORMAT(NOW(), '%Y-%m-%d') AND hour_id=(SELECT hour_id FROM badHandoverCell_ex ORDER BY id DESC LIMIT 1))
                AND day_id = DATE_FORMAT(NOW(), '%Y-%m-%d')
                GROUP BY
                    subNetwork,
                    cell
            ) d ON a.cell = d.cell
            LEFT JOIN (
                SELECT
                    cell,
                    hour_id,
                    sum(准备切换失败数) AS `准备切换失败数(最新)`,
                    sum(执行切换失败数) AS `执行切换失败数(最新)`
                FROM
                    badHandoverCell_ex
                WHERE
                    city = '$city'
                AND cell IN (SELECT DISTINCT cell FROM badHandoverCell_ex WHERE day_id=DATE_FORMAT(NOW(), '%Y-%m-%d') AND hour_id=(SELECT hour_id FROM badHandoverCell_ex ORDER BY id DESC LIMIT 1))
                AND day_id = DATE_FORMAT(NOW(), '%Y-%m-%d')
                AND hour_id = (SELECT hour_id FROM badHandoverCell_ex ORDER BY id DESC LIMIT 1)
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

    $sql = "SELECT cell FROM Temp_BadHandoverCellTableEveryOneHour WHERE city='$city';";
    $rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

    $rowArr = [];
        foreach ($rows as $key => $value) {
            $t = $value['cell'];
            array_push($rowArr, $t); 
        }

      $cell = implode("','", $rowArr);
      $cell = "('".$cell."')";

    $startTime = date("Y-m-d H:i:s");
        getNumOfDiagnosisDataFilter_alarm($city, $cell, $date, $hour, $pdo);    //当前告警--告警数量
        getNumOfDiagnosisDataFilter_weakCover($cell, $date, $hour, $pdo);                      //弱覆盖--RSRP<-116的比例    
        getNumOfDiagnosisDataFilter_highTraffic($cell, $date, $hour, $pdo);                      //高话务--关联度
        getNumOfDiagnosisDataFilter_overlapCover($city, $cell, $date, $hour, $pdo);              //重叠覆盖--重叠覆盖度
        getNumOfDiagnosisDataMR($city, $cell, $date, $hour, $pdo);                              //邻区--需要加邻区数量
        getNumOfDiagnosisDataFilter_zhicha($cell, $date, $hour, $pdo);                          //质差--RSRQ<-15.5的比例
        getNumOfDiagnosisDataFilter_AvgPRB($cell, $date, $hour, $pdo);                          //干扰--平均PRB
        getNumOfDiagnosisDataFilter_parameter($city, $rowArr, $date, $hour, $pdo);                      //参数--参数
        getBadHandover_prepare($cell, $date, $hour, $pdo, $rowArr);                             //相关性--切换成功率_准备切换成功率
        getBadHandover_exec($cell, $date, $hour, $pdo, $rowArr);                                //相关性--切换成功率_执行切换成功率
        geBadHandover_interfere($cell, $date, $hour, $pdo, $rowArr);                              //相关性--切换成功率&干扰
        getBadHandover_zhicha($cell, $date, $hour, $pdo, $rowArr);                                  //相关性--切换成功率&质差
        getPolarMapData($city, $cell, $date, $hour, $pdo, $rowArr);                                      //极地图
    $endTime = date("Y-m-d H:i:s");

    sleep(10);

    // sleep(10);
   
    
    // //当前告警--告警数量
    function getNumOfDiagnosisDataFilter_alarm($city, $cell, $date, $hour, $pdo)   
    {    
        $pdo->query("UPDATE Temp_BadHandoverCellTableEveryOneHour SET `告警数量`=0 WHERE `告警数量` IS NULL"); 
        $sql = "SELECT cellName as cell,siteName from mongs.siteLte WHERE cellName in $cell;"; 
      $res = $pdo->query($sql, PDO::FETCH_ASSOC);
      if ($res) {
        $row = $res->fetchall();
        $erbs = array();
        $cellArr = array();
        $erabArr = array();
        foreach ($row as $value) {
          $v = $value['siteName'];
          $cellArr[$v] = $value['cell'];
          array_push($erbs, $v);
        }
        $erbsStr = "('".implode("','", $erbs)."')";
        // $time = $date.' '.$hour;
        // $sql = "SELECT meContext,COUNT(*) as num FROM Alarm.FMA_alarm_list r LEFT JOIN mongs.AlarmInfo t ON r.SP_text= t.alarmNameE WHERE DATE_FORMAT(Event_time, '%Y-%m-%d %H') >= '$time' AND meContext in $erbsStr;";
        $time = $date;
        $sql = "SELECT meContext,COUNT(*) as num FROM Alarm.FMA_alarm_list r LEFT JOIN mongs.AlarmInfo t ON r.SP_text= t.alarmNameE WHERE DATE_FORMAT(Event_time, '%Y-%m-%d') >= '$time' AND meContext in $erbsStr GROUP BY meContext;";
        // echo $sql;echo '|';
        $rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        if ($rows[0]['meContext'] != null) {
          foreach ($rows as $key => $value) {
            $cellStr = $cellArr[$value['meContext']];
            $num = $value['num'];
            getSameErabs($pdo, $city, $value['meContext'], $num, '告警数量'); 
          }
        } else {
          $pdo->query("UPDATE Temp_BadHandoverCellTableEveryOneHour SET `告警数量`=0 WHERE city='$city'"); 
        }
      }

    }
    //弱覆盖--RSRP<-116的比例
    function getNumOfDiagnosisDataFilter_weakCover($cell, $date, $hour, $pdo) 
    {
      $sql = "select cell,AVG(`RSRP<-116的比例`) as NUM from badHandoverCell_ex where day_id >= '$date' AND cell in $cell AND hour_id='$hour' group by cell;";
      $row = $pdo->query($sql, PDO::FETCH_ASSOC)->fetchall();
      foreach ($row as $key => $value) {
        $cell = $value['cell'];
        $num = $value['NUM'];
        $pdo->query("UPDATE Temp_BadHandoverCellTableEveryOneHour SET `RSRP<-116的比例`= $num WHERE cell='$cell'");
    }
       
    }
       //高话务--关联度    
    function getNumOfDiagnosisDataFilter_highTraffic($cell, $date, $hour, $pdo)
    {
      $sql = "SELECT cell,SUM(`最大RRC连接用户数`)/20 AS NUM FROM badHandoverCell_ex WHERE cell in $cell AND day_id = '$date' AND hour_id='$hour' group by cell;";
      $row = $pdo->query($sql, PDO::FETCH_ASSOC)->fetchall();
      foreach ($row as $key => $value) {
        $cell = $value['cell'];
        $num = $value['NUM'];
        $pdo->query("UPDATE Temp_BadHandoverCellTableEveryOneHour SET `关联度`=$num WHERE cell='$cell'");
    }
   
    }
       //重叠覆盖--重叠覆盖度 notification(只能查询昨天的数据，不能实时)
       function getNumOfDiagnosisDataFilter_overlapCover($city, $cell, $date, $hour, $pdo)
       {
        $date_from = date("Y-m-d", strtotime("-1 day"));
        $db = getConn($city);
        if ($db == 'Caught exception: Sybase服务器连接失败！') {
            $pdo->query("UPDATE Temp_BadHandoverCellTableEveryOneHour SET `重叠覆盖度`='MRConnectFailed' WHERE city = '$city'");
        } else {
            $sql = "SELECT cellName,ecgi FROM mongs.siteLte WHERE cellName IN $cell;";
              $row = $pdo->query($sql, PDO::FETCH_ASSOC)->fetchAll();
              $ecgiArr = [];
              $cellArr = [];
              foreach ($row as $key => $value) {
               $ecgiArr[] = $value['ecgi'];
               $cellArr[$value['ecgi']] = $value['cellName'];
            }
             $ecgiStr = "('".implode("','", $ecgiArr)."')";
              $sql = "SELECT ecgi,rate AS num FROM mroOverCoverage_day WHERE  dateId = '$date_from' AND ecgi in $ecgiStr GROUP BY ecgi;";
              // $row = $db->query($sql, PDO::FETCH_ASSOC)->fetchall();
              $res = $db->query($sql);
              if ($res) {
                  $row = $res->fetchall(PDO::FETCH_ASSOC);
                  foreach ($row as $key => $value) {
                    $ecgiStr = $value['ecgi'];
                    $num = $value['num'];
                    $cell = $cellArr[$ecgiStr];
                    $pdo->query("UPDATE Temp_BadHandoverCellTableEveryOneHour SET `重叠覆盖度`=$num WHERE cell='$cell'");
                }  
            }
             
        }
      
           
        
       }
       //邻区--需要加邻区数量 notification(只能查询昨天的数据，不能实时)
       function getNumOfDiagnosisDataMR($city, $cell, $date, $hour, $pdo)
       {
        $date_from = date("Y-m-d", strtotime("-1 day"));
        $db = getConn($city);
        if ($db == 'Caught exception: Sybase服务器连接失败！') {
            $pdo->query("UPDATE Temp_BadHandoverCellTableEveryOneHour SET `需要加邻区数量`='MRConnectFailed' WHERE city = '$city'");
        } else {
            $sql = "select cellName,ecgi from mongs.siteLte where cellName in $cell";
            $res = $pdo->query($sql);
            $row = $res->fetchAll(PDO::FETCH_ASSOC);
            $ecgiArr = [];
            $ecgi = [];
            foreach ($row as $value) {
                $cell = $value['cellName'];
                $ecgi[] = $value['ecgi'];
                $ecgiArr[$value['ecgi']] = $cell; 
            }
            $ecgiStr = implode("','", $ecgi);
            $ecgiStr = "('".$ecgiStr."')";
            $table = 'mreServeNeigh_day';
            $sql = "select ecgi,count(*) as num from $table where isdefined_direct=0 and ecgi in $ecgiStr AND distance_direct<0.8 and dateId >= '" . $date_from . "'group by ecgi;";
            // $row = $db->query($sql, PDO::FETCH_ASSOC)->fetchall();
            $res = $db->query($sql, PDO::FETCH_ASSOC);
            if ($res) {
                $row = $res->fetchall();
                foreach ($row as $key => $value) {
                    $num = $value['num'];
                    $cell = $ecgiArr[$value['ecgi']];
                    $pdo->query("UPDATE Temp_BadHandoverCellTableEveryOneHour SET `需要加邻区数量`=$num WHERE cell='$cell'");
                }  
            }     
        }  
       }
       //质差--RSRQ<-15.5的比例
       function getNumOfDiagnosisDataFilter_zhicha($cell, $date, $hour, $pdo) 
       {
        $sql = "SELECT cell,AVG(`RSRQ<-15.5的比例`) AS NUM FROM badHandoverCell_ex WHERE cell in $cell AND day_id = '$date' AND hour_id='$hour' group by cell;";
        $row = $pdo->query($sql, PDO::FETCH_ASSOC)->fetchall();
        foreach ($row as $key => $value) {
            $cell = $value['cell'];
            $num = $value['NUM'];
        
            $pdo->query("UPDATE Temp_BadHandoverCellTableEveryOneHour SET `RSRQ<-15.5的比例`=$num WHERE cell='$cell'");

        }

       }
       //干扰--平均PRB
       function getNumOfDiagnosisDataFilter_AvgPRB($cell, $date, $hour, $pdo)
       {
           $sql = "SELECT cell,
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
                cell in $cell 
            AND day_id >= '$date'
            AND hour_id = '$hour'
            GROUP BY cell;";
        $row = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);   //干扰
        
        foreach ($row as $key => $value) {
            $cell = $value['cell'];
            $i = 100;
            $avg = 0;
            foreach ($value as $val) {
                if ($val == null) {
                  $i--;
                    continue;
                }
                $avg = $avg + $val;
            }
            if ($i == 0) {
                $pdo->query("UPDATE Temp_BadHandoverCellTableEveryOneHour SET `平均PRB`=0 WHERE cell='$cell'");
            } else {
                $avg = $avg / $i;
                $pdo->query("UPDATE Temp_BadHandoverCellTableEveryOneHour SET `平均PRB`=$avg WHERE cell='$cell'");
            }
            
        }

       } 
       //参数--参数 
    function getNumOfDiagnosisDataFilter_parameter($city, $rowArr, $date, $hour, $pdo)
    {
        $startTime = date("Y-m-d", strtotime("-1 day"));
        $endTime = date("Y-m-d");
        // // MRE排名前20名的小区mod3重叠覆盖
        // $db = getConn($city);
        // if($db == "Caught exception: Sybase服务器连接失败！") {
        //     $pdo->query("UPDATE Temp_BadHandoverCellTableEveryOneHour SET `参数`='kgetConnectfailed' WHERE city='$city'");
        //     $pdo->query("UPDATE Temp_BadHandoverCellTableEveryOneHour SET `Polar-参数`='kgetConnectfailed' WHERE city='$city'");
        //    return; 
        // }
        // $sql = "select count(*) from mroPciMod3 where datetime_id like '".$startTime."%' and userLabel in $cell and TotalNumOfSample > 100 and OverlapRate > 0.3";
        // $rs  = $db->query($sql);
        // if($rs) {
        //     $row = $rs->fetch(PDO::FETCH_NUM);
        //     if ($row[0] > 0) {
        //         $value = 100;
        //     }
            $date = new DateTime();    
            $date->sub(new DateInterval('P1D'));
            $yesDate = $date->format('ymd');
            $dbname = 'kget' . $yesDate;//获取昨天的kget数据库
            // $dbc = new DataBaseConnection();
            // $db = $dbc->getDB('mongs', $dbname);
            $db = getParamConn($dbname);
            if ($db == 'Caught exception: Sybase服务器连接失败！') {
                $pdo->query("UPDATE Temp_BadHandoverCellTableEveryOneHour SET `参数`='kgetConnectfailed' WHERE city='$city'");
                $pdo->query("UPDATE Temp_BadHandoverCellTableEveryOneHour SET `Polar-参数`='kgetConnectfailed' WHERE city='$city'");
            } else {
                $cityCh = getCHCity($city, $pdo);
                $subNetwork = getSubNets($cityCh, $pdo);
                foreach ($rowArr as $cell) {
                  $cell = "('".$cell."')";
                  $sql = "select siteName from mongs.siteLte where cellName in $cell";
                  $rs   = $pdo->query($sql);
                  if (!$rs) {
                      continue;
                  }
                  $row  = $rs->fetch(PDO::FETCH_NUM);
                  $meContext = $row[0];
                  //MRE邻区排名前30的邻区有PCI一二阶冲突
                  $filter = " where EutranCellTDD in $cell ";
                  $value = 0;
                  $canshu=0;
                  if ($subNetwork != '') {
                      $filter = $filter." and subNetwork in (" . $subNetwork . ")";
                  }
                  //一阶冲突
                  $sql1 = "select count(*) from TempEUtranCellRelationNeighOfPci".$filter;
                  $rs   = $db->query($sql1);
                  if (!$rs) {
                      continue;
                  }
                  $row  = $rs->fetch(PDO::FETCH_NUM);

                  if ($row[0] > 0) {
                     $canshu=$row[0];
                      $value = 100;
                  } else {
                      //二阶冲突
                      $sql2 = "select count(*) from TempEUtranCellRelationNeighOfNeighPci".$filter;
                      
                      $rs   = $db->query($sql2);
                      if (!$rs) {
                          continue;
                      }
                      $row  = $rs->fetch(PDO::FETCH_NUM);
                      if ($row[0] > 0) {
                         $canshu=$row[0];
                          $value = 100;
                      }
                  }
                  if ($value == 0) {
                      //MME LIST定义不一致
                      $filter = " where meContext = '$meContext'";
                      if ($subNetwork != '') {
                          $filter = $filter." and subNetwork in (" . $subNetwork . ")";
                      }
                      $sql = "select count(*) from TempTermPointToMme_S1_MMEGI_dif ".$filter;
                      $rs   = $db->query($sql);
                      if (!$rs) {
                          continue;
                      }
                      $row  = $rs->fetch(PDO::FETCH_NUM);
                      if ($row[0] > 0) {
                         $canshu=$row[0];
                          $value = 100;
                      }
                  }
                  if ($value == 0) {
                      //X2接口定义不一致
                      $filter = " where meContext = '$meContext'";
                      if ($subNetwork != '') {
                          $filter = $filter." and subNetwork in (" . $subNetwork . ")";
                      }
                      //X2 Used IP检查
                      $sql1 = "select count(*) from TempTermPointToENB_ENBID_usedIpAddress".$filter;
                      $rs   = $db->query($sql1);
                      if (!$rs) {
                          continue;
                      }
                      $row  = $rs->fetch(PDO::FETCH_NUM);
                      if ($row[0] > 0) {
                         $canshu=$row[0];
                          $value = 100;
                      } else {
                          //X2-邻区eNbID检查
                          $sql2 = "select count(*) from TempTermPointToENB_IP".$filter;
                          $rs   = $db->query($sql2);
                          if (!$rs) {
                              continue;
                          }
                          $row  = $rs->fetch(PDO::FETCH_NUM);
                          if ($row[0] > 0) {
                            $canshu=$row[0];
                            $value = 100;
                          }
                      }
                  }
                  if ($value == 0) {
                      //如果S1切换失败的占比高于50%且相关邻区ActivePLMNlist为空
                      $filter = " where meContext = '$meContext'";
                      if ($subNetwork != '') {
                          $filter = $filter." and subNetwork in (" . $subNetwork . ")";
                      }
                      $sql = "select count(*) from TempExternalEUtranCellTDDActivePlmnListCheck ".$filter;
                      $rs   = $db->query($sql);
                      $row  = $rs->fetch(PDO::FETCH_NUM);
                      if ($row[0] > 0) {
                         $canshu=$row[0];
                          $value = 100;
                      }
                  }
                  if ($value == 0) {
                      //切换准备失败次数的占比50%以上发生在，邻区外部定义不一致的邻区
                      // $filter = " where meContext = '$meContext' and ExternalEUtranCellTDDId = '$ecgi_nr'";
                      // if ($subNetwork != '') {
                      //     $filter = $filter." and subNetwork in (" . $subNetwork . ")";
                      // }
                      // $sql = "select count(*) from TempExternalNeigh4G ".$filter;
                      // $rs   = $db->query($sql);
                      // $row  = $rs->fetch(PDO::FETCH_NUM);
                      // if ($row[0] > 0) {
                      //     $value = 100;
                      // }
                  }
                  if ($value == 0) {
                      //如果S1切换失败的占比高于50%，相关TAC前三天定义过不同的MMEGI提示发生过TAC割接

                  }

                  if ($value == 0) {
                      //4G测量频点数量多于5个
                      $filter = " where EutranCellTDD in $cell and freqNum > 5";
                      if ($subNetwork != '') {
                          $filter = $filter." and subNetwork in (" . $subNetwork . ")";
                      }
                      $sql = "select count(*) from TempMeasuringFrequencyTooMuch ".$filter;
                      $rs   = $db->query($sql);
                      if (!$rs) {
                          continue;
                      }
                      $row  = $rs->fetch(PDO::FETCH_NUM);
                      if ($row[0] > 0) {
                         $canshu=$row[0];
                          $value = 50;
                      }
                  }
                  if ($value == 0) {
                      //baseline中A类参数配置不一致的
                      $templateId = 53;
                      $filter = " where templateId='$templateId' and category = 'A' and subNetwork in (".$subNetwork.") and ( cellId in $cell or (meContext = '$meContext' and cellId = ''))";
                      $sql = "select count(*) from ParaCheckBaseline".$filter;
                      $rs   = $db->query($sql);
                      if (!$rs) {
                          continue;
                      }
                      $row  = $rs->fetch(PDO::FETCH_NUM);
                      if ($row[0] > 0) {
                         $canshu=$row[0];
                          $value = 50;
                      }
                  }

                  $pdo->query("UPDATE Temp_BadHandoverCellTableEveryOneHour SET  `参数`=$canshu WHERE cell in $cell");
                  $pdo->query("UPDATE Temp_BadHandoverCellTableEveryOneHour SET `Polar-参数`=$value WHERE cell in $cell ");
                }
                
            }
            
        // }
    }
        
       //极地图
       function getPolarMapData($city, $cell, $date, $hour, $pdo) 
       {
           //告警
        $pdo->query("UPDATE Temp_BadHandoverCellTableEveryOneHour SET `Polar-告警`=0 WHERE `Polar-告警` IS NULL"); 
        $sql = "SELECT cellName as cell,siteName from mongs.siteLte WHERE cellName in $cell;";
        $row = $pdo->query($sql)->fetchall(PDO::FETCH_ASSOC);   
        $erbs = array();
        $cellArr = array();
        foreach ($row as $value) {
            $v = $value['siteName'];
            $cellArr[$v] = $value['cell'];
            array_push($erbs, $v);
        }
        $erbsStr = "('".implode("','", $erbs)."')";
        // $sql = "SELECT meContext,max(num) as num FROM( SELECT meContext,SP_text,t.access AS num,t.alarmNameE FROM Alarm.FMA_alarm_list r LEFT JOIN mongs.AlarmInfo t ON r.SP_text= t.alarmNameE WHERE DATE_FORMAT(Event_time, '%Y-%m-%d %H') >= '" .$date." ".$hour. "' AND meContext in $erbsStr GROUP BY meContext,access ORDER BY access DESC)t; ";
        $sql = "SELECT meContext,max(num) as num FROM( SELECT meContext,SP_text,t.access AS num,t.alarmNameE FROM Alarm.FMA_alarm_list r LEFT JOIN mongs.AlarmInfo t ON r.SP_text= t.alarmNameE WHERE DATE_FORMAT(Event_time, '%Y-%m-%d') >= '" .$date."' AND meContext in $erbsStr GROUP BY meContext,access ORDER BY access DESC)t GROUP BY meContext; ";
        // $row = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        $res = $pdo->query($sql);
        if ($res) {
            $row = $res->fetchAll(PDO::FETCH_ASSOC);
            if (count($row[0]['meContext']) == '') {
                $pdo->query("UPDATE Temp_BadHandoverCellTableEveryOneHour SET `Polar-告警`=0");
            } else {
                foreach ($row as $key => $value) {
                    $num = $value['num'];
                    $erbs = $value['meContext'];
                    $cellStr = $cellArr[$erbs];
                    // $pdo->query("UPDATE Temp_BadHandoverCellTableEveryOneHour SET `Polar-告警`=$num WHERE cell='$cellStr'");
                    getSameErabs($pdo, $city, $erbs, $num, 'Polar-告警');
                }
            }
        }
        

        
        //弱覆盖
         $sql = "SELECT cell,AVG(`RSRP<-116的比例`) AS num FROM badHandoverCell_ex WHERE day_id = '$date' AND hour_id='$hour' AND  cell in $cell group by cell;";
      $row = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
      $weakCoverRateFlag = [];
      foreach ($row as $key => $value) {
            $cellStr = $value['cell'];
            $num = $value['num'];
            if ($num > 20) {
          $pdo->query("UPDATE Temp_BadHandoverCellTableEveryOneHour SET `Polar-弱覆盖`=100 WHERE cell='$cellStr'");
        } else if ($num < 2) {
          $pdo->query("UPDATE Temp_BadHandoverCellTableEveryOneHour SET `Polar-弱覆盖`=0 WHERE cell='$cellStr'");
        } else {
          $num = round($num*100/18, 2);
          if ($num > 100) {
              $pdo->query("UPDATE Temp_BadHandoverCellTableEveryOneHour SET `Polar-弱覆盖`=100 WHERE cell='$cellStr'");
          } else {
              $pdo->query("UPDATE Temp_BadHandoverCellTableEveryOneHour SET `Polar-弱覆盖`=$num WHERE cell='$cellStr'");
          }               
        }
        if ($num > 11) {
            $weakCoverRateFlag[$cellStr] = 1;
        } else {
            $weakCoverRateFlag[$cellStr] = 0;
        }
          }
        
        //重叠覆盖
        $date_from = date("Y-m-d", strtotime("-1 day"));
        $db = getConn($city);
        if ($db ==  'Caught exception: Sybase服务器连接失败！') {
            $pdo->query("UPDATE Temp_BadHandoverCellTableEveryOneHour SET `Polar-重叠覆盖`='MRConnectFailed' WHERE city = '$city'");
        } else {
            $sql = "SELECT cellName,ecgi FROM mongs.siteLte WHERE cellName IN $cell;";
            // $row = $pdo->query($sql, PDO::FETCH_ASSOC)->fetchAll();
            $res = $pdo->query($sql, PDO::FETCH_ASSOC);
            if ($res) {
                $row = $res->fetchAll();
                $ecgiArr = [];
                $cellArr = [];
                foreach ($row as $key => $value) {
                  $ecgiArr[] = $value['ecgi'];
                  $cellArr[$value['ecgi']] = $value['cellName'];
                }
                $ecgiStr = "('".implode("','", $ecgiArr)."')";
                $sql = "SELECT ecgi,rate AS num FROM mroOverCoverage_day WHERE  dateId = '$date_from' AND ecgi in $ecgiStr GROUP BY ecgi;";
                
                // $row = $db->query($sql, PDO::FETCH_ASSOC)->fetchAll();
                $res = $db->query($sql, PDO::FETCH_ASSOC);
                $overCoverFlag = [];
                if ($res) {
                    $row = $res->fetchall();
                    foreach ($row as $key => $value) {
                      $ecgiStr = $value['ecgi'];
                      $num = $value['num'];
                      $cellStr = $cellArr[$ecgiStr];
                      if ($num < 0.2) {
                        $pdo->query("UPDATE Temp_BadHandoverCellTableEveryOneHour SET `Polar-重叠覆盖`=0 WHERE cell='$cellStr'");
                      } elseif ($num > 0.2 && $num < 5) {
                        $num = round($num * (100/(5-0.2)), 2);
                        if ($num > 100) {
                          $num = 100;
                        }
                        $pdo->query("UPDATE Temp_BadHandoverCellTableEveryOneHour SET `Polar-重叠覆盖`=$num WHERE cell='$cellStr'");
                      } else {
                        $pdo->query("UPDATE Temp_BadHandoverCellTableEveryOneHour SET `Polar-重叠覆盖`=100 WHERE cell='$cellStr'");
                      }
                      if ($row[0]['num'] > 5) {
                        $overCoverFlag[$cellStr] = 1;
                      } else {
                        $overCoverFlag[$cellStr] = 0;
                      }
                    }
                }  
            }
        }//end else
        
        
                 
        //质差

        $sql = "SELECT cell,AVG(`RSRQ<-15.5的比例`) AS num FROM badHandoverCell_ex WHERE day_id = '$date' AND hour_id = '$hour' AND cell in $cell group by cell;";
        $row = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        foreach ($row as $key => $value) {
          $cellStr = $value['cell'];
          $num = $value['num'];
          if ($num == 0) {
            $pdo->query("UPDATE Temp_BadHandoverCellTableEveryOneHour SET `Polar-质差`=0 WHERE cell='$cellStr'");
          } else {
            if ($weakCoverRateFlag[$cellStr] == 0) {
              if ($overCoverFlag[$cellStr] == 0) {
                $point = $num*5;
                if ($point >100) {
                  $pdo->query("UPDATE Temp_BadHandoverCellTableEveryOneHour SET `Polar-质差`=100 WHERE cell='$cellStr'");
                } else {
                  $pdo->query("UPDATE Temp_BadHandoverCellTableEveryOneHour SET `Polar-质差`=$point WHERE cell='$cellStr'");
                }   
              } else {
                $point = 50 + $num*2.5;
                if ($point >100) {
                  $pdo->query("UPDATE Temp_BadHandoverCellTableEveryOneHour SET `Polar-质差`=100 WHERE cell='$cellStr'");
                } else {
                  $pdo->query("UPDATE Temp_BadHandoverCellTableEveryOneHour SET `Polar-质差`=$point WHERE cell='$cellStr'");
                } 
              }
            } else {
              $point = 50 + $num*2.5;
              if ($point >100) {
                $pdo->query("UPDATE Temp_BadHandoverCellTableEveryOneHour SET `Polar-质差`=100 WHERE cell='$cellStr'");
              } else {
                $pdo->query("UPDATE Temp_BadHandoverCellTableEveryOneHour SET `Polar-质差`=$point WHERE cell='$cellStr'");
              } 
            }
          }
        }
       
        //邻区
        $db = getConn($city);
        if ($db == 'Caught exception: Sybase服务器连接失败！') {
          $pdo->query("UPDATE Temp_BadHandoverCellTableEveryOneHour SET `Polar-邻区`='MRConnectFailed' WHERE city = '$city'");
        } else {
            $date_from = date("Y-m-d", strtotime("-1 day"));
            $sql = "select cellName,ecgi from mongs.siteLte where cellName in $cell";
            $res = $pdo->query($sql);
            if (!$res) {
              return;
            }
            $row = $res->fetchall(PDO::FETCH_ASSOC);
            $ecgiArr = [];
            $ecgi = [];
            foreach ($row as $value) {
              $ecgiArr[$value['ecgi']] = $value['cellName'];
              array_push($ecgi, $value['ecgi']);
            }
            $ecgiStr = implode("','", $ecgi);
            $ecgiStr = "('".$ecgiStr."')";
            $table = 'mreServeNeigh_day';
            $sqlNum = "select ecgi,count(*) as num from $table where isdefined_direct=0 and ecgi in $ecgiStr AND distance_direct<0.8 and dateId >= '$date_from' GROUP by ecgi;";
            // $row = $db->query($sqlNum)->fetchAll(PDO::FETCH_ASSOC);
            $res = $db->query($sqlNum);
            if (!$res) {
                return;
            }
            $row = $res->fetchAll(PDO::FETCH_ASSOC);
            foreach ($row as $key => $value) {
              $ecgi = $value['ecgi'];
              $num = $value['num'];
              $cellStr = $ecgiArr[$ecgi];
              if ($weakCoverRateFlag[$cellStr] == 1) {
                $num = intval($row[0]['num']*10);
                if ($num > 100) {
                    $pdo->query("UPDATE Temp_BadHandoverCellTableEveryOneHour SET `Polar-邻区`=100 WHERE cell='$cellStr'");
                } else {
                    $pdo->query("UPDATE Temp_BadHandoverCellTableEveryOneHour SET `Polar-邻区`=$num WHERE cell='$cellStr'");
                }     
              } else {
                $num = intval(50+$row[0]['num']*5);
                if ($num > 100) {
                    $pdo->query("UPDATE Temp_BadHandoverCellTableEveryOneHour SET `Polar-邻区`=100 WHERE cell='$cellStr'");
                } else {
                    $pdo->query("UPDATE Temp_BadHandoverCellTableEveryOneHour SET `Polar-邻区`=$num WHERE cell='$cellStr'");
                }
              }
            }
        }
       
        //干扰
        $sql = "SELECT cell,
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
                    cell in $cell
                AND day_id = '$date'
                AND hour_id = '$hour'
                GROUP BY cell;";
        $row = $pdo->query($sql)->fetchAll(PDO::FETCH_NUM);
        foreach ($row as $key => $value) {
          $cellStr = $value[0];
          $j = 100;
          $avg = 0;
          // print_r($value);
          for ($i=1; $i < count($value); $i++) { 
          //foreach ($value as $val) {
            if ($value[$i] == null) {
              $j--;
              continue;
            }
            $avg = $avg + $value[$i];
          }
          if ($j == 0) {
            $pdo->query("UPDATE Temp_BadHandoverCellTableEveryOneHour SET `Polar-干扰`=0 WHERE cell='$cellStr'");
          } else {
            $avg = $avg / $j;
            if ($avg >= -102) {
              $pdo->query("UPDATE Temp_BadHandoverCellTableEveryOneHour SET `Polar-干扰`=100 WHERE cell='$cellStr'");
            } elseif ($avg <= -112) {
              $pdo->query("UPDATE Temp_BadHandoverCellTableEveryOneHour SET `Polar-干扰`=0 WHERE cell='$cellStr'");
            } else {
              $point = ($avg + 112)*10;
              if ($point > 100) {
                $pdo->query("UPDATE Temp_BadHandoverCellTableEveryOneHour SET `Polar-干扰`=100 WHERE cell='$cellStr'");
              } else {
                $pdo->query("UPDATE Temp_BadHandoverCellTableEveryOneHour SET `Polar-干扰`=$point WHERE cell='$cellStr'");
              }
            }
          }
        }
       
        //高话务
        $sql = "SELECT cell,SUM(`最大RRC连接用户数`)/20 AS num FROM badHandoverCell_ex WHERE cell in $cell AND day_id = '$date' AND hour_id='$hour' GROUP BY cell;";
        $row = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        foreach ($row as $key => $value) {
          $cellStr = $value['cell'];
          $num = $value['num'];
          if ($num > 100) {
            $pdo->query("UPDATE Temp_BadHandoverCellTableEveryOneHour SET `Polar-高话务`=100 WHERE cell='$cellStr'");
          } else {
            $point = intval($num);
            $pdo->query("UPDATE Temp_BadHandoverCellTableEveryOneHour SET `Polar-高话务`=$point WHERE cell='$cellStr'");
          }
        }
       
        //参数
        // $yesDate = date("ymd",strtotime("-1 day"));
        // $dbname = 'kget' . $yesDate;
        // $table = 'ParaCheckBaseline';
        // $sql = "select cellName,siteName from mongs.siteLte where cellName in $cell;";
        // $row = $pdo->query($sql)->fetchall(PDO::FETCH_ASSOC);
        // $erabArr = [];
        // $cellArr = [];
        // foreach ($row as $key => $value) {
        //   $erabArr[] = $value['siteName'];
        //   $cellArr[$value['siteName']] = $value['cellName'];
        // }
        // $erabStr = "('".implode("','", $erabArr)."')";
        // $db = getParamConn($dbname);
        // $sql = "select meContext,highTraffic from  $table where meContext in $erabStr GROUP by meContext;";
        // $row = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        // foreach ($row as $key => $value) {
        //   $cellStr = $cellArr[$value['meContext']];
        //   $flag = $value['highTraffic'];
        //   if($flag == 'YES') {
        //     $pdo->query("UPDATE Temp_BadHandoverCellTableEveryOneHour SET `Polar-参数`=100 WHERE cell='$cellStr'");
        //   } else {
        //     $sql = "select count(*) as num from  $table where cellId='$cellStr';";
        //     $row_count = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        //     if(count($row_count) == 0) {
        //       $pdo->query("UPDATE Temp_BadHandoverCellTableEveryOneHour SET `Polar-参数`=0 WHERE cell='$cellStr'");
        //     }else {
        //       if($row_count[0]['num'] == 0) {
        //           $pdo->query("UPDATE Temp_BadHandoverCellTableEveryOneHour SET `Polar-参数`=0 WHERE cell='$cellStr'");
        //       }else {
        //           $pdo->query("UPDATE Temp_BadHandoverCellTableEveryOneHour SET `Polar-参数`=50 WHERE cell='$cellStr'");
        //       }
        //     }  
        //   }
        // }
        
       }
    //相关性--切换成功率_准备切换成功率
    function getBadHandover_prepare($cell, $date, $hour, $pdo, $rowArr)  
    {
        $startTime = date('Y-m-d', strtotime("-1 day")); 
        $endTime = date('Y-m-d');
        $value_x = [];
        $value_y = [];
        $sql = "SELECT
                    day_id,
                    hour_id,
                    cell,
                    切换成功率,
                    准备切换成功率
                FROM
                    badHandoverCell
                WHERE
                    day_id >= '" . $startTime . "'
                AND day_id <= '" . $endTime . "'
                AND cell in $cell 
                GROUP BY  cell,day_id, hour_id;";
        $res = $pdo->query($sql);
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $cellStr = $row['cell'];
            $value_x[$cellStr][] = $row['切换成功率'];
            $value_y[$cellStr][] = $row['准备切换成功率'];
        }
        $data= getRelevanceData($value_x, $value_y, $rowArr);
         foreach ($data as $key => $value) {
            $pdo->query("UPDATE Temp_BadHandoverCellTableEveryOneHour SET `切换成功率_准备切换成功率`='$value' WHERE cell='$key'");
        }
    }              
    //相关性--切换成功率_执行切换成功率            
    function getBadHandover_exec($cell, $date, $hour, $pdo, $rowArr)
    {
        $startTime = date('Y-m-d', strtotime("-1 day")); 
        $endTime = date('Y-m-d');
        $value_x = [];
        $value_y = [];
        $sql = "SELECT
                    day_id,
                    hour_id,
                    cell,
                    切换成功率,
                    执行切换成功率
                FROM
                    badHandoverCell
                WHERE
                    day_id >= '" . $startTime . "'
                AND day_id <= '" . $endTime . "'
                AND cell in $cell
                GROUP BY cell,day_id, hour_id;";
        $res = $pdo->query($sql);
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
           $cellStr = $row['cell'];
            $value_x[$cellStr][] = $row['切换成功率'];
            $value_y[$cellStr][] = $row['执行切换成功率'];
        }
        $data= getRelevanceData($value_x, $value_y, $rowArr);
          foreach ($data as $key => $value) {
            $pdo->query("UPDATE Temp_BadHandoverCellTableEveryOneHour SET `切换成功率_执行切换成功率`='$value' WHERE cell='$key'");
        }
       
    } 
    //相关性--切换成功率&干扰  
    function geBadHandover_interfere($cell, $date, $hour, $pdo, $rowArr)
    {
        $startTime = date('Y-m-d', strtotime("-1 day")); 
        $endTime = date('Y-m-d');
        $value_x = [];
        $value_y = [];
        $sql = "SELECT cell,day_id,hour_id,
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
                    cell in $cell
                AND day_id >= '" . $startTime . "'
                AND day_id <= '" . $endTime . "'
                GROUP BY day_id,hour_id,cell
                ORDER BY cell,day_id,hour_id;";
                //++
                $sqlNum = "SELECT  count(cell) as num FROM interfereCell WHERE cell IN $cell AND day_id>='" . $startTime . "' AND day_id <= '" . $endTime . "'GROUP BY day_id,hour_id LIMIT 1;";
        $rowCellNum = $pdo->query($sqlNum)->fetchAll(PDO::FETCH_ASSOC);
        $cellNum = $rowCellNum[0]['num'];

        //++
        $res = $pdo->query($sql);   //干扰
        $value_x = [];
        $value_y = [];
        while ($row = $res->fetch(PDO::FETCH_NUM)) {
            $cellStr = $row[0];
            $avg = 0;
            $j = 100;
            for ($i=0; $i<count($row); $i++) {
                if ($i>1) {
                    if ($row[$i] == null) {
                      $j--;
                        continue;
                    }
                    $avg = $avg + $row[$i];
                }
            }
            if ($j == 0) {
                $value_y[$cellStr][] = 0;
            } else {
                $avg = $avg / $j;
                $value_y[$cellStr][] = $avg;
            }
        }

        $sql = " SELECT
                    day_id,
                    hour_id,
                    cell,
                    切换成功率
                FROM
                    badHandoverCell
                WHERE
                    cell in $cell 
                AND day_id >= '" . $startTime . "'
                AND day_id <= '" . $endTime . "'
                GROUP BY
                    cell,
                    day_id,
                    hour_id";
        $res = $pdo->query($sql);
        while ($row = $res->fetch(PDO::FETCH_NUM)) {
            $cellStr = $row[2];
            $value_x[$cellStr][] = $row[3];
        }            
        $data= getRelevanceData($value_x, $value_y, $rowArr);

        foreach ($data as $key => $value) {
            $pdo->query("UPDATE Temp_BadHandoverCellTableEveryOneHour SET `切换成功率_干扰`='$value' WHERE cell='$key'");
        }
       
    }    
    //相关性--切换成功率&质差    
    function getBadHandover_zhicha($cell, $date, $hour, $pdo, $rowArr)
    {
        $startTime = date('Y-m-d', strtotime("-1 day")); 
        $endTime = date('Y-m-d');
        $value_x = [];
        $value_y = [];
        $sql = "SELECT
                    day_id,
                    hour_id,
                    cell,
                    切换成功率,
                    AVG(`RSRQ<-15.5的比例`) AS num
                FROM
                    badHandoverCell
                WHERE
                    day_id >= '" . $startTime . "'
                AND day_id <= '" . $endTime . "'
                AND cell in $cell
                GROUP BY cell,day_id, hour_id;";
        $res = $pdo->query($sql);
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $cellStr = $row['cell'];
            $value_x[$cellStr][] = $row['切换成功率'];
            $value_y[$cellStr][] = $row['num'];
            // array_push($value_x, $row['切换成功率']);
            // array_push($value_y, $row['num']);
        }
        $data = getRelevanceData($value_x, $value_y, $rowArr);
        // print_r($value_x);
        foreach ($data as $key => $value) {
            $pdo->query("UPDATE Temp_BadHandoverCellTableEveryOneHour SET `切换成功率_质差`='$value' WHERE cell='$key'");
        }
       
    }                                             

    function getRelevanceData($x, $y, $cell) {
        $numArr = [];
        foreach ($cell as $value) {
            if (!array_key_exists($value, $x)) {
                $numArr[$value] = 0;
            } elseif (!array_key_exists($value, $y)) {
                $numArr[$value] = 0;
             } elseif (count($x[$value]) != count($y[$value])) {
                $numArr[$value] = 0;
            } else {
                $xSquare = [];
                $ySquare = [];
                $xySquare = [];
                $xySum = 0;
                $xSum = 0;
                $ySum = 0;
                $xxSum = 0;
                $yySum = 0;
                $count = count($x[$value]);
                for ($i=0,$j=0; $i<count($x[$value]),$j<count($y[$value]); $i++,$j++) {
                    if (count($x[$value])==count($y[$value])) {
                        $xx = $x[$value][$i] * $x[$value][$i];
                        $yy = $y[$value][$j] * $y[$value][$j];
                        $xy = $x[$value][$i] * $y[$value][$j];
                        $xySum = $xySum + $xy;  //xy之和
                        $xSum = $xSum + $x[$value][$i];     //x之和
                        $ySum = $ySum + $y[$value][$j];     //y之和
                        $xxSum = $xxSum + $xx;  //xx之和
                        $yySum = $yySum + $yy;  //yy之和
                    } else {
                        $numArr[$value][] = 0;
                    }  
                }
                if ($xSum == 0 || $ySum == 0||(sqrt($xxSum*$count-$xSum*$xSum)*sqrt($yySum*$count-$ySum*$ySum))==0) {
                    $numArr[$value] = 0;
                } else {
                    $numArr[$value] = abs(round((($xySum*$count-$xSum*$ySum)/(sqrt($xxSum*$count-$xSum*$xSum)*sqrt($yySum*$count-$ySum*$ySum))), 2));
                }
            }

        }
        return $numArr;
    }                           

       function getParamConn($kget)
       {
           try {
            return new PDO("mysql:host=10.39.148.187;dbname=$kget", "root", "mongs");
        } catch(Exception $e) {
            return 'Caught exception: Sybase服务器连接失败！';
        }           
           // return new PDO("mysql:host=10.39.148.187;dbname=$kget", "root", "mongs");
       }

      function getConn($city)
      {
          $dbname = '';
        if ($city == 'changzhou') {
            $dbname = 'MR_CZ';
            try {
              return new PDO("mysql:host=10.40.57.134:8066;dbname=$dbname", "mr", "mr");
            } catch (Exception $e) {
              return 'Caught exception: Sybase服务器连接失败！';
            }
        } elseif ($city == 'nantong') {
            $dbname = 'MR_NT';
            try {
              return new PDO("mysql:host=10.40.57.134:8066;dbname=$dbname", "mr", "mr");
            } catch (Exception $e) {
              return 'Caught exception: Sybase服务器连接失败！';
            }
        } elseif ($city == 'suzhou') {
            $dbname = 'MR_SZ';
            try {
             return new PDO("mysql:host=10.40.57.134:8066;dbname=$dbname", "mr", "mr");
            } catch (Exception $e) {
              return 'Caught exception: Sybase服务器连接失败！';
            }   
        } elseif ($city == 'wuxi') {
            $dbname = 'MR_WX';
            try {
              return new PDO("mysql:host=10.40.57.134:8066;dbname=$dbname", "mr", "mr");
            } catch (Exception $e) {
              return 'Caught exception: Sybase服务器连接失败！';
            }
        } elseif ($city == 'zhenjiang') {
            $dbname = 'MR_ZJ';
            try {
             return new PDO("mysql:host=10.40.57.134:8066;dbname=$dbname", "mr", "mr");
            } catch (Exception $e) {
              return 'Caught exception: Sybase服务器连接失败！';
            }
        }
       
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

    function getSameErabs($pdo, $city, $erab, $num, $type) {
      $sql = "SELECT cell,siteName from Temp_BadHandoverCellTableEveryOneHour,mongs.siteLte WHERE Temp_BadHandoverCellTableEveryOneHour.cell = mongs.siteLte.cellName AND siteName='$erab' AND Temp_BadHandoverCellTableEveryOneHour.city='$city';";
      $row = $pdo->query($sql)->fetchall(PDO::FETCH_ASSOC);
      foreach ($row as $value) {
        $cell = $value['cell'];
        $pdo->query("UPDATE Temp_BadHandoverCellTableEveryOneHour SET `$type`=$num WHERE cell='$cell'");
      }
    }