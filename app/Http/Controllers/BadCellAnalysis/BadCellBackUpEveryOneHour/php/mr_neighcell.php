<?php
    function insertData_mr_neighCell($pdo, $mr_pdo, $city, $database, $erbs, $erbsStr, $cellStr, $table, $tab, $weakCoverRateFlag) {
        $pdo->query("UPDATE $table SET `需要加邻区数量`=0 WHERE `需要加邻区数量` IS NULL AND city='$city'");
        $pdo->query("UPDATE $table SET `Polar-邻区`=0 WHERE `Polar-邻区` IS NULL AND city='$city'");
        $date_from = date("Y-m-d", strtotime("-1 day"));
        $sql = "select cellName,ecgi from mongs.siteLte where cellName in $cellStr";
        $res = $pdo->query($sql);
        if ($res) {
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
            $tables = 'mreServeNeigh_day';
            $mr_pdo->query("use $database;");
            $sql = "SELECT ecgi,count(*) as num from $tables where isdefined_direct=0 and ecgi in $ecgiStr AND distance_direct<0.8 and dateId >= '" . $date_from . "'group by ecgi;";
            $res = $mr_pdo->query($sql, PDO::FETCH_ASSOC);
            if ($res) {
                $row = $res->fetchall();
                foreach ($row as $key => $value) {
                    $num = $value['num'];
                    $cell = $ecgiArr[$value['ecgi']];
                    $pdo->query("UPDATE $table SET `需要加邻区数量`=$num WHERE cell='$cell'");
                    $polarValue = 0;
                    if ($tab == 'lowAccessCell') {
                        if (array_key_exists($cell, $weakCoverRateFlag) && $weakCoverRateFlag[$cell] == 1) {
                            $polarValue = intval($num*5);
                            if ($polarValue > 50) {
                                $polarValue = 50;
                            }
                        } else {
                            $polarValue = intval($num*10);
                            if ($polarValue > 100) {
                                $polarValue = 100;
                            }
                        }
                    } elseif ($tab == 'badHandoverCell') {
                        if (array_key_exists($cell, $weakCoverRateFlag) && $weakCoverRateFlag[$cell] == 1) {
                            $polarValue = intval($num*25);
                            if ($polarValue > 50) {
                                $polarValue = 50;
                            }
                        } else {
                            $polarValue = intval($num*50);
                            if ($polarValue > 100) {
                                $polarValue = 100;
                            }
                        }
                    } elseif ($tab == 'highLostCell') {
                        if (array_key_exists($cell, $weakCoverRateFlag) && $weakCoverRateFlag[$cell] == 1) {
                            $polarValue = intval($num*50/12);
                            if ($polarValue > 50) {
                                $polarValue = 50;
                            }
                        } else {
                            $polarValue = intval($num*100/12);
                            if ($polarValue > 100) {
                                $polarValue = 100;
                            }
                        }
                    }
                    
                    $pdo->query("UPDATE $table SET `Polar-邻区`=$polarValue WHERE cell='$cell'");
                }
            }
        }
        
    }