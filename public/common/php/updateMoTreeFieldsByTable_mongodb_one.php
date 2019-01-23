<?php
    $aOptions = array(
        'connectTimeoutMS' => 86400000,
        'socketTimeoutMS' => 86400000,
        'readPreference' => \MongoClient::RP_PRIMARY
    );
    $m = new MongoClient('10.39.148.186:27028', $aOptions);
    getTableFields_mongodb($m);
    function getTableFields_mongodb($m)
    {
        // $dbn = new DataBaseConnection();
        // $m = $dbn->getMongoDB("mongoDB_ctr");
        $db = $m->kgetParser;
        $collections = $db->getCollectionNames();
        $items       = array();
        foreach ($collections as $collection) {
            array_push($items, $collection);
        }
        rsort($items);
        $dbname = $items[0];
        print_r($dbname."\n");
        $folder = "/opt";
        $fieldJson = array();
        $tables = $db->$dbname->distinct('table');
        //新建临时数据库
        // $dbt = $m->kgetTemp;
        foreach ($tables as $table) {
            if ($table == "_") {
                continue;
            }
            print_r("========".$table." 开始=======\n");
            $command = "sudo mongo 10.39.148.186:27028/kgetParser --quiet --eval \"var collection = '$dbname',limit = 1,persistResults=true,query = {'table' :'$table'}\" /opt/lampp/htdocs/genius/public/variety.js";
            exec($command);
            print_r("========".$table." 取key=======\n");
            $dbv = $m->varietyResults;
            $collectionName = $dbname."Keys";
            $keys = $dbv->$collectionName->distinct('_id');
            $keyArray = array();
            foreach ($keys as $key) {
                array_push($keyArray,$key['key']);
            }
            // print_r(json_encode($keyArray)."\n");
            array_push($fieldJson,array("table"=>$table,"name"=>end(explode("_",$table)),"fields"=>$keyArray));
            print_r("========".$table." 完成=======\n");
        }

        $fileName    = "/opt/lampp/htdocs/genius/public/common/json/parameterTreeField_mongodb.json";
        file_put_contents($fileName, json_encode($fieldJson));
    }

?>