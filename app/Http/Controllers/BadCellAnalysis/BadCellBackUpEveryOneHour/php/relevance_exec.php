<?php
    function insertData_Relevance_exec($pdo, $erbs, $erbsStr, $cellStr, $rowArr, $date, $hour, $txt, $tables, $table_all) {
        $pdo->query("UPDATE $tables SET `切换成功率_执行切换成功率`=0 WHERE `切换成功率_执行切换成功率` IS NULL");
        $startTime = date('Y-m-d', strtotime("-1 day")); 
        $endTime = date('Y-m-d');
        $value_x = [];
        $value_y = [];
        $sql = "SELECT day_id, hour_id, cell, 切换成功率, 执行切换成功率 FROM badHandoverCell WHERE day_id >= '" . $startTime . "' AND day_id <= '" . $endTime . "' AND cell in $cellStr GROUP BY cell,day_id, hour_id;";
        $res = $pdo->query($sql);
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
               $cellStr = $row['cell'];
            $value_x[$cellStr][] = $row['切换成功率'];
            $value_y[$cellStr][] = $row['执行切换成功率'];
        }
        $data= @getRelevanceData($value_x, $value_y, $rowArr);
          foreach ($data as $key => $value) {
            $pdo->query("UPDATE $tables SET `切换成功率_执行切换成功率`='$value' WHERE cell='$key'");
        }
    }