<?php

/**
 * ParamQueryController.php
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

/**
 * 参数查询
 * Class ParamQueryController
 *
 * @category ParameterAnalysis
 * @package  App\Http\Controllers\ParameterAnalysis
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class ParamQueryController extends Controller
{
    /**
     * 获取日期（数据库名称）
     *
     * @return string
     */
    public function getParamTasks()
    {
         $tasks = SCHEMATA::select('SCHEMA_NAME')->where('SCHEMA_NAME', 'like', 'kget%')->where('SCHEMA_NAME', 'not like', 'kgetpart%')->where('SCHEMA_NAME', 'not like', 'kget_External%')->orderBy('SCHEMA_NAME', 'desc')->get()->toArray();
        $items = array();
        foreach ($tasks as $task) {
            $items[] = array("text"=>$task['SCHEMA_NAME'],"id"=>$task['SCHEMA_NAME']);
        }
        return response()->json($items);//需要通过response返回响应数据
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
            $subNetworkNbiot = DB::table('databaseconn')->select('subNetworkNbiot')->where('cityChinese', '=', $city);
            $databaseConns = DB::table('databaseconn')->select('subNetwork')->where('cityChinese', '=', $city)->union($subNetworkFdd)->union($subNetworkNbiot)->get();
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
   $items = array_values(array_unique($items));
        $city = '{"text":"NULL","value":"NULL"}';
        array_push($items, $city);
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
     * 获取参数查询表头
     *
     * @return array
     */
    public function getParamTableField()
    {
        $dbname = Input::get('db');
        $table = Input::get('table');
        if (strpos($table, "_FDD") !== false) {
            $table = str_replace("_FDD", "_2", $table);
        }
        $dbc = new DataBaseConnection();
        $dbn = $dbc->getDB('kget', $dbname);
        $sql = "SELECT count(*) FROM information_schema.`TABLES` where TABLE_SCHEMA='$dbname' and TABLE_NAME='$table';";
        $stmt = $dbn->prepare($sql);
        if ($stmt->execute()) {
            $count = $stmt->fetchColumn();
            if ($count == 0) {
                return '';
            }
        }
        $sql = "select * from " . $table . " limit 1";
        $stmt = $dbn->prepare($sql);
        if ($stmt->execute()) {
            $rs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (count($rs) > 0) {
                return $rs[0];
            }
        }
        return '';
    }

    /**
     * 获取参数查询记录
     *
     * @return string
     */
    public function getParamItems()
    {
        $dbname = Input::get('db');
        $table = Input::get('table');
        if (strpos($table, "_FDD") !== false) {
            $table = str_replace("_FDD", "_2", $table);
        }
        $citys = Input::get('citys');
        $erbs = Input::get('erbs');
        $subNets = Input::get('subNet');
        $featureList = Input::get('featureList');
        $featureListStr = "";
        if ($featureList) {
            foreach ($featureList as $feature) {

                $featureListStr = $featureListStr."'".$feature."',";
            }
            $featureListStr = "(".substr($featureListStr, 0, -1).")";
        }

        $dbc = new DataBaseConnection();
        $dbn = $dbc->getDB('kget', $dbname);

        $displayStart = Input::get('page');
        $displayLength = Input::get('limit');
        $offset = ($displayStart - 1) * $displayLength;
        $orderFilter = " ";
        $limit = " limit $offset,$displayLength ";
        $filter = '';
        //获取数据开始
        $cityStr = "";
        if ($citys != '' and $citys[0]!= 'unknow') {
            foreach ($citys as $city) {
                $city = $dbc->getCityByCityChinese($city)[0]->connName;
                $cityStr = $cityStr."'".$city."',";
            }
            $cityStr = subStr($cityStr, 0, strlen($cityStr) - 1);
            $filter = " where city in ($cityStr) ";
        }
        $subNetworkStr = "";
        if ($subNets != '') {
            $subNetworkStr = "'".implode("','",$subNets)."'";
            if ($filter) {
               $filter = $filter." and subNetwork in ($subNetworkStr) ";
            } else {
                $filter = $filter." where subNetwork in ($subNetworkStr) ";
            }
        }
        $erbsStr = "";
        if (trim($erbs) != '') {
            $erbsStr = "'".str_replace(",", "','", $erbs)."'";
            if ($filter) {
               $filter = $filter." and meContext in ($erbsStr) ";
            } else {
                $filter = $filter." where meContext in ($erbsStr) ";
            }
        }
        if ($table == 'OptionalFeatureLicense' && $featureListStr) {
            if ($filter) {
               $filter = $filter." and OptionalFeatureLicenseId in $featureListStr";
            } else {
                $filter = $filter." where OptionalFeatureLicenseId in $featureListStr";
            }
        }
        if ($table == 'OptionalFeatureLicense' && $featureListStr) {
            if ($filter) {
               $filter = $filter." and OptionalFeatureLicenseId in $featureListStr";
            } else {
                $filter = $filter." where OptionalFeatureLicenseId in $featureListStr";
            }
        }
        $result = array();
        $sql = "select count(*) totalCount from " . $table . $filter;
        $stmt = $dbn->prepare($sql);
        if ($stmt->execute()) {
            $result["total"] = $stmt->fetchColumn();
        }
        $sql = "select * from " . $table . $filter . $orderFilter . $limit;
        $stmt = $dbn->prepare($sql);
        $items = array();
        if ($stmt->execute()) {
            $rows = $stmt->fetchAll(PDO::FETCH_OBJ);
            $result["records"] = $rows;
        }
        return json_encode($result);
    }
    /**
     * 文件导出
     *
     * @return array
     */
    public function exportParamFile()
    {
        $dbname = Input::get('db');
        $table = Input::get('table');
        $fileName = "files/" . $dbname . "_" . $table . "_" . date('YmdHis') . ".csv";
        if (strpos($table, "_FDD") !== false) {
            $table = str_replace("_FDD", "_2", $table);
        }
        $citys = Input::get('citys');
        $erbs = Input::get('erbs');
        $subNets = Input::get('subNet');
        $featureList = Input::get('featureList');
        $featureListStr = "";
        if ($featureList) {
            foreach ($featureList as $feature) {

                $featureListStr = $featureListStr."'".$feature."',";
            }
            $featureListStr = "(".substr($featureListStr, 0, -1).")";
        }
        $fieldsArr = Input::get('fields');
        $fields = implode("`,`", $fieldsArr);

        $dbc = new DataBaseConnection();
        $dbn = $dbc->getDB('mongs', $dbname);

        $displayStart = Input::get('page');
        $displayLength = Input::get('limit');
        $offset = ($displayStart - 1) * $displayLength;
        $orderFilter = " ";
        $limit = " limit $offset,$displayLength ";
        $filter = '';
        //获取数据开始
        $cityStr = "";
        if ($citys != '' and $citys[0]!= 'unknow') {
            foreach ($citys as $city) {
                $city = $dbc->getCityByCityChinese($city)[0]->connName;
                $cityStr = $cityStr."'".$city."',";
            }
            $cityStr = subStr($cityStr, 0, strlen($cityStr) - 1);
            $filter = " where city in ($cityStr) ";
        }
        $subNetworkStr = "";
        if ($subNets != '') {
            $subNetworkStr = "'".implode("','",$subNets)."'";
            if ($filter) {
               $filter = $filter." and subNetwork in ($subNetworkStr) ";
            } else {
                $filter = $filter." where subNetwork in ($subNetworkStr) ";
            }
        }
        $erbsStr = "";
        if (trim($erbs) != '') {
            $erbsStr = "'".str_replace(",", "','", $erbs)."'";
            if ($filter) {
               $filter = $filter." and meContext in ($erbsStr) ";
            } else {
                $filter = $filter." where meContext in ($erbsStr) ";
            }
        }
        if ($table == 'OptionalFeatureLicense' && $featureListStr) {
            if ($filter) {
               $filter = $filter." and OptionalFeatureLicenseId in $featureListStr";
            } else {
                $filter = $filter." where OptionalFeatureLicenseId in $featureListStr";
            }
        }
        if ($table == 'OptionalFeatureLicense' && $featureListStr) {
            if ($filter) {
               $filter = $filter." and OptionalFeatureLicenseId in $featureListStr";
            } else {
                $filter = $filter." where OptionalFeatureLicenseId in $featureListStr";
            }
        }
        //$sql = "select * from $table" . $filter;
        // $sql = "select * from " . $table . " limit 1";
        // $title = "";
        // $stmt = $dbn->prepare($sql);
        // if ($stmt->execute()) {
        //     $rs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //     $title = "select "."'".implode("','", array_keys($rs[0]))."' union all ";
            
        // }
        $title = "select "."'".implode("','", $fieldsArr)."' union all ";
        $filePath = dirname(dirname(dirname(dirname(dirname(__FILE__)))))."/public/";
        $sql = $title."select `$fields` from ".$dbname.".$table" . $filter." INTO OUTFILE '".$filePath.$fileName."'
                FIELDS TERMINATED BY ',' 
                OPTIONALLY ENCLOSED BY '\"' 
                LINES TERMINATED BY '\\n'";
        $stmt = $dbn->prepare($sql);
        if ($stmt->execute()) {
           /* $fileUtil = new FileUtil();
            $totalCount = $fileUtil->resultToCSV($stmt, $fileName);*/
            $result['fileName'] = $fileName;
            $result['result'] = true;
        } else {
            $result['result'] = false;
        }
        return $result;
    }

    /**
     * 参数检索
     *
     * @return string
     */
    public function getParamData()
    {
        $data = input::get("pattern");
        $moData = input::get("moData");
        $moDataTemp = array();
        $items = array();
        if ($moData) {
            foreach ($moData as $row) {
                array_push($moDataTemp, $row['text']);
            }
        }
        $handler = fopen("common/txt/paramDistribution.txt", "r") or die("Unable to open file!");
        $m = [];
        $params = [];
        while (!feof($handler)) {
            $m[] = fgets($handler, 4096);
        }
        array_pop($m);
        foreach ($m as $val) {
            $val = str_replace("\r\n", "", $val);
            $arr = explode('=', trim($val));
            $params[][$arr[0]] = $arr[1];

        }
        foreach ($params as $values) {
            foreach ($values as $key => $value) {
                if (strpos(strtolower($value), strtolower($data)) !== false) {
                    if (strpos($key, "_2") !== false) {
                        $key = str_replace("_2", "_FDD", $key);
                    }
                    array_push($moDataTemp, $key);
                }
            }
        }
        foreach (array_unique($moDataTemp) as $row) {
            $array = array();
            $array['TABLE_NAME'] = $row;
            array_push($items, $array);
        }
        echo json_encode($items);
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
}
