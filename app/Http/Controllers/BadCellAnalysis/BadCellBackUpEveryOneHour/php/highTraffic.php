<?php
    function insertData_highTraffic($pdo, $erbs, $erbsStr, $cellStr, $date, $hour, $txt, $table, $table_all) {
        $pdo->query("UPDATE $table SET `最高RRC用户数`=0 WHERE `最高RRC用户数` IS NULL");
        $pdo->query("UPDATE $table SET `MAC层时延`=0 WHERE `MAC层时延` IS NULL");
        $pdo->query("UPDATE $table SET `SRcongestion数`=0 WHERE `SRcongestion数` IS NULL");
        $pdo->query("UPDATE $table SET `SR拥塞比`=0 WHERE `SR拥塞比` IS NULL");
        $pdo->query("UPDATE $table SET `下行CQI<3的比例`=0 WHERE `下行CQI<3的比例` IS NULL");
        $pdo->query("UPDATE $table SET `Polar-最高RRC用户数`=0 WHERE `Polar-最高RRC用户数` IS NULL");
        $sql = "SELECT cell,SUM(`最大RRC连接用户数`) AS NUM,MAC层时延,SRcongestion数,SR拥塞比,`下行CQI<3的比例` FROM $table_all WHERE cell in $cellStr AND day_id = '$date' AND hour_id='$hour' group by cell;";
        $res = $pdo->query($sql, PDO::FETCH_ASSOC);
        if ($res) {
            $row = $res->fetchall();
            foreach ($row as $key => $value) {
                $cell = $value['cell'];
                $num = $value['NUM'];
                $v1 = $value['MAC层时延'];
                $v2 = $value['SRcongestion数'];
                $v3 = $value['SR拥塞比'];
                $cqi= $value['下行CQI<3的比例'];
                $pdo->query("UPDATE $table SET `最高RRC用户数`=$num, `MAC层时延`=$v1, `SRcongestion数`=$v2, `SR拥塞比`=$v3,`下行CQI<3的比例`=$cqi WHERE cell='$cell'");
                if ($num > 100) {
                    $pdo->query("UPDATE $table SET `Polar-最高RRC用户数`=100 WHERE cell='$cell'");
                } else {
                    $point = intval($num);
                    $pdo->query("UPDATE $table SET `Polar-最高RRC用户数`=$point WHERE cell='$cell'");
                }
            }
        }
    }

    function insertData_highTraffic_badhandover($pdo, $erbs, $erbsStr, $cellStr, $date, $hour, $txt, $table, $table_all) {
        $pdo->query("UPDATE $table SET `关联度`=0 WHERE `关联度` IS NULL");
        $pdo->query("UPDATE $table SET `下行CQI<3的比例`=0 WHERE `下行CQI<3的比例` IS NULL");
        $pdo->query("UPDATE $table SET `Polar-高话务`=0 WHERE `Polar-高话务` IS NULL");
        $sql = "SELECT cell,SUM(`最大RRC连接用户数`)/20 AS NUM ,`下行CQI<3的比例` FROM $table_all WHERE cell in $cellStr AND day_id = '$date' AND hour_id='$hour' group by cell;";
        $res = $pdo->query($sql, PDO::FETCH_ASSOC);
        if ($res) {
            $row = $res->fetchall();
            foreach ($row as $key => $value) {
                $cell = $value['cell'];
                $num = $value['NUM'];
                $cqi = $value['下行CQI<3的比例'];
                $pdo->query("UPDATE $table SET `关联度`=$num,`下行CQI<3的比例`=$cqi WHERE cell='$cell'");
                if ($num > 100) {
                    $pdo->query("UPDATE $table SET `Polar-高话务`=100 WHERE cell='$cell'");
                } else {
                    $point = intval($num);
                    $pdo->query("UPDATE $table SET `Polar-高话务`=$point WHERE cell='$cell'");
                }
            }
        }
    }
