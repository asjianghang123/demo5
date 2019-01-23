<?php
    function insertData_Relevance_wirelessCallRate_interfere($pdo, $erbs, $erbsStr, $cellStr, $rowArr, $date, $hour, $txt, $tables, $table_all, $column) {
        $columns = $column.'_干扰';
        $pdo->query("UPDATE $tables SET $columns=0 WHERE $columns IS NULL");
        $startTime = date('Y-m-d', strtotime("-1 day")); 
        $endTime = date('Y-m-d');
        $value_x = [];
        $value_y = [];
        $sql = "SELECT cell,day_id,hour_id,PRB1上行干扰电平,PRB2上行干扰电平,PRB3上行干扰电平,PRB4上行干扰电平,PRB5上行干扰电平,PRB6上行干扰电平,PRB7上行干扰电平,PRB8上行干扰电平,PRB9上行干扰电平,PRB10上行干扰电平,PRB11上行干扰电平,PRB12上行干扰电平,PRB13上行干扰电平,PRB14上行干扰电平,PRB15上行干扰电平,PRB16上行干扰电平,PRB17上行干扰电平,PRB18上行干扰电平,PRB19上行干扰电平,PRB20上行干扰电平,PRB21上行干扰电平,PRB22上行干扰电平,PRB23上行干扰电平,PRB24上行干扰电平,PRB25上行干扰电平,PRB26上行干扰电平,PRB27上行干扰电平,PRB28上行干扰电平,PRB29上行干扰电平,PRB30上行干扰电平,PRB31上行干扰电平,PRB32上行干扰电平,PRB33上行干扰电平,PRB34上行干扰电平,PRB35上行干扰电平,PRB36上行干扰电平,PRB37上行干扰电平,PRB38上行干扰电平,PRB39上行干扰电平,PRB40上行干扰电平,PRB41上行干扰电平,PRB42上行干扰电平,PRB43上行干扰电平,PRB44上行干扰电平,PRB45上行干扰电平,PRB46上行干扰电平,PRB47上行干扰电平,PRB48上行干扰电平,PRB49上行干扰电平,PRB50上行干扰电平,PRB51上行干扰电平,PRB52上行干扰电平,PRB53上行干扰电平,PRB54上行干扰电平,PRB55上行干扰电平,PRB56上行干扰电平,PRB57上行干扰电平,PRB58上行干扰电平,PRB59上行干扰电平,PRB60上行干扰电平,PRB61上行干扰电平,PRB62上行干扰电平,PRB63上行干扰电平,PRB64上行干扰电平,PRB65上行干扰电平,PRB66上行干扰电平,PRB67上行干扰电平,PRB68上行干扰电平,PRB69上行干扰电平,PRB70上行干扰电平,PRB71上行干扰电平,PRB72上行干扰电平,PRB73上行干扰电平,PRB74上行干扰电平,PRB75上行干扰电平,PRB76上行干扰电平,PRB77上行干扰电平,PRB78上行干扰电平,PRB79上行干扰电平,PRB80上行干扰电平,PRB81上行干扰电平,PRB82上行干扰电平,PRB83上行干扰电平,PRB84上行干扰电平,PRB85上行干扰电平,PRB86上行干扰电平,PRB87上行干扰电平,PRB88上行干扰电平,PRB89上行干扰电平,PRB90上行干扰电平,PRB91上行干扰电平,PRB92上行干扰电平,PRB93上行干扰电平,PRB94上行干扰电平,PRB95上行干扰电平,PRB96上行干扰电平,PRB97上行干扰电平,PRB98上行干扰电平,PRB99上行干扰电平,PRB100上行干扰电平 FROM interfereCell WHERE cell in $cellStr AND day_id >= '" . $startTime . "' AND day_id <= '" . $endTime . "' GROUP BY day_id,hour_id,cell ORDER BY cell,day_id,hour_id;";
        $res = $pdo->query($sql);
        if ($res) {
            while ($row = $res->fetch(PDO::FETCH_NUM)) {
                $cells = $row[0];
                $avg = 0;
                $j = 0;
                for ($i=0; $i<count($row); $i++) {
                    if ($i>2) {
                        if ($row[$i] == null) {
                            continue;
                        }
                        $avg = $avg + $row[$i];
                        $j++;
                    }
                }
                if ($j == 0) {
                    $value_y[$cells][] = 0;
                } else {
                    $avg = $avg / $j;
                    $value_y[$cells][] = $avg;
                }
            }
        }
        $sql = "SELECT day_id, hour_id, cell, $column FROM $table_all WHERE cell in $cellStr AND day_id >= '" . $startTime . "' AND day_id <= '" . $endTime . "' GROUP BY cell, day_id, hour_id";
        $res = $pdo->query($sql);
        if ($res) {
            while ($row = $res->fetch(PDO::FETCH_NUM)) {
                $cells = $row[2];
                $value_x[$cells][] = $row[3];
            }   
        }
        $data= @getRelevanceData($value_x, $value_y, $rowArr);
        foreach ($data as $key => $value) {
            $pdo->query("UPDATE $tables SET $columns='$value' WHERE cell='$key'");
        }
    }


    function getRelevanceData($x, $y, $cell) {
        $numArr = [];
        foreach ($cell as $value) {
            if (!array_key_exists($value, $x)) {
                $numArr[$value] = 0;
            } elseif (!array_key_exists($value, $y)) {
                $numArr[$value] = 0;
            } elseif (count($x[$value]) != count($y[$value])) {
                $numArr[$value] = 0;
            } else {
                $xSquare = [];
                $ySquare = [];
                $xySquare = [];
                $xySum = 0;
                $xSum = 0;
                $ySum = 0;
                $xxSum = 0;
                $yySum = 0;
                $count = count($x[$value]);
                for ($i=0,$j=0; $i<count($x[$value]),$j<count($y[$value]); $i++,$j++) {
                    if (count($x[$value])==count($y[$value])) {
                        $xx = $x[$value][$i] * $x[$value][$i];
                        $yy = $y[$value][$j] * $y[$value][$j];
                        $xy = $x[$value][$i] * $y[$value][$j];
                        $xySum = $xySum + $xy;  //xy之和
                        $xSum = $xSum + $x[$value][$i];     //x之和
                        $ySum = $ySum + $y[$value][$j];     //y之和
                        $xxSum = $xxSum + $xx;  //xx之和
                        $yySum = $yySum + $yy;  //yy之和
                    } else {
                        $numArr[$value][] = 0;
                    }  
                }
                if ($xSum == 0 || $ySum == 0) {
                    $numArr[$value] = 0;
                } else {
                    $numArr[$value] = abs(round((($xySum*$count-$xSum*$ySum)/(sqrt($xxSum*$count-$xSum*$xSum)*sqrt($yySum*$count-$ySum*$ySum))), 2));
                }
            }

        }
        return $numArr;
        
    }