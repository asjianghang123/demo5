<?php
    $dataBase = include "config/dataBase.php";
    $city = trim($argv[1]);
    $sql = '';
    $host     = $dataBase['AutoKPI']['host'];
    $database = $dataBase['AutoKPI']['database'];
    $username = $dataBase['AutoKPI']['username'];
    $password = $dataBase['AutoKPI']['password'];
    //1.总表下载 表temp_lowaccesscell
    $pdo = new PDO("mysql:host=$host;dbname=$database", "$username", "$password");
    //1-1.建表
    $file = "sql/createTable_badHandoverCell.sql";
    if (is_file($file)) {
        $sql = file_get_contents($file);
    } else {
        echo "createTable_badHandoverCell.sql 不存在,请新建表!";
        return;
    }
    $pdo->query($sql);
    //1-2运行sql，开始插入初始数据
    $file = "sql/insert30Data_badHandoverCell.sql";
    if (is_file($file)) {
        $sql = str_replace('$city', $city, file_get_contents($file));
    } else {
        echo "insert30Data_badHandoverCell.sql 不存在,请新建表!";
        return;
    }
    $pdo->query($sql); 

    //2.获取所有小区名=基站名 一一对应
    $sql = "SELECT cell,siteName FROM temp_badhandovercell,mongs.siteLte WHERE temp_badhandovercell.city = ? AND cell=cellName;";
    $sth = $pdo->prepare($sql);
    $sth->execute(array($city));
    $txt = "txt/badhandover.txt";
    $file = fopen($txt, "a");
    while ($row = $sth->fetch(PDO::FETCH_ASSOC)) {
        $name = "\r\n".$city."=".$row['cell']."=".$row['siteName'];
        fwrite($file, $name);
    }
    fclose($file);

    //确保所有城市小区都进txt(留待观察)
    // sleep(10);
