<?php
/**
  * 江苏本地4GLTE指标备份
  * 数据库 siteSTS
  * 输出格式：字段名=表名
  * li
**/
    $dbName = "siteSTS";
    $Path = "/opt/lampp/htdocs/genius_li/public/common/txt/LTELocalCounters.txt";
    // $Path = "/home/lijian/LTELocalCounters.txt";
    $pdo = new PDO("mysql:host=localhost;dbname=siteSTS", "root", "mongs");
    $sql = "SELECT DISTINCT TABLE_NAME FROM information_schema.`COLUMNS` WHERE TABLE_SCHEMA = :dbName;";
    $sth = $pdo->prepare($sql);
    $sth->execute(array(':dbName'=>$dbName));
    $file = fopen($Path, "w");
    while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
        $tableName = $row['TABLE_NAME'];
        $sql = "SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE table_name = :tableName and table_schema = :dbName;";
        $sths = $pdo->prepare($sql);
        $sths->execute(array(':tableName'=>$tableName, ':dbName'=>$dbName));
        while ($rows = $sths->fetch(PDO::FETCH_ASSOC)) {
            if ($rows['COLUMN_NAME'] == 'id' || $rows['COLUMN_NAME'] == 'DATE_ID' || $rows['COLUMN_NAME'] == 'HOUR_ID' || $rows['COLUMN_NAME'] == 'MIN_ID' || $rows['COLUMN_NAME'] == 'SubNetwork' || $rows['COLUMN_NAME'] == 'ENodeBFunction' || $rows['COLUMN_NAME'] == 'ERBS'|| $rows['COLUMN_NAME'] == 'EUtranCellRelation' || $rows['COLUMN_NAME'] == 'EUtranCellTDD' || $rows['COLUMN_NAME'] == 'DC_index' ) {
                continue;
            }
            $name = $rows['COLUMN_NAME']."=".$tableName."\r\n";
            // $name = strtolower($rows['COLUMN_NAME']."=".$tableName."\r\n");
            fwrite($file, $name);
        }
    }
    fclose($file);
    exec("chmod -R 777 $Path");