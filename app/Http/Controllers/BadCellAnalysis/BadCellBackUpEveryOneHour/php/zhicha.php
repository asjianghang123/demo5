<?php
    function insertData_zhicha($pdo, $erbs, $erbsStr, $cellStr, $date, $hour, $txt, $table, $table_all, $weakCoverRateFlag, $overCoverFlag) {
        $pdo->query("UPDATE $table SET `RSRQ<-15.5的比例`=0 WHERE `RSRQ<-15.5的比例` IS NULL");
        $pdo->query("UPDATE $table SET `Polar-质差`=0 WHERE `Polar-质差` IS NULL");
        $sql = "SELECT cell,AVG(`RSRQ<-15.5的比例`) AS NUM FROM $table_all WHERE cell in $cellStr AND day_id = '$date' AND hour_id='$hour' group by cell;";
        $res = $pdo->query($sql, PDO::FETCH_ASSOC);
        if ($res) {
            $row = $res->fetchall();
            foreach ($row as $key => $value) {
                $cell = $value['cell'];
                $num = $value['NUM'];
                $pdo->query("UPDATE $table SET `RSRQ<-15.5的比例`=$num WHERE cell='$cell'");
                $polarValue = 0;
                if (array_key_exists($cell, $weakCoverRateFlag) && $weakCoverRateFlag[$cell] == 0) {
                    if (array_key_exists($cell, $overCoverFlag) && $overCoverFlag[$cell] == 0) {
                        $polarValue = $num*5;
                        if ($polarValue > 100) {
                            $polarValue = 100;
                        }
                    } else {
                        $polarValue = 50 + $num*2.5;
                        if ($polarValue > 100) {
                            $polarValue = 100;
                        }
                    }
                } else {
                    $polarValue = 50 + $num*2.5;
                    if ($polarValue > 100) {
                        $polarValue = 100;
                    }
                }
                $pdo->query("UPDATE $table SET `Polar-质差`=$polarValue WHERE cell='$cell'");
            }
        }
    }
