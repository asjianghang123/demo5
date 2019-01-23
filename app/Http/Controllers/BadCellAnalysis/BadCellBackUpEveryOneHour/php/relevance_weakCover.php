<?php
    function insertData_Relevance_weakCover($pdo, $erbs, $erbsStr, $cellStr, $rowArr, $date, $hour, $txt, $tables, $table_all, $column) {
        $columns = $column."_弱覆盖";
        $pdo->query("UPDATE $tables SET $columns=0 WHERE $columns IS NULL");
        $startTime = date('Y-m-d', strtotime("-1 day")); 
        $endTime = date('Y-m-d');
        $value_x = [];
        $value_y = [];
        $sql = "SELECT day_id, hour_id, cell, $column, `RSRP<-116的比例` AS num FROM $table_all WHERE day_id >= '" . $startTime . "' AND day_id <= '" . $endTime . "' AND cell in $cellStr GROUP BY cell,day_id, hour_id;";
        $res = $pdo->query($sql);
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $cell = $row['cell'];
            $value_x[$cell][] = $row[$column];
            $value_y[$cell][] = $row['num'];
        }
        $data = @getRelevanceData($value_x, $value_y, $rowArr);
        foreach ($data as $key => $value) {
            $pdo->query("UPDATE $tables SET $columns='$value' WHERE cell='$key'");
        }
    }