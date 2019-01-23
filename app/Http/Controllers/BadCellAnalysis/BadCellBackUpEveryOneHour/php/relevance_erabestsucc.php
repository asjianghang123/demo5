<?php
        function insertData_Relevance_erabestsucc($pdo, $erbs, $erbsStr, $cellStr, $rowArr, $date, $hour, $txt, $tables, $table_all) {
                $pdo->query("UPDATE $tables SET `无线接通率_ERAB建立成功率`=0 WHERE `无线接通率_ERAB建立成功率` IS NULL");
                $startTime = date('Y-m-d', strtotime("-1 day")); 
                $endTime = date('Y-m-d');
                $value_x = [];
                $value_y = [];
                $sql = "SELECT day_id, hour_id, cell, 无线接通率, ERAB建立成功率 AS num FROM lowAccessCell WHERE day_id >= '" . $startTime . "' AND day_id <= '" . $endTime . "' AND cell in $cellStr GROUP BY cell,day_id, hour_id;";
                $res = $pdo->query($sql);
                while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
                        $cellStr = $row['cell'];
                        $value_x[$cellStr][] = $row['无线接通率'];
                        $value_y[$cellStr][] = $row['num'];
                }
                $data = @getRelevanceData($value_x, $value_y, $rowArr);
                foreach ($data as $key => $value) {
                    $pdo->query("UPDATE $tables SET `无线接通率_ERAB建立成功率`='$value' WHERE cell='$key'");
                }
    }