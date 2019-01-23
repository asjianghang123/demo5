<?php
    function insertData_AvgPRB($pdo, $erbs, $erbsStr, $cellStr, $date, $hour, $txt, $table, $table_all) {
        $pdo->query("UPDATE $table SET `平均PRB`=0 WHERE `平均PRB` IS NULL");
        $pdo->query("UPDATE $table SET `Polar-干扰`=0 WHERE `Polar-干扰` IS NULL");
        $sql = "SELECT cell,PRB1上行干扰电平,PRB2上行干扰电平,PRB3上行干扰电平,PRB4上行干扰电平,PRB5上行干扰电平,PRB6上行干扰电平,PRB7上行干扰电平,PRB8上行干扰电平,PRB9上行干扰电平,PRB10上行干扰电平,PRB11上行干扰电平,PRB12上行干扰电平,PRB13上行干扰电平,PRB14上行干扰电平,PRB15上行干扰电平,PRB16上行干扰电平,PRB17上行干扰电平,PRB18上行干扰电平,PRB19上行干扰电平,PRB20上行干扰电平,PRB21上行干扰电平,PRB22上行干扰电平,PRB23上行干扰电平,PRB24上行干扰电平,PRB25上行干扰电平,PRB26上行干扰电平,PRB27上行干扰电平,PRB28上行干扰电平,PRB29上行干扰电平,PRB30上行干扰电平,PRB31上行干扰电平,PRB32上行干扰电平,PRB33上行干扰电平,PRB34上行干扰电平,PRB35上行干扰电平,PRB36上行干扰电平,PRB37上行干扰电平,PRB38上行干扰电平,PRB39上行干扰电平,PRB40上行干扰电平,PRB41上行干扰电平,PRB42上行干扰电平,PRB43上行干扰电平,PRB44上行干扰电平,PRB45上行干扰电平,PRB46上行干扰电平,PRB47上行干扰电平,PRB48上行干扰电平,PRB49上行干扰电平,PRB50上行干扰电平,PRB51上行干扰电平,PRB52上行干扰电平,PRB53上行干扰电平,PRB54上行干扰电平,PRB55上行干扰电平,PRB56上行干扰电平,PRB57上行干扰电平,PRB58上行干扰电平,PRB59上行干扰电平,PRB60上行干扰电平,PRB61上行干扰电平,PRB62上行干扰电平,PRB63上行干扰电平,PRB64上行干扰电平,PRB65上行干扰电平,PRB66上行干扰电平,PRB67上行干扰电平,PRB68上行干扰电平,PRB69上行干扰电平,PRB70上行干扰电平,PRB71上行干扰电平,PRB72上行干扰电平,PRB73上行干扰电平,PRB74上行干扰电平,PRB75上行干扰电平,PRB76上行干扰电平,PRB77上行干扰电平,PRB78上行干扰电平,PRB79上行干扰电平,PRB80上行干扰电平,PRB81上行干扰电平,PRB82上行干扰电平,PRB83上行干扰电平,PRB84上行干扰电平,PRB85上行干扰电平,PRB86上行干扰电平,PRB87上行干扰电平,PRB88上行干扰电平,PRB89上行干扰电平,PRB90上行干扰电平,PRB91上行干扰电平,PRB92上行干扰电平,PRB93上行干扰电平,PRB94上行干扰电平,PRB95上行干扰电平,PRB96上行干扰电平,PRB97上行干扰电平,PRB98上行干扰电平,PRB99上行干扰电平,PRB100上行干扰电平 FROM interfereCell WHERE cell in $cellStr AND day_id >= '$date' AND hour_id = '$hour' GROUP BY cell;";
        // echo $sql;
        $res = $pdo->query($sql, PDO::FETCH_NUM);
        if ($res) {
            $row = $res->fetchall();
            foreach ($row as $key => $value) {
                $cell = $value[0];
                $j = 0;
                $avg = 0;
                for ($i=1; $i < count($value); $i++) { 
                    if ($value[$i] == null) {
                        continue;
                    }
                    $avg = $avg + $value[$i];
                    $j++;
                }
                if ($j == 0) {
                    $pdo->query("UPDATE $table SET `平均PRB`=0 WHERE cell='$cell'");
                    $pdo->query("UPDATE $table SET `Polar-干扰`=0 WHERE cell='$cell'");
                } else {
                    $avg = round($avg / $j, 2);
                    $pdo->query("UPDATE $table SET `平均PRB`=$avg WHERE cell='$cell'");
                    $polarValue = 0;
                    if ($avg >= -102) {
                        $polarValue = 100;
                    } elseif ($avg <= -112) {
                        $polarValue = 0;
                    } else {
                        $point = ($avg + 112)*10;
                        if ($point > 100) {
                            $polarValue = 100;
                        } else {
                            $polarValue = $point;
                        }
                    }
                    $pdo->query("UPDATE $table SET `Polar-干扰`=$polarValue WHERE cell='$cell'");
                }
            }
        }
    }

    //差小区诊断报告中细化高干扰算法第一点
    function insertData_AvgPRB_Refine($pdo, $erbs, $erbsStr, $cellStr, $date, $hour, $txt, $table, $table_all) {
        $sql = "SELECT cell,估算PUSCH_SINR,估算PUCCH_SINR,`MAC层上行重传率(%)` FROM $table_all WHERE cell in $cellStr AND day_id >= '$date' AND hour_id = '12' GROUP BY cell;";
        $res = $pdo->query($sql, PDO::FETCH_ASSOC);
        if ($res) {
            $row = $res->fetchall();
            foreach ($row as $key => $value) {
                $cell = $value['cell'];
                $puschSinr = $value['估算PUSCH_SINR'];
                $pucchSinr = $value['估算PUCCH_SINR'];
                $mac = $value['MAC层上行重传率(%)'];
                if ($puschSinr >= 3 && $pucchSinr >= 3 && $mac < 10) {
                    $sql = "SELECT `Polar-干扰` FROM $table WHERE cell='$cell';";
                    $row = $pdo->query($sql)->fetchall(PDO::FETCH_ASSOC);
                    $flag = $row[0]['Polar-干扰'];
                    if ($flag > 50) {
                        $pdo->query("UPDATE $table SET `Polar-干扰`=50 WHERE cell='$cell';");
                    }
                }
            }
        }
    }

    //差小区诊断报告中细化高干扰算法第二点
    function insertData_AvgPRB_Refine_2($pdo, $erbs, $erbsStr, $cellStr, $date, $hour, $txt, $table, $table_all) {
        $date_from = date("Y-m-d", strtotime("-2 day"));
        $sql = "SELECT a.cell, IF(tnum IS NULL, 0, tnum)/anum radio FROM (SELECT cell,COUNT(*) tnum FROM interfereCell_avg WHERE day_id='$date' AND cell IN $cellStr GROUP BY cell)t RIGHT JOIN (SELECT cell,COUNT(*) anum FROM interfereCell WHERE day_id>='$date_from' AND cell IN $cellStr GROUP BY cell)a ON t.cell=a.cell;";
        $res = $pdo->query($sql, PDO::FETCH_ASSOC);
        if ($res) {
            $row = $res->fetchall();
            foreach ($row as $key => $value) {
                $cell = $value['cell'];
                $radio = $value['radio'];
                if ($radio > 0.75) {
                    $sql = "SELECT 平均PRB FROM $table WHERE cell='$cell';";
                    $row = $pdo->query($sql)->fetchall(PDO::FETCH_ASSOC);
                    $data = $row[0]['平均PRB'];
                    $data = $data . '--突发干扰：' . $radio;
                    $pdo->query("UPDATE $table SET `平均PRB`='$data' WHERE cell='$cell';");
                }
            }
        }
    }

    //差小区诊断报告中细化高干扰算法第三点
    function insertData_AvgPRB_Refine_3($pdo, $erbs, $erbsStr, $cellStr, $date, $hour, $txt, $table, $table_all, $cellArr) {

        //判断是否用今天的数据
        $dbname = "kget".date("ymd");
        $sql = "SELECT COUNT(*) AS num FROM mongs.task WHERE taskName='$dbname';";
        $row = $pdo->query($sql, PDO::FETCH_ASSOC)->fetchall();
        if ($row[0]['num'] == 0) {
            $dbname = "kget".date("ymd", strtotime("-1 day"));
        }
        $pdo->query("use $dbname");

        foreach ($cellArr as $cell) {
            $sql = "SELECT 平均PRB FROM AutoKPI.$table WHERE cell='$cell';";
            $row = $pdo->query($sql)->fetchall(PDO::FETCH_ASSOC);
            $data = $row[0]['平均PRB'];
            $nowPRB = explode('--', $data)[0];

            $sixCellArr = is_exist_from_file($pdo, $cell); 
            if (count($sixCellArr) < 6) {
                continue;
            }

            if ($nowPRB <= -110) {
                continue;
            }

            $sixCellArr = array_slice($sixCellArr, 0, 6);
            $sixCellStr = "('".implode("','", $sixCellArr)."')";
            $sql = "SELECT cell,PRB1上行干扰电平,PRB2上行干扰电平,PRB3上行干扰电平,PRB4上行干扰电平,PRB5上行干扰电平,PRB6上行干扰电平,PRB7上行干扰电平,PRB8上行干扰电平,PRB9上行干扰电平,PRB10上行干扰电平,PRB11上行干扰电平,PRB12上行干扰电平,PRB13上行干扰电平,PRB14上行干扰电平,PRB15上行干扰电平,PRB16上行干扰电平,PRB17上行干扰电平,PRB18上行干扰电平,PRB19上行干扰电平,PRB20上行干扰电平,PRB21上行干扰电平,PRB22上行干扰电平,PRB23上行干扰电平,PRB24上行干扰电平,PRB25上行干扰电平,PRB26上行干扰电平,PRB27上行干扰电平,PRB28上行干扰电平,PRB29上行干扰电平,PRB30上行干扰电平,PRB31上行干扰电平,PRB32上行干扰电平,PRB33上行干扰电平,PRB34上行干扰电平,PRB35上行干扰电平,PRB36上行干扰电平,PRB37上行干扰电平,PRB38上行干扰电平,PRB39上行干扰电平,PRB40上行干扰电平,PRB41上行干扰电平,PRB42上行干扰电平,PRB43上行干扰电平,PRB44上行干扰电平,PRB45上行干扰电平,PRB46上行干扰电平,PRB47上行干扰电平,PRB48上行干扰电平,PRB49上行干扰电平,PRB50上行干扰电平,PRB51上行干扰电平,PRB52上行干扰电平,PRB53上行干扰电平,PRB54上行干扰电平,PRB55上行干扰电平,PRB56上行干扰电平,PRB57上行干扰电平,PRB58上行干扰电平,PRB59上行干扰电平,PRB60上行干扰电平,PRB61上行干扰电平,PRB62上行干扰电平,PRB63上行干扰电平,PRB64上行干扰电平,PRB65上行干扰电平,PRB66上行干扰电平,PRB67上行干扰电平,PRB68上行干扰电平,PRB69上行干扰电平,PRB70上行干扰电平,PRB71上行干扰电平,PRB72上行干扰电平,PRB73上行干扰电平,PRB74上行干扰电平,PRB75上行干扰电平,PRB76上行干扰电平,PRB77上行干扰电平,PRB78上行干扰电平,PRB79上行干扰电平,PRB80上行干扰电平,PRB81上行干扰电平,PRB82上行干扰电平,PRB83上行干扰电平,PRB84上行干扰电平,PRB85上行干扰电平,PRB86上行干扰电平,PRB87上行干扰电平,PRB88上行干扰电平,PRB89上行干扰电平,PRB90上行干扰电平,PRB91上行干扰电平,PRB92上行干扰电平,PRB93上行干扰电平,PRB94上行干扰电平,PRB95上行干扰电平,PRB96上行干扰电平,PRB97上行干扰电平,PRB98上行干扰电平,PRB99上行干扰电平,PRB100上行干扰电平 FROM AutoKPI.interfereCell WHERE cell in $sixCellStr AND day_id >= '$date' AND hour_id = '$hour' GROUP BY cell;";
            // echo $sql;
            $res = $pdo->query($sql, PDO::FETCH_NUM);
            $k = 0;
            if ($res) {
                $row = $res->fetchall();
                $allAvg = 0;
                foreach ($row as $key => $value) {
                    $six_cell = $value[0];
                    $j = 0;
                    $avg = 0;
                    for ($i=1; $i < count($value); $i++) { 
                        if ($value[$i] == null || $value[$i] == 0) {
                            continue;
                        }
                        $avg = $avg + $value[$i];
                        $j++;
                    }
                    if ($j == 0) {
                        continue;
                    } else {
                        $avg = round($avg / $j, 2);
                        $k++;
                    }
                    $allAvg = $allAvg + $avg;
                }
                $avgAvg = round($allAvg / $k, 2);
                if (abs($nowPRB - $avgAvg) > 10) {
                    $data = $data . '--局部干扰';
                } elseif (abs($nowPRB - $avgAvg) < 5) {
                    $data = $data . '--片区干扰';
                }
                $pdo->query("UPDATE AutoKPI.$table SET `平均PRB`='$data' WHERE cell='$cell'");
            }
        }
    }

    //差小区诊断报告中细化高干扰算法第四点/第五点
    function insertData_AvgPRB_Refine_4($pdo, $erbs, $erbsStr, $cellStr, $date, $hour, $txt, $table, $table_all, $cellArr) {
        foreach ($cellArr as $cell) {
            $sql = "SELECT 平均PRB FROM AutoKPI.$table WHERE cell='$cell';";
            $row = $pdo->query($sql)->fetchall(PDO::FETCH_ASSOC);
            $data = $row[0]['平均PRB'];
            $sixCellArr = is_exist_from_file($pdo, $cell); 
            if (count($sixCellArr) < 6) {
                continue;
            }

            $twelveCellStr = "('".implode("','", $sixCellArr)."')";
            $sixCellArr = array_slice($sixCellArr, 0, 6);
            $sixCellStr = "('".implode("','", $sixCellArr)."')";
            $sql = "SELECT FORMAT(ABS(mAVG-AVG),2) AS num FROM(SELECT ((`SF1上行干扰电平`+`SF2上行干扰电平`)/2-(`SF6上行干扰电平`+`SF7上行干扰电平`)/2) AS mAVG FROM interfereCell WHERE cell IN ('$cell') AND day_id = '$date' AND hour_id = 10)m,(SELECT AVG(((`SF1上行干扰电平`+`SF2上行干扰电平`)/2-(`SF6上行干扰电平`+`SF7上行干扰电平`)/2)) AS AVG FROM interfereCell WHERE cell IN $sixCellStr AND day_id = '$date' AND hour_id =10)s;";
            $res = $pdo->query($sql, PDO::FETCH_ASSOC);
            if ($res) {
                $row = $res->fetch();
                $num = $row['num'];
                if ($num > 10) {
                    $data = $data . "--时隙跑偏";
                } else {
                    $sql = "SELECT FORMAT(ABS(mAVG-AVG),2) AS num FROM(SELECT ((`SF1上行干扰电平`+`SF2上行干扰电平`)/2-(`SF6上行干扰电平`+`SF7上行干扰电平`)/2) AS mAVG FROM interfereCell WHERE cell IN ('$cell') AND day_id = '$date' AND hour_id = $hour)m,(SELECT AVG(((`SF1上行干扰电平`+`SF2上行干扰电平`)/2-(`SF6上行干扰电平`+`SF7上行干扰电平`)/2)) AS AVG FROM interfereCell WHERE cell IN $twelveCellStr AND day_id = '$date' AND hour_id = $hour)s;";
                    $res = $pdo->query($sql, PDO::FETCH_ASSOC);
                    if ($res) {
                        $row = $res->fetch();
                        $num = $row['num'];
                        if ($num > 6) {
                            $data = $data . "--时隙跑偏";
                        }
                    }
                }
                $pdo->query("UPDATE AutoKPI.$table SET `平均PRB`='$data' WHERE cell='$cell'");
            }
        }
    }

    /**
      * http://www.cnblogs.com/John727/p/4514503.html 
      * 读取文件查找符合条件的小区，没有查询到则去数据库中中匹配
      *
      * @param 
      *            pdo 连接数据库 默认kget库 
      * @param
      *            cell 需要检测的小区
      * @param
      *            num 查询距离最近12个小区
      *
      * @return array 距离最近的12和小区
      *
      */
    function is_exist_from_file($pdo, $cell) {
        $res = [];
        //读文件
        /*$txt = "txt/distince.txt";
        $file = fopen($txt, "r");
        fgets($file);
        $flag = 0;
        while(!feof($file)) { 
            $fileArr = explode("=", trim(fgets($file)));
            $compareCell = $fileArr[0];
            if($compareCell == $cell) {
                $flag = 1;
                $res = explode(',', $fileArr[1]);
                return $res;
            }
        }
        fclose($file);
        if($flag == 0) {*/
            //写文件
            // $file = fopen($txt, "a");
            $arr = getCells($pdo, $cell);
            // print_r($arr);
            foreach ($arr as $keys => $values) {
                // $name = "\r\n" . $keys. '=';
                foreach ($values as $key => $value) {
                    // $name = $name . $key .',';
                    array_push($res, $key);
                }
                // $name = substr($name,0,strlen($name)-1); 
                // fwrite($file, $name);
            }
        // } 
        // fclose($file);
        return $res;
    }
    function is_exist_from_file_bak($pdo, $cell) {
        $res = [];
        //读文件
        $txt = "txt/distince.txt";
        $file = fopen($txt, "r");
        fgets($file);
        $flag = 0;
        while (!feof($file)) { 
            $fileArr = explode("=", trim(fgets($file)));
            $compareCell = $fileArr[0];
            if ($compareCell == $cell) {
                $flag = 1;
                $res = explode(',', $fileArr[1]);
                return $res;
            }
        }
        fclose($file);
        if ($flag == 0) {
            //写文件
            $file = fopen($txt, "a");
            $arr = getCells($pdo, $cell);
            foreach ($arr as $keys => $values) {
                $name = "\r\n" . $keys. '=';
                foreach ($values as $key => $value) {
                    $name = $name . $key .',';
                    array_push($res, $key);
                }
                $name = substr($name, 0, strlen($name)-1); 
                fwrite($file, $name);
            }
        } 
        fclose($file);
        return $res;
    }

    //遍历符合条件小区。筛选经纬度
    // function getCells($pdo, $cellArr) {
    function getCells($pdo, $cell) {
        $items = [];  //存储所有符合条件的小区
        $arr = [];
        // foreach ($cellArr as $key => $cell) {
            //获取当前小区经纬度
            $sql = "SELECT latitudeBD,longitudeBD FROM mongs.siteLte WHERE cellName IN ('$cell');";
            $row = $pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
            $mCell_latitude = $row['latitudeBD'];
            $mCell_longitude = $row['longitudeBD'];
            $mCell = $cell;
            if ($mCell_longitude == '' || $mCell_latitude == '') {
                //主小区没有匹配到经纬度。
                continue;
            }

            //筛选(10公里范围内)前12个符合条件的小区
            $square_distance = 100;
            $i = 0;
            $items = [];
            // while(count($items) < 12) {
                $items = [];
                $index = getCellAndDistince($pdo, $cell, $mCell_latitude, $mCell_longitude, $mCell, $square_distance);
                if (array_key_exists($mCell, $index)) { 
                    foreach ($index[$mCell] as $key => $value) {
                        $items[$key] =  $value;  
                    }
                } 
                 
                if (count($items) >= 12) {
                    asort($items);
                    $items = array_slice($items, 0, 12, true);
                    // break;
                }

                if (count($items) < 12) {
                    asort($items);
                    // $items = array_slice($items, 0, 12, true);
                    // break;
                }

                // $square_distance++;

                // if($square_distance >50) {
                //     break;
                // }
            // }
            $arr[$mCell] = $items;        
        // }
        return $arr;
    }

    function getCellAndDistince($pdo, $cell, $mCell_latitude, $mCell_longitude, $mCell, $square_distance) {
        $items = [];
        $square = returnSquarePoint($mCell_longitude, $mCell_latitude, $square_distance, 6378.138);
        // $sql = "SELECT cellName,siteName,latitudeBD,longitudeBD FROM mongs.siteLte WHERE siteName IN (SELECT meContext FROM EUtranCellTDD WHERE earfcn IN (SELECT earfcn FROM EUtranCellTDD WHERE EUtranCellTDDId IN('$cell')) AND meContext != (SELECT meContext FROM EUtranCellTDD WHERE EUtranCellTDDId IN ('$cell')) GROUP BY meContext) GROUP BY siteName;";
        $sql = "SELECT cellName,siteName,latitudeBD,longitudeBD FROM mongs.siteLte WHERE ecgi IN (SELECT EUtranCellRelationId FROM EUtranCellRelation WHERE EUtranCellTDD='$cell') AND siteName NOT IN (SELECT meContext FROM EUtranCellRelation WHERE EUtranCellTDD = '$cell');";
        // echo $sql;
        $res = $pdo->query($sql);
        //邻小区参数
        $vCell_latitude = [];
        $vCell_longitude = [];
        $vCell = [];
        if ($res) {
            while ($rows = $res->fetch(PDO::FETCH_ASSOC)) {
                $latitude = $rows['latitudeBD'];
                $longitude = $rows['longitudeBD'];
                $cell = $rows['cellName'];
                $site = $rows['siteName'];
                if ($longitude >= $square['left-top']['lng'] && $longitude <= $square['right-bottom']['lng'] && $latitude >= $square['left-bottom']['lat'] && $latitude <= $square['right-top']['lat']) {
                    $distance = ROUND(6378.138*2*asin(sqrt(pow(sin( ($mCell_latitude*pi()/180-$latitude*pi()/180)/2), 2)+cos($mCell_latitude*pi()/180)*cos($latitude*pi()/180)* pow(sin( ($mCell_longitude*pi()/180-$longitude*pi()/180)/2), 2))), 3);
                    if ($distance <= $square_distance) {
                        $items[$mCell][$cell] = $distance;
                    }
                }
            }
        }
        return $items;
    }

    /**
      * 计算某个经纬度的周围某段距离的正方形的四个点
      *
      * @param
      *            radius 地球半径 平均6371km
      * @param
      *            lng float 经度
      * @param
      *            lat float 纬度
      * @param
      *            distance float 该点所在圆的半径，该圆与此正方形内切，默认值为1千米
      * @return array 正方形的四个点的经纬度坐标 
      *
      */
    function returnSquarePoint($lng, $lat, $distance = 1, $radius = 6371)
    {
        $dlng = 2 * asin(sin($distance / (2 * $radius)) / cos(deg2rad($lat)));
        $dlng = rad2deg($dlng);       
        $dlat = $distance / $radius;
        $dlat = rad2deg($dlat);
        return array(
                    'left-top' => array(
                        'lat' => $lat + $dlat,
                        'lng' => $lng - $dlng
                    ),
                    'right-top' => array(
                        'lat' => $lat + $dlat,
                        'lng' => $lng + $dlng
                    ),
                    'left-bottom' => array(
                        'lat' => $lat - $dlat,
                        'lng' => $lng - $dlng
                    ),
                    'right-bottom' => array(
                        'lat' => $lat - $dlat,
                        'lng' => $lng + $dlng
                    )
                );
     }