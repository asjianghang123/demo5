<?php

/**
 * CtrSignalingAnalysisController.php
 *
 * @category ComplaintHandling
 * @package  App\Http\Controllers\ComplaintHandling
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
namespace App\Http\Controllers\ComplaintHandling;

use App\Http\Controllers\Common\DataBaseConnection;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use MongoClient;
use MongoCode;
use MongoId;

/**
 * CTR信令分析
 * Class CtrSignalingAnalysisController
 *
 * @category ComplaintHandling
 * @package  App\Http\Controllers\ComplaintHandling
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class CtrSignalingAnalysisController extends Controller
{

    /**
     * 获得筛选流程后报表数据
     *
     * @return array
     */
    public function getChartDataFilter()
    {
        $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $rows = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 50;
        $offset = ($page - 1) * $rows;

        $dbn = new DataBaseConnection();
        $m = $dbn->getMongoDB("mongoDB_ctr");
        $db = $m->ctr;
        if (!Auth::user()) {
            echo "login";
            return;
        }
        $user = Auth::user();
        $dataBase = $user["user"] . "_my_result";
        $collection = $db->$dataBase;

        $count = $collection->count();
        if ($count == 0) {
            echo "false";
            return;
        }

        $ips = $collection->find()->fields(array("value" => true))->skip($offset)->limit($rows);//->limit(200);
        $count = $ips->count();
        $nodes = [];

        $i = 0;
        foreach ($ips as $ip) {
            if (array_key_exists("ectr", $ip["value"]["_source"]["layers"])) {
                $date = '';
                if (array_key_exists("einternal.EVENT_PARAM_TIMESTAMP_YEAR", $ip["value"]["_source"]["layers"]["ectr"])) {
                    $date = $ip["value"]["_source"]["layers"]["ectr"]["einternal.EVENT_PARAM_TIMESTAMP_YEAR"] . "-" . $ip["value"]["_source"]["layers"]["ectr"]["einternal.EVENT_PARAM_TIMESTAMP_MONTH"] . "-" . $ip["value"]["_source"]["layers"]["ectr"]["einternal.EVENT_PARAM_TIMESTAMP_DAY"] . " ";
                }
                $date = $date . $ip["value"]["_source"]["layers"]["ectr"]["einternal.EVENT_PARAM_TIMESTAMP_HOUR"] . ":" . $ip["value"]["_source"]["layers"]["ectr"]["einternal.EVENT_PARAM_TIMESTAMP_MINUTE"] . ":" . $ip["value"]["_source"]["layers"]["ectr"]["einternal.EVENT_PARAM_TIMESTAMP_SECOND"];
                if (array_key_exists("einternal.EVENT_PARAM_TIMESTAMP_MILLISEC", $ip["value"]["_source"]["layers"]["ectr"])) {
                    $date = $date . " " . $ip["value"]["_source"]["layers"]["ectr"]["einternal.EVENT_PARAM_TIMESTAMP_MILLISEC"];
                }

                if (array_key_exists("einternal.EVENT_NAME", $ip["value"]["_source"]["layers"]["ectr"])) {
                    $eventName = $ip["value"]['_source']['layers']['ectr']["einternal.EVENT_NAME"];
                    $nodes[$i]['Event_NAME'] = $eventName;
                } else {
                    $nodes[$i]['Event_NAME'] = 'NULL';
                }

                if (array_key_exists("einternal.EVENT_PARAM_RAC_UE_REF", $ip["value"]["_source"]["layers"]["ectr"])) {
                    $eventName = $ip["value"]['_source']['layers']['ectr']["einternal.EVENT_PARAM_RAC_UE_REF"];
                    $nodes[$i]['EVENT_PARAM_RAC_UE_REF'] = $eventName;
                } else {
                    $nodes[$i]['EVENT_PARAM_RAC_UE_REF'] = 'NULL';
                }

                if (array_key_exists("einternal.EVENT_PARAM_ENBS1APID", $ip["value"]["_source"]["layers"]["ectr"])) {
                    $eventName = $ip["value"]['_source']['layers']['ectr']["einternal.EVENT_PARAM_ENBS1APID"];
                    $nodes[$i]['EVENT_PARAM_ENBS1APID'] = $eventName;
                } else {
                    $nodes[$i]['EVENT_PARAM_ENBS1APID'] = 'NULL';
                }

                if (array_key_exists("einternal.EVENT_PARAM_MMES1APID", $ip["value"]["_source"]["layers"]["ectr"])) {
                    $eventName = $ip["value"]['_source']['layers']['ectr']["einternal.EVENT_PARAM_MMES1APID"];
                    $nodes[$i]['EVENT_PARAM_MMES1APID'] = $eventName;
                } else {
                    $nodes[$i]['EVENT_PARAM_MMES1APID'] = 'NULL';
                }

                if (array_key_exists("einternal.EVENT_PARAM_GLOBAL_CELL_ID", $ip["value"]["_source"]["layers"]["ectr"])) {
                    $eventName = $ip["value"]['_source']['layers']['ectr']["einternal.EVENT_PARAM_GLOBAL_CELL_ID"];
                    $num0 = ($eventName & 0xFFFFFF00) >> 8;
                    $num1 = $eventName & 0x000000FF;
                    $num1 = $num1 > 10 ? $num1 : '0' . $num1;
                    $eventName = (string)($num0 . ':' . $num1);
                    $nodes[$i]['EVENT_PARAM_GLOBAL_CELL_ID'] = $eventName;
                } else {
                    $nodes[$i]['EVENT_PARAM_GLOBAL_CELL_ID'] = 'NULL';
                }

                if (array_key_exists("einternal.EVENT_PARAM_GUMMEI", $ip["value"]["_source"]["layers"]["ectr"])) {
                    $eventName = $ip["value"]['_source']['layers']['ectr']["einternal.EVENT_PARAM_GUMMEI"];
                    $arr = explode(':', $eventName);
                    $num0 = hexdec($arr[4] . $arr[5]);
                    $num1 = hexdec($arr[6]);
                    $eventName = (string)('460:0:' . $num0 . ':' . $num1);
                    $nodes[$i]['EVENT_PARAM_GUMMEI'] = $eventName;
                } else {
                    $nodes[$i]['EVENT_PARAM_GUMMEI'] = 'NULL';
                }

                $nodes[$i]['Event_Time'] = $date;
                $nodes[$i]["_id"] = $ip["_id"];
                $i++;
            } else {
                $nodes[$i]['Event_Time'] = "NULL";
                $nodes[$i]['Event_NAME'] = "NULL";
                $nodes[$i]['EVENT_PARAM_RAC_UE_REF'] = 'NULL';
                $nodes[$i]['EVENT_PARAM_ENBS1APID'] = 'NULL';
                $nodes[$i]['EVENT_PARAM_MMES1APID'] = 'NULL';
                $nodes[$i]['EVENT_PARAM_GLOBAL_CELL_ID'] = 'NULL';
                $nodes[$i]['EVENT_PARAM_GUMMEI'] = 'NULL';
                $nodes[$i]["_id"] = $ip["_id"];
                $i++;
            }
        }

        $result = [];
        $result["records"] = $nodes;
        $result["total"] = $count;
        return json_encode($result);
    }

    /**
     * 获取报表数据
     *
     * @return void
     */
    public function getChartData()
    {
        $page = Input::get('page', 1);
        $rows = Input::get('limit', 50);
        $offset = ($page - 1) * $rows;
        $dbn = new DataBaseConnection();
        $m = $dbn->getMongoDB("mongoDB_ctr");
        $db = $m->ctr;

        $dataBase = Input::get("dataBase");

        $collection = $db->$dataBase;

        $count = $collection->count();
        if ($count == 0) {
            echo "false";
            return;
        }

        $ips = $collection->find()->fields(array("_source" => true))->skip($offset)->limit($rows);//->limit(200);
        $count = $ips->count();
        $nodes = [];

        $i = 0;
        foreach ($ips as $ip) {
            if (array_key_exists("ectr", $ip["_source"]["layers"])) {
                $date = '';
                if (!is_array($ip["_source"]["layers"]["ectr"])) {
                    $nodes[$i]['Event_Time'] = "NULL";
                    $nodes[$i]['Event_NAME'] = "NULL";
                    $nodes[$i]['EVENT_PARAM_RAC_UE_REF'] = 'NULL';
                    $nodes[$i]['EVENT_PARAM_ENBS1APID'] = 'NULL';
                    $nodes[$i]['EVENT_PARAM_MMES1APID'] = 'NULL';
                    $nodes[$i]['EVENT_PARAM_GLOBAL_CELL_ID'] = 'NULL';
                    $nodes[$i]['EVENT_PARAM_GUMMEI'] = 'NULL';
                    $nodes[$i]["_id"] = $ip["_id"];
                    $i++;
                } else {
                    if (array_key_exists("einternal.EVENT_PARAM_TIMESTAMP_YEAR", $ip["_source"]["layers"]["ectr"])) {
                        $date = $ip["_source"]["layers"]["ectr"]["einternal.EVENT_PARAM_TIMESTAMP_YEAR"] . "-" . $ip["_source"]["layers"]["ectr"]["einternal.EVENT_PARAM_TIMESTAMP_MONTH"] . "-" . $ip["_source"]["layers"]["ectr"]["einternal.EVENT_PARAM_TIMESTAMP_DAY"] . " ";
                    }
                    $date = $date . $ip["_source"]["layers"]["ectr"]["einternal.EVENT_PARAM_TIMESTAMP_HOUR"] . ":" . $ip["_source"]["layers"]["ectr"]["einternal.EVENT_PARAM_TIMESTAMP_MINUTE"] . ":" . $ip["_source"]["layers"]["ectr"]["einternal.EVENT_PARAM_TIMESTAMP_SECOND"];
                    if (array_key_exists("einternal.EVENT_PARAM_TIMESTAMP_MILLISEC", $ip["_source"]["layers"]["ectr"])) {
                        $date = $date . " " . $ip["_source"]["layers"]["ectr"]["einternal.EVENT_PARAM_TIMESTAMP_MILLISEC"];
                    }

                    if (array_key_exists("einternal.EVENT_NAME", $ip["_source"]["layers"]["ectr"])) {
                        $eventName = $ip['_source']['layers']['ectr']["einternal.EVENT_NAME"];
                        $nodes[$i]['Event_NAME'] = $eventName;
                    } else {
                        $nodes[$i]['Event_NAME'] = 'NULL';
                    }

                    if (array_key_exists("einternal.EVENT_PARAM_RAC_UE_REF", $ip["_source"]["layers"]["ectr"])) {
                        $eventName = $ip['_source']['layers']['ectr']["einternal.EVENT_PARAM_RAC_UE_REF"];
                        $nodes[$i]['EVENT_PARAM_RAC_UE_REF'] = $eventName;
                    } else {
                        $nodes[$i]['EVENT_PARAM_RAC_UE_REF'] = 'NULL';
                    }

                    if (array_key_exists("einternal.EVENT_PARAM_ENBS1APID", $ip["_source"]["layers"]["ectr"])) {
                        $eventName = $ip['_source']['layers']['ectr']["einternal.EVENT_PARAM_ENBS1APID"];
                        $nodes[$i]['EVENT_PARAM_ENBS1APID'] = $eventName;
                    } else {
                        $nodes[$i]['EVENT_PARAM_ENBS1APID'] = 'NULL';
                    }

                    if (array_key_exists("einternal.EVENT_PARAM_MMES1APID", $ip["_source"]["layers"]["ectr"])) {
                        $eventName = $ip['_source']['layers']['ectr']["einternal.EVENT_PARAM_MMES1APID"];
                        $nodes[$i]['EVENT_PARAM_MMES1APID'] = $eventName;
                    } else {
                        $nodes[$i]['EVENT_PARAM_MMES1APID'] = 'NULL';
                    }

                    if (array_key_exists("einternal.EVENT_PARAM_GLOBAL_CELL_ID", $ip["_source"]["layers"]["ectr"])) {
                        $eventName = $ip['_source']['layers']['ectr']["einternal.EVENT_PARAM_GLOBAL_CELL_ID"];
                        $num0 = ($eventName & 0xFFFFFF00) >> 8;
                        $num1 = $eventName & 0x000000FF;
                        $num1 = $num1 > 10 ? $num1 : '0' . $num1;
                        $eventName = (string)($num0 . ':' . $num1);
                        $nodes[$i]['EVENT_PARAM_GLOBAL_CELL_ID'] = $eventName;
                    } else {
                        $nodes[$i]['EVENT_PARAM_GLOBAL_CELL_ID'] = 'NULL';
                    }

                    if (array_key_exists("einternal.EVENT_PARAM_GUMMEI", $ip["_source"]["layers"]["ectr"])) {
                        $eventName = $ip['_source']['layers']['ectr']["einternal.EVENT_PARAM_GUMMEI"];
                        $arr = explode(':', $eventName);
                        $num0 = hexdec($arr[4] . $arr[5]);
                        $num1 = hexdec($arr[6]);
                        $eventName = (string)('460:0:' . $num0 . ':' . $num1);
                        $nodes[$i]['EVENT_PARAM_GUMMEI'] = $eventName;
                    } else {
                        $nodes[$i]['EVENT_PARAM_GUMMEI'] = 'NULL';
                    }

                    $nodes[$i]['Event_Time'] = $date;
                    $nodes[$i]["_id"] = $ip["_id"];
                    $i++;
                }

            } else {
                $nodes[$i]['Event_Time'] = "NULL";
                $nodes[$i]['Event_NAME'] = "NULL";
                $nodes[$i]['EVENT_PARAM_RAC_UE_REF'] = 'NULL';
                $nodes[$i]['EVENT_PARAM_ENBS1APID'] = 'NULL';
                $nodes[$i]['EVENT_PARAM_MMES1APID'] = 'NULL';
                $nodes[$i]['EVENT_PARAM_GLOBAL_CELL_ID'] = 'NULL';
                $nodes[$i]['EVENT_PARAM_GUMMEI'] = 'NULL';
                $nodes[$i]["_id"] = $ip["_id"];
                $i++;
            }
        }

        $result = [];
        $result["records"] = $nodes;
        $result["total"] = $count;
        echo json_encode($result);
    }

    /**
     * 获得详细信令
     *
     * @return void
     */
    public function showMessageFilter()
    {
        $id = Input::get("id");
        $dbn = new DataBaseConnection();
        $m = $dbn->getMongoDB("mongoDB_ctr");
        $db = $m->ctr; // 获取名称为 "test" 的数据库
        if (!Auth::user()) {
            echo "login";
            return;
        }
        $user = Auth::user();
        $dataBase = $user["user"] . "_my_result";
        $collection = $db->$dataBase;
        $cursor = $collection->findOne(array("_id" => new MongoId($id)), array("value" => true));
        if (isset($cursor["value"]["_source"]["layers"])) {
            $items = $this->getFields($cursor["value"]["_source"]["layers"]);
        }
        $result = [];
        $result["tree"] = $items;
        echo json_encode($result);
    }

    /**
     * 获得域名列表
     *
     * @param array $packet Element数组
     *
     * @return array
     */
    protected function getFields($packet)
    {
        $tree = [];
        foreach ($packet as $key => $p) {
            $node = [];
            if (!is_array($p)) {
                $node["name"] = $key . ":" . $p;
            } else {
                $node["name"] = $key;
            }
            if (is_array($p)) {
                $children = $this->getFields($p);
                $node["children"] = $children;
            }
            array_push($tree, $node);
        }
        return $tree;
    }

    /**
     * 获得详细信令
     *
     * @return string
     */
    public function showMessage()
    {
        $id = Input::get("id");
        $dbn = new DataBaseConnection();
        $m = $dbn->getMongoDB("mongoDB_ctr");
        $db = $m->ctr; // 获取名称为 "test" 的数据库
        $dataBase = Input::get("dataBase");
        $collection = $db->$dataBase;
        $cursor = $collection->findOne(array("_id" => new MongoId($id)), array("_source" => true));
        if (isset($cursor["_source"]["layers"])) {
            $items = $this->getFields($cursor["_source"]["layers"]);
        }
        $result = [];
        $result["tree"] = $items;
        return json_encode($result);
    }

    /**
     * 获得数据库名
     *
     * @return mixed
     */
    public function getDataBase()
    {
        $dbn = new DataBaseConnection();
        $m = $dbn->getMongoDB("mongoDB_ctr");
        $db = $m->ctr; // 获取名称为 "ctr" 的数据库
        $collections = $db->getCollectionNames();
        $items = array();
        foreach ($collections as $collection) {
            array_push($items, ["id" => $collection, "text" => $collection]);
        }
        return json_encode($items);
    }

    /**
     * 获得节点表
     *
     * @return string
     */
    public function getNodeTable()
    {
        $aOptions = array(
            'connectTimeoutMS' => 86400000,
            'socketTimeoutMS' => 86400000,
            'readPreference' => \MongoClient::RP_PRIMARY
        );
        $dbn = new DataBaseConnection();
        $MONGO_SERVER = $dbn->getMongoDBArr("mongoDB_ctr");
        $host_port = $MONGO_SERVER['host'] . ':' . $MONGO_SERVER['port'];
        $conn = new MongoClient($host_port, $aOptions);
        $db = $conn->selectDB($MONGO_SERVER['dbname']);
        $dbAll = $db;

        $dataBase = Input::get('dataBase');
        $node = Input::get('node');
        if ($node != '') {
            $nodes = explode(':', $node);
            $value = '';
            if (count($nodes) > 2) {
                for ($i = 1; $i < count($nodes); $i++) {
                    $value = $value . $nodes[$i] . ":";
                }
                $key = $nodes[0];
                $value = substr($value, 0, strlen($value) - 1);
            } else {
                $key = $nodes[0];
                $value = $nodes[1];
            }
        }


        if (!Auth::user()) {
            echo "login";
            return;
        }
        $user = Auth::user();
        if ($node != '') {
            $table = $user["user"] . "_my_result";
        } else {
            $table = $dataBase;
        }
        // echo $table;return;

        $map = new MongoCode("function () {
                //递归匹配字段
                var ok = recursion(this);
                //var ok = true;
                if(ok){
                    emit(this._id,this);
                }}"
        );


        $reduce = new MongoCode("function(key, values) {
                return values[0];};"
        );

        if ($node != '') {
            $scope = ["recursion" => new MongoCode("function (json) {
                for(var key in json){
                    var value = json[key];
                    //判断是不是json结构
                    if(typeof(value) == \"object\" &&
                        Object.prototype.toString.call(value).toLowerCase() == \"[object bson]\" && !value.length){
                        if(true == recursion(value)){
                            return true;
                        }
                    }
                    if(key == filterJson.key&& value == filterJson.value) {
                        return true;
                    }
                }
                return false;}"
            ), "filterJson" => ["key" => $key, "value" => $value]];

            $db->command(array(
                "mapreduce" => $dataBase,
                "map" => $map,
                "reduce" => $reduce,
                "scope" => $scope,
                "out" => $table));
            $collection = $db->$table;
            $ips = $collection->find();
            $fileName = "files/" . $dataBase . "_" . $table . "_" . date('YmdHis') . ".txt";
            $fp = fopen($fileName, "w");
            $i = 0;
            foreach ($ips as $ip) {
                // fputcsv($fp, $ip);
                fwrite($fp, var_export($ip, true));
                fwrite($fp, "\r\n");
                $i++;
                if ($i >= 100000) {
                    break;
                }
            }
            fclose($fp);
            $result['fileName'] = $fileName;
            return $result;
        } else {
            $collection = $dbAll->$table;
            $ips = $collection->find();
            $fileName = "files/" . $dataBase . "_" . date('YmdHis') . ".txt";
            $fp = fopen($fileName, "w");
            // $i = 0;
            foreach ($ips as $ip) {

                fwrite($fp, var_export($ip, true));
                fwrite($fp, "\r\n");
                // $i++;
                // if($i >= 100000) {
                //     break;
                // }           
            }
            fclose($fp);
            // echo $fileName;
            $result['fileName'] = $fileName;
            return $result;
        }


    }
}