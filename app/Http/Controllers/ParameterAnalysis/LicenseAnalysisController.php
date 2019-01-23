<?php

/**
 * LicenseAnalysisController.php
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
use App\Models\Kget\LicenseAnalysis;
use App\Models\SCHEMATA;

/**
 * 参数查询
 * Class LicenseAnalysisController
 *
 * @category ParameterAnalysis
 * @package  App\Http\Controllers\ParameterAnalysis
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class LicenseAnalysisController extends Controller
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
        if (count($items) > 0) {
            $city = '{"text":"NULL","value":"NULL"}';
            array_push($items, $city);
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
     * 获取LicenseName列表
     *
     * @return string
     */
    public function getLicenseNameList(){
        $dbname = Input::get('db');
        $dbname = $this->check_input($dbname);
        Config::set("database.connections.kget.database", $dbname);
        $licenseNames = LicenseAnalysis::distinct('LicenseName')->whereRaw( " LicenseName is not null ")->get(['LicenseName']);
        $items         = array();
        foreach ($licenseNames as $licenseName) {
            $licenseNameOption = '{"text":"'.$licenseName->LicenseName.'","value":"'.$licenseName->LicenseName.'"}';
            array_push($items, $licenseNameOption);
        }
        return response()->json($items);
    }// end of getLicenseNameList()
    /**
     * 获取LicenseId列表
     *
     * @return string
     */
    public function getLicenseIdList(){
        $dbname = Input::get('db');
        $dbname = $this->check_input($dbname);
        Config::set("database.connections.kget.database", $dbname);
        $licenseIds = LicenseAnalysis::distinct('LicenseId')->whereRaw( " LicenseId is not null ")->get(['LicenseId']);
        $items         = array();
        foreach ($licenseIds as $licenseId) {
            $licenseIdOption = '{"text":"'.$licenseId->LicenseId.'","value":"'.$licenseId->LicenseId.'"}';
            array_push($items, $licenseIdOption);
        }
        return response()->json($items);
    }// end of getLicenseIdList()

    /**
     * 获取状态state列表
     *
     * @return string
     */
    public function getStateList(){
        $dbname = Input::get('db');
        $dbname = $this->check_input($dbname);
        Config::set("database.connections.kget.database", $dbname);
        $comments = LicenseAnalysis::distinct('Comment')->whereRaw( " Comment is not null ")->get(['Comment']);
        $items         = array();
        foreach ($comments as $comment) {
            $commentOption = '{"text":"'.$comment->Comment.'","value":"'.$comment->Comment.'"}';
            array_push($items, $commentOption);
        }
        return response()->json($items);
    }// end of getStateList()

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
    public function getTableField()
    {
        $dbname = Input::get('db');
        $table = "LicenseAnalysis";
        $dbc = new DataBaseConnection();
        $dbn = $dbc->getDB('mongs', $dbname);
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
    }// end of getTableField

    /**
     * 获取参数查询记录
     *
     * @return string
     */
    public function getItems()
    {
        $table = "LicenseAnalysis";
        $dbname = Input::get('db');
        $citys = Input::get('citys');
        $erbs = Input::get('erbs');
        $subNets = Input::get('subNet');
        $licenseNameList = Input::get('licenseNameList');
        $licenseIdList = Input::get('licenseIdList');
        $stateList = Input::get('stateList');

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
                if ($city != 'unknow') {
                    $city = $dbc->getCityByCityChinese($city)[0]->connName;
                    $cityStr = $cityStr."'".$city."',";
                }
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
        $licenseNameListStr = "";
        if ($licenseNameList) {
            $licenseNameListStr = "'".implode("','", $licenseNameList)."'";
            if ($filter) {
                $filter = $filter." and LicenseName in ($licenseNameListStr) ";
            } else {
                $filter = $filter." where LicenseName in ($licenseNameListStr) ";
            }
        }

        $licenseIdListStr = "";
        if ($licenseIdList) {
            $licenseIdListStr = "'".implode("','", $licenseIdList)."'";
            if ($filter) {
                $filter = $filter." and LicenseId in ($licenseIdListStr) ";
            } else {
                $filter = $filter." where LicenseId in ($licenseIdListStr) ";
            }
        }

        $stateListStr = "";
        if ($stateList) {
            $stateListStr = "'".implode("','", $stateList)."'";
            if ($filter) {
                $filter = $filter." and Comment in ($stateListStr) ";
            } else {
                $filter = $filter." where Comment in ($stateListStr) ";
            }
        }

        $result = array();
        $sql = "select count(*) totalCount from " . $table . $filter;
        //print_r($sql);
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
    }// end of getItems
    /**
     * 文件导出
     *
     * @return array
     */
    public function exportFile()
    {
        $table = "LicenseAnalysis";
        $dbname = Input::get('db');
        $citys = Input::get('citys');
        $erbs = Input::get('erbs');
        $subNets = Input::get('subNet');
        $licenseNameList = Input::get('licenseNameList');
        $licenseIdList = Input::get('licenseIdList');
        $stateList = Input::get('stateList');

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
        $licenseNameListStr = "";
        if ($licenseNameList) {
            $licenseNameListStr = "'".implode("','", $licenseNameList)."'";
            if ($filter) {
                $filter = $filter." and LicenseName in ($licenseNameListStr) ";
            } else {
                $filter = $filter." where LicenseName in ($licenseNameListStr) ";
            }
        }

        $licenseIdListStr = "";
        if ($licenseIdList) {
            $licenseIdListStr = "'".implode("','", $licenseIdList)."'";
            if ($filter) {
                $filter = $filter." and LicenseId in ($licenseIdListStr) ";
            } else {
                $filter = $filter." where LicenseId in ($licenseIdListStr) ";
            }
        }

        $stateListStr = "";
        if ($stateList) {
            $stateListStr = "'".implode("','", $stateList)."'";
            if ($filter) {
                $filter = $filter." and Comment in ($stateListStr) ";
            } else {
                $filter = $filter." where Comment in ($stateListStr) ";
            }
        }
        $sql = "select * from " . $table . " limit 1";
        $title = "";
        $stmt = $dbn->prepare($sql);
        if ($stmt->execute()) {
            $rs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $title = "select "."'".implode("','", array_keys($rs[0]))."' union all ";
            
        }
        $filePath = dirname(dirname(dirname(dirname(dirname(__FILE__)))))."/public/";
        $fileName = "files/" . $dbname . "_" . $table . "_" . date('YmdHis') . ".csv";
        $sql = $title."select * from $table" . $filter." INTO OUTFILE '".$filePath.$fileName."'
                CHARACTER SET gbk
                FIELDS TERMINATED BY ',' 
                OPTIONALLY ENCLOSED BY '\"' 
                LINES TERMINATED BY '\\n'";
        $stmt = $dbn->prepare($sql);
        if ($stmt->execute()) {
            $result['fileName'] = $fileName;
            $result['result'] = true;
        } else {
            $result['result'] = false;
        }
        return $result;
    }
}
