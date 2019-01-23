<?php
    function insertData_weakCover($pdo, $erbs, $erbsStr, $cellStr, $date, $hour, $txt, $table, $table_all) {
        $pdo->query("UPDATE $table SET `RSRP<-116的比例`=0 WHERE `RSRP<-116的比例` IS NULL"); 
        $pdo->query("UPDATE $table SET `Polar-弱覆盖`=0 WHERE `Polar-弱覆盖` IS NULL"); 
        $sql = "SELECT cell,AVG(`RSRP<-116的比例`) as NUM from $table_all where day_id >= '$date' AND cell in $cellStr AND hour_id='$hour' group by cell;";
        // print_r($sql);
        $res = $pdo->query($sql, PDO::FETCH_ASSOC);
        $weakCoverRateFlag = [];
        if ($res) {
            $row = $res->fetchall();
            foreach ($row as $key => $value) {
                $cell = $value['cell'];
                $num = $value['NUM'];
                $pdo->query("UPDATE $table SET `RSRP<-116的比例`=$num WHERE cell='$cell'");
                $polarValue = 0;
                if ($num > 20) {
                    $polarValue = 100;
                } elseif ($num < 2) {
                    $polarValue = 0;
                } else {
                    $polarValue = round(($num-2)*100/18, 2); 
                }
                $pdo->query("UPDATE $table SET `Polar-弱覆盖`=$polarValue WHERE cell='$cell'");

                if ($num > 11) {
                    $weakCoverRateFlag[$cell] = 1;
                } else {
                    $weakCoverRateFlag[$cell] = 0;
                }
            }
        }
        return $weakCoverRateFlag;
    }