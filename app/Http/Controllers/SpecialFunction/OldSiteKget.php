<?php 
#namespace App\Http\Controllers\SpecialFunction;


class OldSiteKget {

/**
     * 获取kget与分发docker
     *
     * @return mixed
     */
   public function sendInfo2Docker($ipListOri, $info = null, $source = null){
        $tableName = 'OldSiteOpenRemind';
        //$nodeList = Config::get("sitekget.nodeList");
        // 从文件中读取数据到PHP变量  
        $json_string = file_get_contents('/opt/lampp/htdocs/genius/public/common/json/nodeList.json');  
          
        // 把JSON字符串转成PHP数组  
        $nodeList = json_decode($json_string, true);  
        //获取kget日志,并解析入库
        $date = new DateTime();
        $TIME = $this->udate('YmdHisu');
        $pdo = new PDO("mysql:host=10.197.132.26;port=8066;dbname=NEWSITESTATE","root","mongs");
        $dbName = "kget".$date->format('ymd');
        $pdoKget = new PDO("mysql:host=127.0.0.1;dbname=$dbName","root","mongs");
        $count = $pdoKget->query("select count(*) from mongs.kgetTask where taskName='$dbName'")->fetchColumn();
        if ($count == 0) {
            $dbName = "kget".$date->sub(new DateInterval('P1D'))->format('ymd');
        }
        //$dbName = "kget180719";
        $pdoKget = new PDO("mysql:host=127.0.0.1;dbname=$dbName","root","mongs");
        $ipList = array();
        foreach ($ipListOri as $city => $ipAddrs) {
            foreach ($ipAddrs as $ipAddr) {
                $num = $pdoKget->query("select count(*) from mongs.kgetTask where taskName='$dbName'")->fetchColumn();
                $meContextCount = 0;
                if ($num > 0) {
                    $meContextCount = $pdoKget->query("select count(*) from $dbName.ENodeBFunction where ipAddr='$ipAddr'")->fetchColumn();
                    if ($meContextCount > 0) {
                        $pdo->query("update $tableName set IsGetKget='Y' where city='$city' and ipAddr='".$ipAddr."'");
                    }
                }
                print_r($meContextCount);
                $sql = "select count(*) from $tableName where IsExistKget='N' and IsGetKget='N' and city='$city' and ipAddr ='$ipAddr'";
                $count = $pdo->query($sql)->fetchColumn();
                if ($count > 0){
                    if (array_key_exists($city, $ipList)) {
                        if (!in_array($ipAddr, $ipList[$city])) {
                            $ipList[$city][]= $ipAddr;
                        }
                    }else{
                        $ipList[$city][]= $ipAddr;
                    }
                }
            }
        }
        foreach ($ipList as $city => $ipAddrs) {
            $nodeIp = $nodeList[$city]['ip'];
            $port = $nodeList[$city]['port'];
            $user = $nodeList[$city]['user'];
            $password = $nodeList[$city]['password'];
            $siteIps = implode(",", $ipAddrs);
            $command = "sh /opt/gback/gtools/loginSiteGetKget/batchSiteGetKget.sh $nodeIp $user $password $siteIps $TIME $dbName $city $tableName $port >> /data/trace/siteKget/log/batchSiteGetKget.log";
            print_r(system($command));
        }
       
        //分发ipList到docker
        foreach ($ipList as $city => $ipAddrs) {
            $fileName = "/data/trace/siteKget/ipList/ipList_".$city."_".$this->udate('YmdHisu').".txt";
            $myfile = fopen($fileName, "a+") or die("Unable to open file!");
            $content = "";
            foreach ($ipAddrs as $ipAddr) {
                $content = $content.$ipAddr."\r\n";
            }
            fwrite($myfile, $content);
            fclose($myfile);
            $remoteIp = $nodeList[$city]['ip'];
            $remoteFile = "/data/ftpcfg";
            $command = "sh /opt/gback/gtools/loginSiteGetKget/runScpExpect.sh $fileName $remoteIp $remoteFile >> /data/trace/siteKget/log/runScpExpect.log";
            //print_r($command);
            exec($command);
        }
        //新站检查结果上传到阿里云
        $runTime = $this->udate('YmdHisu');
        foreach ($ipList as $city => $ipAddrs) {
            $meContextCount = $pdoKget->query("select count(*) from $dbName.ENodeBFunction where ipAddr in ('".implode("','", $ipAddrs)."')")->fetchColumn();
            print_r("meContextCount:".$meContextCount);
            if ($meContextCount > 0) {
                $sql = "select GROUP_CONCAT(distinct meContext) meContexts from $dbName.ENodeBFunction where ipAddr in ('".implode("','", $ipAddrs)."');";
                $res = $pdoKget->query($sql);
                $meContexts = $res->fetchColumn();
                print_r($meContexts);
                if ($meContexts) {
                    print_r("call mongs.newSiteParamCheck('$dbName','$city','$meContexts','$runTime')");
                    $pdoKget->query("call mongs.newSiteParamCheck('$dbName','$city','$meContexts','$runTime')");
                }
            }
        }
        $runCount = $pdoKget->query("select count(*) from mongs.newSiteParamCheck where runTime='$runTime'")->fetchColumn();
        //print_r("runCount:".$runCount);
        if ($runCount > 0){
            $command = "sh /opt/mongs/data_to_alicloud/newSiteParamCheck_to_alicloud.sh $runTime";
            //print_r($command);
            system($command);
        }
        //收集新站信息到mongs.newSiteLte
        if ($source) {
            foreach ($ipList as $city => $ipAddrs) {
                $command = "call mongs.getNewSiteInfo('$dbName','$city','".implode(",", $ipAddrs)."');";
                $pdoKget->query($command);
            }
        }
        return;
    }
    /**
     * 获取当前时间毫秒级
     *
     * @return str
     */
    public function udate($format = 'u', $utimestamp = null) {
        if (is_null($utimestamp))
            $utimestamp = microtime(true);

        $timestamp = floor($utimestamp);
        $milliseconds = round(($utimestamp - $timestamp) * 1000000);

        return date(preg_replace('`(?<!\\\\)u`', $milliseconds, $format), $timestamp);
    }
}