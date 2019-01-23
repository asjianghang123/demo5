<?php 
    $pdo = new PDO("mysql:host=10.39.148.187;dbname=AutoKPI", "root", "mongs");
    //高掉线
    $sql = "TRUNCATE TABLE HighLostCellTableEveryOneHour;";
     $pdo->query($sql);
    $sql = "INSERT INTO HighLostCellTableEveryOneHour SELECT * FROM Temp_HighLostCellTableEveryOneHour;";
    $pdo->query($sql);
    // 低接入
    $sql = "TRUNCATE TABLE LowAccessCellTableEveryOneHour;";
    $pdo->query($sql);
    $sql = "INSERT INTO LowAccessCellTableEveryOneHour SELECT * FROM Temp_LowAccessCellTableEveryOneHour;";
    $pdo->query($sql);
    // 切换差
    $sql = "TRUNCATE TABLE BadHandoverCellTableEveryOneHour;";
    $pdo->query($sql);
    $sql = "INSERT INTO BadHandoverCellTableEveryOneHour SELECT * FROM Temp_BadHandoverCellTableEveryOneHour;";
    $pdo->query($sql);
    // 邻分区
       $sql = "TRUNCATE TABLE NeighBadHandoverCellTableEveryOneHour;";
    $pdo->query($sql);
    $sql = "INSERT INTO NeighBadHandoverCellTableEveryOneHour SELECT * FROM Temp_NeighBadHandoverCellTableEveryOneHour;";
    $pdo->query($sql);

    sleep(30);
    //删除临时文件的数据
    $sql="TRUNCATE TABLE Temp_HighLostCellTableEveryOneHour";
    $pdo->query($sql);
    $sql="TRUNCATE TABLE Temp_LowAccessCellTableEveryOneHour";
    $pdo->query($sql);
    $sql="TRUNCATE TABLE Temp_BadHandoverCellTableEveryOneHour";
    $pdo->query($sql);
    $sql="TRUNCATE TABLE Temp_NeighBadHandoverCellTableEveryOneHour";
    $pdo->query($sql);

?>