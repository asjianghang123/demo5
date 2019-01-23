<?php
    $aOptions = array(
        'connectTimeoutMS' => 86400000,
        'socketTimeoutMS' => 86400000,
        'readPreference' => \MongoClient::RP_PRIMARY
    );
    $m = new MongoClient('10.39.148.186:27028', $aOptions);
    getTableTree_mongodb($m);
    function getTableTree_mongodb($m)
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
        foreach ($items as $item) {
            if (preg_match("/^kget\d{6}$/",$item) == 1) {
                $dbname = $item;
                break;
            }
        }
        print_r($dbname."\n");
        $elements = $db->$dbname->distinct('table');
        
        $i = 0;
        $trees = array();
        foreach ($elements as $element) {
            $tableArr = explode("_",$element);
            $tree = arrayToTree1($tableArr,count($tableArr),0,$i++);
            $trees = mergeArray1($trees,$tree);
        }
        $tableJson = arrayToJson($trees);
        // return json_encode($tableJson);

        $fileName    = "/opt/lampp/htdocs/genius/public/common/json/parameterTreeData_mongodb.json";
        $myFile      = fopen($fileName, "w") or die("Unable to open file!");
        fwrite($myFile, json_encode($tableJson));
        fclose($myFile);
    }

    function arrayToTree1($tableArr,$length,$i,$id)
    {
        $trees = array();
        if ($i < $length-1) {
            $trees[$tableArr[$i++]] = arrayToTree1($tableArr,$length,$i,$id);
        } else {
            $trees[$tableArr[$i++]] = array("id" => $id);
        }
        return $trees;
    }

    function mergeArray1($array1,$array2)
    {
        if ($array1 == []) {
            return $array2;
        }
        $return = array();
        $key  = array_keys($array2)[0];
        if (isset($array1[$key])) {
            $array1[$key] = mergeArray1($array1[$key],$array2[$key]);
            return $array1;
        } else {
            return array_merge($array1,$array2);
        }
    }

    function arrayToJson($array){
        $return = array();
        foreach ($array as $key => $value) {
            $temp = array();
            if ($key != "id") {
                $temp["text"] = $key;
                $nodes = arrayToJson($value);
                if (count($nodes)>0) {
                    $temp["nodes"] = $nodes;
                }
                array_push($return,$temp);
            }
        }
        return $return;
    }

?>