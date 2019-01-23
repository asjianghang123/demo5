<?php
	$date = new DateTime();
    $day = $date->format('ymd');
    //$dbname  = 'kget'.$day;
    //$dbname = 'kget170601';
    //$pdo = new PDO("mysql:host=127.0.0.1;dbname=information_schema","root","mongs");  //实例化一个类的时候，会自动的调用__construct()构造函数
    //
    $dbname = getopt('d:')['d'];
    $pdo = new PDO("mysql:host=127.0.0.1;dbname=$dbname","root","mongs");
    getTreeData($pdo);
    function getTreeData($pdo){
        $sql = "select id, pmoName,moName from moTreeInfo group by moName";
        $rs  = $pdo->query($sql);
        if ($rs) {
            $rows = $rs->fetchAll(PDO::FETCH_ASSOC);
            $result = displayTree('parent', $rows);
            $fileName    = "/opt/lampp/htdocs/genius/public/common/json/parameterTreeData.json";
            $myFile      = fopen($fileName, "w") or die("Unable to open file!");
            fwrite($myFile, json_encode($result));
            fclose($myFile);
        }
    }
    function displayTree($pmoName, $rows){
        $result = array();
        foreach ($rows as $row) {
            $arr = array();
            if ($row['pmoName'] == $pmoName) {
                $arr['id'] = $row['id'];
                #$arr['text'] = $row['moName'];
                if ( strpos($row['moName'], "_2") !== false) {

                    $arr['text'] = str_replace("_2", "_FDD", $row['moName']);

                } else{
                    $arr['text'] = $row['moName'];
                }
                
                if ($nodes = displayTree($row['moName'], $rows)) {
                    $arr['nodes'] = $nodes;
                }
                array_push($result, $arr);
            }
        }
        return $result;
    }

?>