<?php
	$date = new DateTime();
    $day = $date->format('ymd');
    //$dbname  = 'kget'.$day;
    //$dbname = 'kget170715';
    //$pdo = new PDO("mysql:host=127.0.0.1;dbname=information_schema","root","mongs");  //实例化一个类的时候，会自动的调用__construct()构造函数
    //
    $meContexts = "";
    $dbname = getopt('d:c:')['d'];
    if(array_key_exists('c', getopt('d:c:'))){
        $meContexts = getopt('d:c:')['c'];
        print_r(getopt('d:c:'));
    }
    $pdo = new PDO("mysql:host=127.0.0.1;dbname=$dbname","root","mongs");
    runProcedure($pdo, $dbname, $meContexts);
    function runProcedure($pdo, $dbname, $meContexts){
        $sql = "select id,networkStandard,city from mongs.templateParaBaseline where isNewSite='yes' and isAutoExecute='yes' and id in (select DISTINCT templateId from mongs.formulaParaBaseline);";
        $rs = $pdo->query($sql);
        if ($rs) {
            $rows = $rs->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                $id = $row['id'];
                $networkStandard = $row['networkStandard'];
                $city = $row['city'];
                $sql = "call mongs.baselinecheckByCity('$dbname',$id,'$networkStandard','$meContexts','','$city')";
                $pdo->query($sql);
            }
        }
    }

?>