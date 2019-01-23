<?php
    $city = trim($argv[1]);

    $sql = "CREATE TABLE IF NOT EXISTS `Temp_NeighBadHandoverCellTableEveryOneHour` (
              `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
              `day_id` varchar(25) DEFAULT NULL,
              `hour_id` varchar(25) DEFAULT NULL,
              `city` varchar(25) DEFAULT NULL,
              `subNetwork` varchar(25) DEFAULT NULL,
              `site` varchar(25) DEFAULT NULL,
              `cell` varchar(25) DEFAULT NULL,
              `relation` varchar(25) DEFAULT NULL,
              `切换成功率` varchar(25) DEFAULT NULL,
              `准备切换失败数` varchar(25) DEFAULT NULL,
              `执行切换失败数` varchar(25) DEFAULT NULL,
              `准备切换成功率` decimal(18,0) DEFAULT NULL,
              `执行切换成功率` decimal(18,0) DEFAULT NULL,
              `同频切换成功率` decimal(18,0) DEFAULT NULL,
              `异频切换成功率` decimal(18,0) DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `hour_index` (`hour_id`),
              KEY `city_index` (`city`),
              KEY `subNet_index` (`subNetwork`),
              KEY `cell_index` (`cell`)
            ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;";

    $dbA = $db = new PDO("mysql:host=localhost;dbname=AutoKPI", 'root', 'mongs');        
    $sql = "select count(distinct city) as num from Temp_NeighBadHandoverCellTableEveryOneHour";
    $res=$dbA->query($sql);
    $row=$res->fetch();
    // print_r($row);
    // return;
    if ($row&&$row['num']>=5) {
        $sql = "TRUNCATE TABLE Temp_NeighBadHandoverCellTableEveryOneHour;";
        $dbA->query($sql);
    }                
    // $dbA = $db = new PDO("mysql:host=localhost;dbname=AutoKPI", 'root', 'mongs');
    // $dbA->query($sql);
    // $sql = "TRUNCATE TABLE Temp_NeighBadHandoverCellTableEveryOneHour;";
    // $dbA->query($sql);

    $cityArr = [];
    if ($city == 'changzhou') {
        $cityArr = ['changzhou', 'changzhou1'];
    } else if ($city == 'nantong') {
        $cityArr = ['nantong', 'nantong1'];
    } else {
        $cityArr=array($city);
    }
    $date = date("Y-m-d");
    $hour = date("H", strtotime("-1 hour"));
    $db = new PDO("mysql:host=localhost;dbname=mongs", 'root', 'mongs');
    $sql = "SELECT
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
    $cells = $dbA->query($sql)->fetchAll(PDO::FETCH_ASSOC);

    // print_r($cells);

    $cellArr = [];
    foreach ($cells as $value) {
        array_push($cellArr, $value['cell']);
    }
    $cell = "'".implode("','", $cellArr)."'";

    foreach ($cityArr as $dbServer) {
        $sql = "SELECT * FROM databaseconn WHERE connName='$dbServer'";
        $dbServers = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        foreach ($dbServers as $dbs) {
            $host = $dbs["host"];
            $port = $dbs["port"];
            $dbName = $dbs["dbName"];
            $userName = $dbs["userName"];
            $password = $dbs["password"];
            $subNets = getSubNets($db, $dbs["connName"]);
            $subNets = "'".str_replace(",", "','", $subNets)."'";
            // print_r($subNets);return;
            $pmDbDSN = "dblib:host=" . $host . ":" . $port . ";".((float)phpversion()>7.0?'dbName':'dbname')."=" . $dbName;
        
            try {
                $pmDB = new PDO($pmDbDSN, $userName, $password);
            }
            catch (PDOException $exception) {
                echo "Failed to connect server $pmDbDSN\n";
                continue;
            }
            // $resultText = "";
            queryTemplate($dbServer, $pmDB, $dbA, $subNets, $date, $hour, $cell);
        }
        // print_r($dbServer);
    }


    // sleep(10);
 //    $sql = "TRUNCATE TABLE NeighBadHandoverCellTableEveryOneHour;";
 //    $dbA->query($sql);
 //    $sql = "INSERT INTO NeighBadHandoverCellTableEveryOneHour SELECT * FROM Temp_NeighBadHandoverCellTableEveryOneHour;";
 //    $dbA->query($sql);

    function queryTemplate($city, $pmDB, $dbA, $subNets, $date, $hour, $cell)
    {
        if ($city == 'changzhou1') {
            $city = 'changzhou';
        }
        if ($city == 'nantong1') {
            $city = 'nantong';
        }
        $sql = "select AGG_TABLE0.day,AGG_TABLE0.hour,'$city',AGG_TABLE0.subNet,AGG_TABLE0.site,AGG_TABLE0.location,AGG_TABLE0.relation,cast(100*(0.0000001+pmHoPrepSuccLteIntraF+pmHoPrepSuccLteInterF)/(pmHoPrepAttLteIntraF+pmHoPrepAttLteInterF)*(0.0000001+pmHoExeSuccLteIntraF+pmHoExeSuccLteInterF)/(pmHoExeAttLteIntraF+pmHoExeAttLteInterF) as decimal(18,2)) as kpi0,cast((pmHoPrepAttLteInterF-pmHoPrepSuccLteInterF)+(pmHoPrepAttLteIntraF-pmHoPrepSuccLteIntraF) as decimal(18,0)) as kpi1,cast((pmHoExeAttLteIntraF-pmHoExeSuccLteIntraF)+(pmHoExeAttLteInterF-pmHoExeSuccLteInterF) as decimal(18,0)) as kpi2,cast(100*(pmHoPrepSuccLteIntraF+pmHoPrepSuccLteInterF)/(pmHoPrepAttLteIntraF+pmHoPrepAttLteInterF) as decimal(18,2)) as kpi3,cast(100*(pmHoExeSuccLteIntraF+pmHoExeSuccLteInterF)/(pmHoExeAttLteIntraF+pmHoExeAttLteInterF) as decimal(18,2)) as kpi4,cast(100*(pmHoPrepSuccLteIntraF+pmHoExeSuccLteIntraF)/(pmHoExeAttLteIntraF+pmHoPrepAttLteIntraF) as decimal(18,2)) as kpi5,cast(100*(pmHoPrepSuccLteInterF+pmHoExeSuccLteInterF)/(pmHoExeAttLteInterF+pmHoPrepAttLteInterF) as decimal(18,2)) as kpi6 from (select convert(char(10),date_id) as day,hour_id as hour,substring(SN,charindex('=',substring(SN,32,25))+32,charindex(',',substring(SN,32,25))-charindex('=',substring(SN,32,25))-1) as subNet,substring(substring(SN,charindex (',', substring(SN, 32, 25)) + 32),11,25) as site,EutranCellTDD as location,EUtranCellRelation as relation,COUNT(DISTINCT(EutranCellTDD)) AS cellNum, COUNT(DISTINCT(hour_id)) AS HourTotal,sum(pmHoPrepSuccLteIntraF) as 'pmHoPrepSuccLteIntraF',sum(pmHoPrepSuccLteInterF) as 'pmHoPrepSuccLteInterF',sum(pmHoPrepAttLteIntraF) as 'pmHoPrepAttLteIntraF',sum(pmHoPrepAttLteInterF) as 'pmHoPrepAttLteInterF',sum(pmHoExeSuccLteIntraF) as 'pmHoExeSuccLteIntraF',sum(pmHoExeSuccLteInterF) as 'pmHoExeSuccLteInterF',sum(pmHoExeAttLteIntraF) as 'pmHoExeAttLteIntraF',sum(pmHoExeAttLteInterF) as 'pmHoExeAttLteInterF' from dc.DC_E_ERBS_EUTRANCELLRELATION_raw where date_id>='$date' and substring(SN,charindex('=',substring(SN,32,25))+32,charindex(',',substring(SN,32,25))-charindex('=',substring(SN,32,25))-1) in ($subNets) and EutranCellTDD in ($cell) and hour_id in ($hour) group by date_id,hour_id,SN,location,relation)as AGG_TABLE0 order by AGG_TABLE0.day,AGG_TABLE0.hour;";
        // print_r($sql.'            ');return;
        
        $rows = $pmDB->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        $arr = '';
        $strs = '';
        foreach ($rows as $value) {
            $str = '';
            foreach ($value as $val) {
                $str = $str .'"'. $val . '",';
            }
            $strs = $strs . '(null,'.substr($str, 0, strlen($str)-1).'),';
            
        }
        $strs = substr($strs, 0, strlen($strs)-1);
        // print_r($strs);
        $sql = "INSERT INTO Temp_NeighBadHandoverCellTableEveryOneHour VALUES $strs;";
        // print_r($sql);
        $dbA->query($sql);
    }

    function getSubNets($db, $city)
    {
        $SQL = "select subNetwork from databaseconn where connName = '$city'";
        $res = $db->query($SQL);
        $row = $res->fetch(PDO::FETCH_ASSOC);
        $subNets = $row['subNetwork'];
        return $subNets;
    }
