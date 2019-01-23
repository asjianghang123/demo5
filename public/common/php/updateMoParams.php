<?php
	$date = new DateTime();
    $day = $date->format('ymd');
    //$dbname  = 'kget'.$day;
    // $dbname = 'kget171231';
    $dbname = getopt('d:')['d'];
    $pdo = new PDO("mysql:host=127.0.0.1;dbname=information_schema","root","mongs");  //实例化一个类的时候，会自动的调用__construct()构造函数
    getTreeData($dbname, $pdo);
    /**
     * 获得检查项目列表
     *
     * @return void
     */
    function getTreeData($dbname, $pdo)
    {
        $schema      = $dbname;
        $filename    = "/opt/lampp/htdocs/genius/public/common/json/parameterTreeData.json";
        $json_string = file_get_contents($filename);
        $datas       = json_decode($json_string, true);
        $myfile      = fopen("/opt/lampp/htdocs/genius/public/common/txt/paramDistribution.txt", "w") or die("Unable to open file!");
        _fun($datas, $myfile, $schema, $pdo);
        fclose($myfile);

    }//end getTreeData()
    /**
     * What's it?
     *
     * @param array  $a      what?
     * @param string $myfile 文件名
     * @param string $schema TABLE_SCHEMA
     *
     * @return void
     */
    function _fun($a, $myfile, $schema, $pdo)
    {
        foreach ($a as $key => $val) {
            if (is_array($val)) {
                // 如果键值是数组，则进行函数递归调用
                _fun($val, $myfile, $schema, $pdo);
            } else {
                // 如果键值是数值，则进行输出
                if ($key == 'text') {
                    if ( strpos($val, "_FDD") !== false) {
                        $val = str_replace("_FDD", "_2", $val);
                    }
                    $sql = "select COLUMN_NAME from `COLUMNS` where TABLE_NAME='$val' and TABLE_SCHEMA='$schema'";
                    $rs  = $pdo->query($sql);

                    if ($rs) {
                        $rows = $rs->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($rows as $row) {
                            $txt = $val.'='.$row['COLUMN_NAME']."\r\n";
                            fwrite($myfile, $txt);
                        }
                    }
                }
            }
        }

    }//end fun()

?>