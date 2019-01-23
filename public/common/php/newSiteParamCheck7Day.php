<?php
//mycat中获取新站信息;
$db = new PDO("mysql:host=10.40.57.134;port=8066;dbname=NEWSITESTATE","root","mongs");

$sql = "select distinct cellName as meContext,city,date(date_id) date_id from NewSiteRemind where isGetKget='Y' and DAYOFYEAR(date_id) > DAYOFYEAR(CURDATE())-7;";

$date = new DateTime();
$date_id = $date->format('Y-m-d');
$dbName = "kget".$date->format("ymd");
$yesdbName = "kget".$date->sub(new DateInterval('P1D'))->format('ymd');
$meContextList = array();
$curDateMeContextList = array();
$rs = $db->query($sql,PDO::FETCH_ASSOC);
if ($rs) {
	$rows = $rs->fetchAll();
	foreach ($rows as $row) {
		if (array_key_exists($row['city'], $meContextList)) {
            if (!in_array($row['meContext'], $meContextList[$row['city']])) {
                $meContextList[$row['city']][]= $row['meContext'];
            }
        }else{
            $meContextList[$row['city']][]= $row['meContext'];
        }
        if ($row['date_id'] == $date_id) {
        	if (array_key_exists($row['city'], $curDateMeContextList)) {
	            if (!in_array($row['meContext'], $curDateMeContextList[$row['city']])) {
	                $curDateMeContextList[$row['city']][]= $row['meContext'];
	            }
	        }else{
	            $curDateMeContextList[$row['city']][]= $row['meContext'];
	        }
        }
	}
}
//获取kget新站检查结果
#$dbName = getopt('a:')['a'];
$db = new PDO("mysql:host=127.0.0.1;port=3306;dbname=$dbName","root","mongs");
$meContexts = "";
foreach ($meContextList as $city => $meContextArr) {
    $meContexts = implode(",", $meContextArr);
    //print_r($meContexts);
    if ($meContexts) {
        //print_r("call mongs.newSiteParamCheck7Day('$dbName','$city','$meContexts')");
        $db->query("call mongs.newSiteParamCheck7Day('$dbName','$city','$meContexts')");
    }
}
//当天新开站可能入到昨天的kget中
$meContexts = "";
foreach ($curDateMeContextList as $city => $meContextArr) {
    $meContexts = implode(",", $meContextArr);
    if ($meContexts) {
        //print_r("call mongs.newSiteParamCheck7Day('$yesdbName','$city','$meContexts')");
        $db->query("call mongs.newSiteParamCheck7Day('$yesdbName','$city','$meContexts')");
    }
}

?>
