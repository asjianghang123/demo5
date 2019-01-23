<?php
    include "php/alarmNum.php";                     //告警数量
    include "php/weakCover.php";                    //弱覆盖
    include "php/highTraffic.php";                  //高话务
    include "php/zhicha.php";                       //质差 
    include "php/avgPrb.php";                       //干扰  
    include "php/parameter_highlost.php";               //参数   
    include "php/relevance_wirelesscallrate_interfere.php";  //相关性--无线掉线率&干扰
    include "php/relevance_zhicha.php";             //相关性--无线掉线率&质差 
    include "php/relevance_weakCover.php";          //相关性--无线接通率&弱覆盖
    include "php/mr_overlapcover.php";              //重叠覆盖--重叠覆盖度 notification(只能查询昨天的数据，不能实时)
    include "php/mr_neighcell.php";                 //邻区--需要加邻区数量 notification(只能查询昨天的数据，不能实时)
    include 'php/mr_avgTA.php';                     //平均TA输出
    $dataBase = include "config/dataBase.php";
    // $city = trim($argv[1]);
    $sql = '';
    $host     = $dataBase['AutoKPI']['host'];
    $database = $dataBase['AutoKPI']['database'];
    $username = $dataBase['AutoKPI']['username'];
    $password = $dataBase['AutoKPI']['password'];

    $pdo = new PDO("mysql:host=$host;dbname=$database", "$username", "$password");
    
    $date = date("Y-m-d");
    $hour = date("H", strtotime("-1 hour"));

    $erbs = [];  //arr['小区']='基站'
    $erbsArr = [];
    $cellArr = [];

    $txt = "txt/highlost.txt";
    $file = fopen($txt, "r");
    fgets($file);
    while (!feof($file)) {
        // if(fgets($file) == '') {
        //     continue;
        // }
        $arr = explode("=", trim(fgets($file)));
        $erbs[$arr[1]] = $arr[2];  //arr['小区']='基站'
        array_push($erbsArr, $arr[2]);
        array_push($cellArr, $arr[1]);
    }
    fclose($file);

    $erbsStr = "('".implode("','", $erbsArr)."')";
    $cellStr = "('".implode("','", $cellArr)."')";

    insertData_alarmNum($pdo, $erbs, $erbsStr, $cellStr, $date, $txt, 'temp_highlostcell');   //告警数量
    insertData_polar_alarm($pdo, $erbs, $erbsStr, $cellStr, $date, $txt, 'temp_highlostcell');//Polar-告警

    $weakCoverRateFlag = insertData_weakCover($pdo, $erbs, $erbsStr, $cellStr, $date, $hour, $txt, 'temp_highlostcell', 'highLostCell_ex');  //弱覆盖--RSRP<-116的比例 

    insertData_highTraffic($pdo, $erbs, $erbsStr, $cellStr, $date, $hour, $txt, 'temp_highlostcell', 'highLostCell_ex'); //高话务/MAC层时延/SRcongestion数/SR拥塞比

    

    insertData_AvgPRB($pdo, $erbs, $erbsStr, $cellStr, $date, $hour, $txt, 'temp_highlostcell', 'highLostCell');      //干扰--平均PRB/Polar-干扰
    insertData_AvgPRB_Refine($pdo, $erbs, $erbsStr, $cellStr, $date, $hour, $txt, 'temp_highlostcell', 'highLostCell');   //干扰--Polar-干扰 规则细化1
    insertData_AvgPRB_Refine_2($pdo, $erbs, $erbsStr, $cellStr, $date, $hour, $txt, 'temp_highlostcell', 'highLostCell');   //干扰--Polar-干扰 规则细化2

    insertData_parameter_highlost($pdo, $host, $username, $password, $erbs, $erbsStr, $cellStr, $date, $hour, $txt, $cellArr, 'temp_highlostcell', 'highLostCell');   //参数

    insertData_Relevance_wirelessCallRate_interfere($pdo, $erbs, $erbsStr, $cellStr, $cellArr, $date, $hour, $txt, 'temp_highlostcell', 'highLostCell', '无线掉线率');   //相关性--无线掉线率&干扰

    insertData_Relevance_zhicha($pdo, $erbs, $erbsStr, $cellStr, $cellArr, $date, $hour, $txt, 'temp_highlostcell', 'highLostCell', '无线掉线率');   //相关性--无线掉线率&质差

    insertData_Relevance_weakCover($pdo, $erbs, $erbsStr, $cellStr, $cellArr, $date, $hour, $txt, 'temp_highlostcell', 'highLostCell', '无线掉线率');  //相关性--无线掉线率&弱覆盖 
    

    $mr_host = $dataBase['MR']['host'];
    $mr_port = $dataBase['MR']['port'];
    $mr_username = $dataBase['MR']['username'];
    $mr_password = $dataBase['MR']['password'];
    $mr_pdo = '';
    $err = '';

    try{
        // $mr_pdo = new PDO("mysql:host=$mr_host:$mr_port;dbname=$database", "$mr_username", "$mr_password", array(PDO::ATTR_TIMEOUT => 3));
        $mr_pdo = new PDO("mysql:host=$mr_host:$mr_port", "$mr_username", "$mr_password", array(PDO::ATTR_TIMEOUT => 3));
    }catch(PDOException $e) {
        $err_txt = "err/err.txt";
        $file_err = fopen($err_txt, "a");
        $err = date("Y-m-d H:i:s").":".$database."数据库连接超时/失败!\r\n";
        fwrite($file_err, $err);
        fclose($file_err);
        continue;
    }
    $mr_txt = "config/mr_city.conf";
    $file = fopen($mr_txt, "r");
    while (!feof($file)) {
        $arr = explode(" ", trim(fgets($file)));
        if (count($arr) == 1) {
            continue;
        }
        $city = $arr[0];
        $database = $arr[1];
        
        
        $overCoverFlag = insertData_mr_overlapCover($pdo, $mr_pdo, $city, $database, $erbs, $erbsStr, $cellStr, 'temp_highlostcell', 'highLostCell');//重叠覆盖--重叠覆盖度 notification(只能查询昨天的数据，不能实时)

        insertData_mr_neighCell($pdo, $mr_pdo, $city, $database, $erbs, $erbsStr, $cellStr, 'temp_highlostcell', 'highLostCell', $weakCoverRateFlag); //邻区--需要加邻区数量 notification(只能查询昨天的数据，不能实时)

        insertData_mr_avgTA($pdo, $mr_pdo, $city, $database, $erbs, $erbsStr, $cellStr, 'temp_highlostcell', 'highLostCell'); //平均TA的输出，数据来源为MRS分析的TADV分析,取差小区出现的最近的一个小时的结果
        // $mr_pdo = null;
    }
    $mr_pdo = null;
    fclose($file);

    insertData_zhicha($pdo, $erbs, $erbsStr, $cellStr, $date, $hour, $txt, 'temp_highlostcell', 'highLostCell_ex', $weakCoverRateFlag, $overCoverFlag);     //质差