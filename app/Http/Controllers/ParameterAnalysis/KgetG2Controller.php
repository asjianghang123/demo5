<?php

/**
 * KgetG2Controller.php
 *
 * @category ParameterAnalysis
 * @package  App\Http\Controllers\ParameterAnalysis
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
namespace App\Http\Controllers\ParameterAnalysis;

use App\DatabaseConn;
use App\Http\Controllers\Common\DataBaseConnection;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Utils\FileUtil;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use PDO;
use Config;
use App\Models\Mongs\Task;
use App\Models\Mongs\Databaseconns;
use App\Models\Kget\OptionalFeatureLicense;
use App\Models\SCHEMATA;
use MongoClient;
use MongoId;
use MongoRegex;

/**
 * 参数查询
 * Class KgetG2Controller
 *
 * @category ParameterAnalysis
 * @package  App\Http\Controllers\ParameterAnalysis
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class KgetG2Controller extends Controller
{
    /**
     * 获取日期（数据库名称）
     *
     * @return string
     */
    public function getParamTasks()
    {
        $dbn = new DataBaseConnection();
        $m = $dbn->getMongoDB("mongoDB_kget");
        $db = $m->kgetParser;
        $collections = $db->getCollectionNames();
        $items       = array();
        foreach ($collections as $collection) {
            if (preg_match("/^kget\d{6}$/", $collection) == 1) {
                array_push($items, ["id" => $collection, "text" => $collection]);
            }
        }
        rsort($items);
        return response()->json($items);
    }
    /**
     * 获得子网集合
     *
     * @param mixed  $db   数据库连接句柄
     * @param string $city 城市名
     *
     * @return string
     */
    public function getSubNets($db, $city)
    {
        $row = Databaseconns::where('cityChinese', $city)->first()->toArray();
        $subNets    = $row['subNetwork'];
        $subNetArr  = explode(",", $subNets);
        $subNetsStr = '';
        foreach ($subNetArr as $subNet) {
            $subNetsStr .= "'".$subNet."',";
        }

        return $subNetsStr;

    }//end getSubNets()
    /**
     * 获得子网集合
     *
     * @return mixed
     */
    public function getFormatAllSubNetwork()
    {
        $citys  = Input::get('citys');
        $format = Input::get('format');
        $items  = array();
        foreach ($citys as $city) {
            $databaseConns = DB::table('databaseconn')->where('cityChinese', '=', $city)->get();
            foreach ($databaseConns as $databaseConn) {
                if ($format == 'TDD') {
                    $subStr = $databaseConn->subNetwork;
                } else if ($format == 'FDD') {
                    $subStr = $databaseConn->subNetworkFdd;
                }

                $subArr = explode(',', $subStr);
                foreach ($subArr as $sub) {
                    $city = '{"text":"'.$sub.'","value":"'.$sub.'"}';
                    array_push($items, $city);
                }
            }
        }

        return response()->json($items);

    }//end getFormatAllSubNetwork()

    /**
     * 获得子网集合
     *
     * @return mixed
     */
    public function getAllSubNetwork()
    {
        $citys  = Input::get('citys');
        $format = Input::get('format');
        $items  = array();
        if ($citys!= '') {
        foreach ($citys as $city) {
            $subNetworkFdd = DB::table('databaseconn')->select('subNetworkFdd')->where('cityChinese', '=', $city);
            $databaseConns = DB::table('databaseconn')->select('subNetwork')->where('cityChinese', '=', $city)->union($subNetworkFdd)->get();
            //$databaseConns = DB::table('databaseconn')->select('subNetwork')->union('subNetwork_Fdd')->where('cityChinese', '=', $city)->get();
            $tempArr       = array();
            foreach ($databaseConns as $databaseConn) {
                $subStr = $databaseConn->subNetwork;
                $subArr = explode(',', $subStr);
                foreach ($subArr as $sub) {
                    if (!array_search($sub, $tempArr)) {
                        array_push($tempArr, $sub);
                        $city = '{"text":"'.$sub.'","value":"'.$sub.'"}';
                        array_push($items, $city);
                    }
                }
            }
        }
   }
        return response()->json($items);

    }//end getAllSubNetwork()
    /**
     * 获取城市列表
     *
     * @return string
     */
    public function getParamCitys()
    {
        $dbc = new DataBaseConnection();
        return $dbc->getCityOptions();
    }
    /**
     * 获取OptionalFeatureLicense中OptionalFeatureLicenseId列表
     *
     * @return string
     */
    public function getFeatureList()
    {
        $dbname = Input::get('db');
        $dbname = $this->check_input($dbname);
        Config::set("database.connections.kget.database", $dbname);
        $features = OptionalFeatureLicense::distinct('OptionalFeatureLicenseId')->get(['OptionalFeatureLicenseId']);
        $items         = array();
        foreach ($features as $feature) {
            $featureOption = '{"text":"'.$feature->OptionalFeatureLicenseId.'","value":"'.$feature->OptionalFeatureLicenseId.'"}';
            array_push($items, $featureOption);
        }
        return response()->json($items);
    }// end of getFeatureList()

    function check_input($value)
    {
        //去除斜杠
        if (get_magic_quotes_gpc()) {
            $value=stripslashes($value);
        }
        return $value;
    }

    /**
     * 导出公共类
     *
     * @param array  $result   查询结果
     * @param string $filename CSV文件名
     *
     * @return void
     */
    protected function resultToCSV2($result, $filename)
    {
        $csvContent = mb_convert_encoding($result['text'] . "\n", 'GBK');
        $fp = fopen($filename, "w");
        fwrite($fp, $csvContent);
        foreach ($result['rows'] as $row) {
            fputcsv($fp, $row);
        }
        fclose($fp);
    }

    /**
     * 获取参数查询记录
     *
     * @return string
     */
    public function getParamItems_mongodb()
    {
        $dbname = Input::get('db');
        $table = Input::get('table');
        $citys = Input::get('citys');
        $erbs = Input::get('erbs');
        $subNets = Input::get('subNet');//自己选择的子网
        $featureList = Input::get('featureList');

        $displayStart = Input::get('pageNumber');
        $displayLength = Input::get('pageSize');
        $offset = ($displayStart - 1) * $displayLength;
        
        $dbc = new DataBaseConnection();
        $m = $dbc->getMongoDB("mongoDB_kget");
        $db = $m->kgetParser;
        $collection = $db->$dbname;
        $query = array('table'=>$table);
        
        $orderFilter = " ";
        $subNetworkIsNull = '';
        //获取数据开始
        //指定城市所有子网
        $subNetwork = '';
        //指定城市传入的部分子网数据
        // $subNetworks = '';
        if (is_array($citys) && count($citys) > 0 /* && $citys[0] != 'unknow' */) {
            foreach ($citys as $city) {
                if ($city == 'unknow') {
                    $subNetworkIsNull = 'null';
                } else {
                    $subNetwork .= $dbc->getSubNets($city) . ',';
                }
            }
            $subNetwork = substr($subNetwork, 0, -1);
            $subNetwork = str_replace("'", "", $subNetwork);
            $subNetworkArr = explode(",", $subNetwork);//选择城市的子网数组
        }
        $query1 = [];
        if (trim($erbs) != '') {
            $erbsArr = explode(",", $erbs);
            if ($subNetwork != '') {
                if ($subNetworkIsNull == 'null') {
                    $query1 = array('MeContext'=>array('$in'=>$erbsArr), '$or'=>array(array('SubNetwork'=>'null'), array('SubNetwork'=>NULL), array('SubNetwork'=>''), array('SubNetwork'=>array('$in'=>$subNetworkArr))));
                } else {
                    $query1 = array('MeContext'=>array('$in'=>$erbsArr), 'SubNetwork'=>array('$in'=>$subNetworkArr));
                }
            } else {
                if ($subNetworkIsNull == 'null') {
                    $query1 = array('MeContext'=>array('$in'=>$erbsArr), '$or'=>array(array('SubNetwork'=>'null'), array('SubNetwork'=>NULL), array('SubNetwork'=>'')));
                } else {
                    $query1 = array('MeContext'=>array('$in'=>$erbsArr));
                }
            }
        } else {
            if ($subNetworkIsNull == 'null') {
                $query1 = array('$or'=>array(array('SubNetwork'=>'null'), array('SubNetwork'=>NULL), array('SubNetwork'=>''), array('SubNetwork'=>array('$in'=>$subNetworkArr))));
            }
            
        }
        $query = array_merge($query, $query1);
        if (is_array($subNets) && count($subNets)>0) {
            $query2 = array('SubNetwork'=>array('$in'=>$subNets));
            $query = array_merge($query, $query2);
        }
        if ($table == 'ManagedElement_SystemFunctions_Licensing_OptionalFeatureLicense' && count($featureList)>0) {
            $query3 = array('OptionalFeatureLicenseId'=>array('$in'=>$featureList));
            $query = array_merge($query, $query3);
        }
        $result = array();
        $collection = $collection->find($query);
        $result["total"] = $collection->count();
        $cursor = $collection->skip($offset)->limit($displayLength);
        $items = array();
        foreach ($cursor as $document) {
            array_push($items, $document);
        }
        $result['rows'] = $items;
        return json_encode($result);
    }

    /* 
    mongodb导出到csv
    */
    public function exportParamFile_mongodb()
    {
        $dbname = Input::get('db');
        $table = Input::get('table');
        $fields = implode(",", Input::get('fields'));
        $citys = Input::get('citys');
        $erbs = Input::get('erbs');
        $subNets = Input::get('subNet');//自己选择的子网
        $featureList = Input::get('featureList');

        // $displayStart = Input::get('pageNumber');
        // $displayLength = Input::get('pageSize');
        // $offset = ($displayStart - 1) * $displayLength;
        
        $dbc = new DataBaseConnection();
        $query = array('table'=>$table);

        $orderFilter = " ";
        $subNetworkIsNull = '';
        //获取数据开始
        //指定城市所有子网
        $subNetwork = '';
        //指定城市传入的部分子网数据
        // $subNetworks = '';
        if (is_array($citys) && count($citys) > 0 /* && $citys[0] != 'unknow' */) {
            foreach ($citys as $city) {
                if ($city == 'unknow') {
                    $subNetworkIsNull = 'null';
                } else {
                    $subNetwork .= $dbc->getSubNets($city) . ',';
                }
            }
            $subNetwork = substr($subNetwork, 0, -1);
            $subNetwork = str_replace("'", "", $subNetwork);
            $subNetworkArr = explode(",", $subNetwork);//选择城市的子网数组
        }
        $query1 = [];
        if (trim($erbs) != '') {
            $erbsArr = explode(",", $erbs);
            if ($subNetwork != '') {
                if ($subNetworkIsNull == 'null') {
                    $query1 = array('MeContext'=>array('$in'=>$erbsArr), '$or'=>array(array('SubNetwork'=>'null'), array('SubNetwork'=>NULL), array('SubNetwork'=>''), array('SubNetwork'=>array('$in'=>$subNetworkArr))));
                } else {
                    $query1 = array('MeContext'=>array('$in'=>$erbsArr), 'SubNetwork'=>array('$in'=>$subNetworkArr));
                }
            } else {
                if ($subNetworkIsNull == 'null') {
                    $query1 = array('MeContext'=>array('$in'=>$erbsArr), '$or'=>array(array('SubNetwork'=>'null'), array('SubNetwork'=>NULL), array('SubNetwork'=>'')));
                } else {
                    $query1 = array('MeContext'=>array('$in'=>$erbsArr));
                }
            }
        } else {
            if ($subNetworkIsNull == 'null') {
                $query1 = array('$or'=>array(array('SubNetwork'=>'null'), array('SubNetwork'=>NULL), array('SubNetwork'=>''), array('SubNetwork'=>array('$in'=>$subNetworkArr))));
            }
            
        }
        $query = array_merge($query, $query1);
        if (is_array($subNets) && count($subNets)>0) {
            $query2 = array('SubNetwork'=>array('$in'=>$subNets));
            $query = array_merge($query, $query2);
        }
        if ($table == 'ManagedElement_SystemFunctions_Licensing_OptionalFeatureLicense' && count($featureList)>0) {
            $query3 = array('OptionalFeatureLicenseId'=>array('$in'=>$featureList));
            $query = array_merge($query, $query3);
        }
        $result = array();

        $config = Config::get("database.connections.mongoDB_kget");
        $host = $config['host'];
        $port = $config['port'];

        $fileName = "files/" . $dbname . "_" . $table . "_" . date('YmdHis') . ".csv";
        $queryJson = json_encode($query);
        //-f 部分还要加上对应字段
        $command = "sudo mongoexport -h$host:$port -d kgetParser -c $dbname -f $fields -q '$queryJson' --csv -o $fileName";
        exec($command);
        $result["fileName"] = $fileName;
        return json_encode($result);
    }

}
