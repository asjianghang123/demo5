<?php
    function insertData_mr_overlapCover($pdo, $mr_pdo, $city, $database, $erbs, $erbsStr, $cellStr, $table, $tab) {
        $pdo->query("UPDATE $table SET `重叠覆盖度`=0 WHERE `重叠覆盖度` IS NULL AND city='$city'");
        $pdo->query("UPDATE $table SET `Polar-重叠覆盖`=0 WHERE `Polar-重叠覆盖` IS NULL AND city='$city'");
        $date_from = date("Y-m-d", strtotime("-1 day"));
        $sql = "SELECT cellName,ecgi FROM mongs.siteLte WHERE cellName IN $cellStr GROUP BY cellName;";
        $overCoverFlag = [];
        $res = $pdo->query($sql, PDO::FETCH_ASSOC);
        if ($res) {
            $row = $res->fetchall();
            $ecgiArr = [];
            $cellArr = [];
            foreach ($row as $key => $value) {
                $ecgiArr[] = $value['ecgi'];
                $cellArr[$value['ecgi']] = $value['cellName'];
            }
            $ecgiStr = "('".implode("','", $ecgiArr)."')";
            $mr_pdo->query("use $database;");
            $sql = "SELECT ecgi,rate AS num FROM mroOverCoverage_day WHERE  dateId = '$date_from' AND ecgi in $ecgiStr GROUP BY ecgi;";
            $res = $mr_pdo->query($sql, PDO::FETCH_ASSOC);  
            if ($res) {
                $row = $res->fetchall();
                foreach ($row as $key => $value) {
                    $ecgiStr = $value['ecgi'];
                    $num = $value['num'];
                    $cell = $cellArr[$ecgiStr];
                    $pdo->query("UPDATE $table SET `重叠覆盖度`=$num WHERE cell='$cell'");
                    $polarValue = 0;
                    if ($num <= 0.2) {
                        $polarValue = 0;
                    } elseif ($num > 0.2 && $num < 5) {
                        $polarValue = round(($num-0.2) * 100/4.8, 2);
                    } else {
                        $polarValue = 100;
                    }
                    $pdo->query("UPDATE $table SET `Polar-重叠覆盖`=$polarValue WHERE cell='$cell'");
                    if ($num > 5) {
                        $overCoverFlag[$cell] = 1;
                    } else {
                        $overCoverFlag[$cell] = 0;
                    }
                } 
            }  
        }
        return $overCoverFlag;
    }