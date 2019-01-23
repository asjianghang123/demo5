<?php
    function insert_noOfPucchSrUsers($pdo, $host, $username, $password, $erbs, $erbsStr, $cellStr, $date, $hour, $txt, $tables, $table_all) {
        $pdo->query("UPDATE $tables SET `srUser`=0 WHERE `srUser` IS NULL");
        //判断是否用今天的数据
        $dbname = "kget".date("ymd");
        $sql = "SELECT COUNT(*) AS num FROM mongs.task WHERE taskName='$dbname';";
        $row = $pdo->query($sql, PDO::FETCH_ASSOC)->fetchall();
        if ($row[0]['num'] == 0) {
            $dbname = "kget".date("ymd", strtotime("-1 day"));
        }

        $db = '';
        try {
            $db = new PDO("mysql:host=$host;dbname=$dbname", "$username", "$password");
        } catch (Exception $e) {
            return;
        }

        $srUserFlag = 0;
        $sql = "SELECT EUtranCellTDDId,noOfPucchSrUsers FROM EUtranCellTDD WHERE EUtranCellTDDId in $cellStr GROUP by EUtranCellTDDId;";
        $res = $db->query($sql);
        if ($res) {
            $row = $res->fetchAll(PDO::FETCH_ASSOC);
            foreach ($row as $key => $value) {
                $num = $value['noOfPucchSrUsers'];
                $file = fopen($txt, "r");
                fgets($file);
                while (!feof($file)) {
                    $arr = explode("=", trim(fgets($file)));
                    $arrMeContext = $arr[1];
                    if ($arrMeContext == $value['EUtranCellTDDId']) {
                        $cell = $arr[1];
                        // if($num < 400) {
                            $srUserFlag = $num;
                            $pdo->query("UPDATE $tables SET `srUser`=$num WHERE `cell`='$cell';"); 
                        // }
                    }  
                }
            }
        }

        $sql = "SELECT EUtranCellFDDId,noOfPucchSrUsers FROM EUtranCellFDD WHERE EUtranCellFDDId in $cellStr GROUP by EUtranCellFDDId;";
        $res = $db->query($sql);
        if ($res) {
            $row = $res->fetchAll(PDO::FETCH_ASSOC);
            foreach ($row as $key => $value) {
                $num = $value['noOfPucchSrUsers'];
                $file = fopen($txt, "r");
                fgets($file);
                while (!feof($file)) {
                    $arr = explode("=", trim(fgets($file)));
                    $arrMeContext = $arr[1];
                    if ($arrMeContext == $value['EUtranCellFDDId']) {
                        $cell = $arr[1];
                        if ($srUserFlag < $num) {
                            $pdo->query("UPDATE $tables SET `srUser`=$num WHERE `cell`='$cell';"); 
                        }
                    }  
                }
            }
        }

    }