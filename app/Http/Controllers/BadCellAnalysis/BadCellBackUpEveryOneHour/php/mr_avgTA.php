<?php
    function insertData_mr_avgTA($pdo, $mr_pdo, $city, $database, $erbs, $erbsStr, $cellStr, $table, $tab) {
        // print_r('expression');
        $pdo->query("UPDATE $table SET `avgTA`=0 WHERE `avgTA` IS NULL AND city='$city'");
        $date = date("Y-m-d");
        $sql = "SELECT cellName,ecgi FROM mongs.siteLte WHERE cellName IN $cellStr GROUP BY cellName;";
        $res = $pdo->query($sql, PDO::FETCH_ASSOC);
        if ($res) {
            $row = $res->fetchall();
            $ecgiArr = [];
            $cellArr = [];
            foreach ($row as $key => $value) {
                $ecgiArr[] = $value['ecgi'];
                $cellArr[$value['ecgi']] = $value['cellName'];
            }
            $ecgiStr = "('".implode("','", $ecgiArr)."')";
            $mr_pdo->query("use $database;");
            $sql = "SELECT max(hourId) AS hour FROM MRS WHERE timeStamp='$date';";
            $row = $mr_pdo->query($sql, PDO::FETCH_ASSOC)->fetchall();
            $hour = $row[0]['hour'];
            if ($hour == '') {
                $date = date("Y-m-d", strtotime("-1 day"));
                $sql = "SELECT max(hourId) AS hour FROM MRS WHERE timeStamp='$date';";
                $hour = $row[0]['hour'];
            }
            $sum_num=45;
            $name='TADV';
            $searchArr = array();
            for ($i = 0; $i < $sum_num; $i++) {
                if ($i < 10) {
                    $n = "0".$i;
                } else {
                    $n = $i;
                }
                array_push($searchArr, "sum(mr_".$name."_".$n.") as mr_".$name."_".$n);
            }
            $searchArr = implode(",", $searchArr);
            $sql = "SELECT ecgi,$searchArr from MRS where timeStamp='$date' AND hourId=$hour AND ecgi IN $ecgiStr group by ecgi;";
            $res = $mr_pdo->query($sql);
            if ($res) {
                $rows = $res->fetchAll(PDO::FETCH_NUM);
                foreach ($rows as $row) {
                    $ecgiStr = $row[0];
                    $cell = $cellArr[$ecgiStr];
                    $temp = array();
                    for ($i = 1; $i <= $sum_num; $i++) {
                        if ($i-1<10) {
                            $k="0".($i-1);
                        } else {
                            $k=($i-1);
                        }
                        $temp[$name.$k] = $row[$i];      
                    }
                    $ue_avg=getueAvg($temp);
                    if ($ue_avg == '') {
                        continue;
                    }
                    $pdo->query("UPDATE $table SET `avgTA`=$ue_avg WHERE cell='$cell'");
                }
            }
        }
    }

    /**
     * 求出UE平均距离
     */
    function getueAvg($data)
    {

        $sum=array_sum($data);
        if ($sum!=0) {
            $avg=0.5*4.89*(
            $data['TADV00']*8 +
            $data['TADV01']*24 +
            $data['TADV02']*40+
            $data['TADV03']*56+
            $data['TADV04']*72+
            $data['TADV05']*88+
            $data['TADV06']*104+
            $data['TADV07']*120+
            $data['TADV08']*136+
            $data['TADV09']*152+
            $data['TADV10']*168+
            $data['TADV11']*184+
            $data['TADV12']*208+
            $data['TADV13']*240+
            $data['TADV14']*272+
            $data['TADV15']*304+
            $data['TADV16']*336+
            $data['TADV17']*368+
            $data['TADV18']*400+
            $data['TADV19']*432+
            $data['TADV20']*464+
            $data['TADV21']*496+
            $data['TADV22']*528+
            $data['TADV23']*560+
            $data['TADV24']*592+
            $data['TADV25']*624+
            $data['TADV26']*656+
            $data['TADV27']*688+
            $data['TADV28']*720+
            $data['TADV29']*752+
            $data['TADV30']*784+
            $data['TADV31']*816+
            $data['TADV32']*848+
            $data['TADV33']*880+
            $data['TADV34']*912+
            $data['TADV35']*944+
            $data['TADV36']*976+
            $data['TADV37']*1008+
            $data['TADV38']*1152+
            $data['TADV39']*1408+
            $data['TADV40']*1664+
            $data['TADV41']*1920+
            $data['TADV42']*2560+
            $data['TADV43']*3584+
            $data['TADV44']*4096)/($sum*1000); 
        } else {
            $avg=0;
        }
        return $avg;
    }