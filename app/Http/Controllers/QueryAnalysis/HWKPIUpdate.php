<?php
/**
  * 清远本地2G指标备份
  * 数据库 apg_sts
  * 输出格式：字段名=表名
  * li
**/
    $dbName = "hwpm";
    $Path = "/opt/lampp/htdocs/genius/public/common/txt/HWCounters.txt";
    $pdo = new PDO("mysql:host=localhost;dbname=hwpm", "root", "mongs");
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
            if ($rows['COLUMN_NAME'] == 'id' || $rows['COLUMN_NAME'] == 'TimeStampdate' || $rows['COLUMN_NAME'] == 'hourid' || $rows['COLUMN_NAME'] == 'userLabel' || $rows['COLUMN_NAME'] == 'FunctionName' || $rows['COLUMN_NAME'] == 'LocalCellID' || $rows['COLUMN_NAME'] == 'CellName'|| $rows['COLUMN_NAME'] == 'eNodeBID' || $rows['COLUMN_NAME'] == 'CellFDDTDDindication') {
                continue;
            }
            $name = $rows['COLUMN_NAME']."=".$tableName."\r\n";
            // $name = strtolower($rows['COLUMN_NAME']."=".$tableName."\r\n");
            fwrite($file, $name);
        }
    }
    fclose($file);
    exec("sudo chmod -R 777 $Path");