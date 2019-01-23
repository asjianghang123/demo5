<?php
		$siteIps = "";
		$dbName = "";
		$city = "";
		$TIME = "";
		$tableName = "";
		$dbName = getopt('a:b:c:d:e:')['a'];
		$city = getopt('a:b:c:d:e:')['b'];
		$siteIps = getopt('a:b:c:d:e:')['c'];
		$TIME = getopt('a:b:c:d:e:')['d'];
		$tableName = getopt('a:b:c:d:e:')['e'];
		print_r(getopt('a:b:c:d:e:'));
		$ipAddrs = explode(",", $siteIps);
		update($ipAddrs,$city,$dbName,$TIME,$tableName);
		function update($ipAddrs,$city,$dbName,$TIME,$tableName){
			$pdo = new PDO("mysql:host=10.40.57.134;port=8066;dbname=NEWSITESTATE","root","mongs");
			$pdoKget = new PDO("mysql:host=127.0.0.1;dbname=$dbName","root","mongs");
			//更新isGetKget状态
	        $errorCode='';
	        $errorMessage='';
	        include_once("/opt/lampp/htdocs/genius/public/common/php/filterLogUtil.php");
	        $obj = new filterLogUtil();
	        $filePath = "/data/trace/siteKget/log/".$TIME.".log";

	        $ipConnInfos = $obj->filterLogFile($filePath,$ipAddrs,$errorCode,$errorMessage);
	        print_r($ipConnInfos);
	        foreach ($ipAddrs as $ipAddr) {
	            $meContextCount = $pdoKget->query("select count(*) from $dbName.ENodeBFunction where ipAddr='$ipAddr'")->fetchColumn();
	            print_r($meContextCount);
	            if ($ipConnInfos[$ipAddr][0] == 'OK') {
	                    $pdo->query("update $tableName set IsGetKget='Y' where city='$city' and ipAddr='".$ipConnInfos[$ipAddr][2]."'");
	            }else if($ipConnInfos[$ipAddr][0] == 'no contact'){
	                $pdo->query("update $tableName set IsGetKget='IP无法连接' where city='$city' and ipAddr='".$ipConnInfos[$ipAddr][2]."'");
	            }else if ($ipConnInfos[$ipAddr][0] == 'Timeout') {
	                $pdo->query("update $tableName set IsGetKget='Timeout' where city='$city' and ipAddr='".$ipConnInfos[$ipAddr][2]."'");
	            }
	        }
	        $meContextCount = $pdoKget->query("select count(*) from $dbName.ENodeBFunction where ipAddr in ('".implode("','", $ipAddrs)."')")->fetchColumn();
            print_r("meContextCount:".$meContextCount);
            if ($meContextCount > 0) {
            	$sql = "select GROUP_CONCAT(distinct meContext) meContexts from $dbName.ENodeBFunction where ipAddr in ('".implode("','", $ipAddrs)."');";
	            $res = $pdoKget->query($sql);
                $meContexts = $res->fetchColumn();
                print_r($meContexts);
                if ($meContexts) {
                    $command = "sh /opt/gback/mparser/scripts/kget/procedure/TDDNew/kgetProcedure.sh $dbName $meContexts $city >> /data/trace/siteKget/log/kgetProcedure.log";
                    print_r($command);
                    exec($command);
                }
            }

		}
		
                
?>