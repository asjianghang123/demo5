<?php
    function insertData_alarmNum($pdo, $erbs, $erbsStr, $cellStr, $date, $txt, $table) {
        $pdo->query("UPDATE $table SET `告警数量`=0 WHERE `告警数量` IS NULL"); 

        $sql = "SELECT meContext,COUNT(*) AS num FROM Alarm.FMA_alarm_list WHERE DATE_FORMAT(Event_time, '%Y-%m-%d') >= '$date' AND meContext in $erbsStr GROUP BY meContext;";
        // print_r($sql);return;
        $res = $pdo->query($sql);
        if ($res) {
            $row = $res->fetchAll(PDO::FETCH_ASSOC);
            if (count($row) == 0) {
                return;
            } 
            foreach ($row as $key => $value) {
                $num = $value['num'];
                $file = fopen($txt, "r");
                fgets($file);
                while (!feof($file)) {
                    // if(fgets($file) == '') {
                    //     continue;
                    // }
                    $arr = explode("=", trim(fgets($file)));
                    $arrMeContext = $arr[2];
                    if ($arrMeContext == $value['meContext']) {
                        $cell = $arr[1];
                        $pdo->query("UPDATE $table SET `告警数量`=$num WHERE `cell`='$cell';"); 
                    }
                }
                fclose($file);
            }
        }
    }

    function insertData_polar_alarm($pdo, $erbs, $erbsStr, $cellStr, $date, $txt, $table) {
        $pdo->query("UPDATE $table SET `Polar-告警`=0 WHERE `Polar-告警` IS NULL");

        $sql = "SELECT meContext,max(num) as num FROM( SELECT meContext,SP_text,t.access AS num,t.alarmNameE FROM Alarm.FMA_alarm_list r, mongs.AlarmInfo t WHERE r.SP_text= t.alarmNameE AND DATE_FORMAT(Event_time, '%Y-%m-%d') >= '" .$date. "' AND meContext in $erbsStr GROUP BY meContext,access ORDER BY access DESC)t GROUP BY meContext;";
        $res = $pdo->query($sql);
        if ($res) {
            $row = $res->fetchAll(PDO::FETCH_ASSOC);
            if (count($row) == 0) {
                return;
            } 
            foreach ($row as $key => $value) {
                $num = $value['num'];
                $file = fopen($txt, "r");
                fgets($file);
                $i = 0;
                while (!feof($file)) {
                    // if(fgets($file) == '') {
                    //     continue;
                    // }
                    $i++;
                    $arr = explode("=", trim(fgets($file)));
                    
                    $arrMeContext = $arr[2];
                    if ($arrMeContext == $value['meContext']) {
                        $cell = $arr[1];
                        $pdo->query("UPDATE $table SET `Polar-告警`=$num WHERE `cell`='$cell';"); 
                    }
                }
                fclose($file);
            }
        }
    }