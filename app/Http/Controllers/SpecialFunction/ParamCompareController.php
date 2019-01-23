<?php

/**
 * ParamCompareController.php
 *
 * @category SpecialFunction
 * @package  App\Http\Controllers\SpecialFunction
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
namespace App\Http\Controllers\SpecialFunction;

use App\DatabaseConn;
use App\Http\Controllers\Common\DataBaseConnection;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Utils\FileUtil;
use App\Task;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use PDO;
use App\Models\SCHEMATA;


/**
 * 参数对比
 * Class ParamCompareController
 *
 * @category SpecialFunction
 * @package  App\Http\Controllers\SpecialFunction
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class ParamCompareController extends Controller
{
     /**
     * 获得城市列表
     *
     * @return string
     */
    public function getAllCity()
    {
        $cityClass = new DataBaseConnection();
        return $cityClass->getCityOptions();
    }
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
            array_push($items, '{"text":"' . $task['SCHEMA_NAME'] . '"}');
        }
        return response()->json($items);//需要通过response返回响应数据
    }

    /**
     * 获取对比差异结果
     *
     * @return void
     */
    public function getItems()
    {
        $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $rows = isset($_REQUEST['rows']) ? intval($_REQUEST['rows']) : 10;
        $offset = ($page - 1) * $rows;
        $limit = " limit $offset,$rows";
        $basedb = Input::get('basedb');
        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('kget', $basedb);
        $table = "TempParamCompareResult";
        $filter = "";
        $totalMOs = $db->query("select distinct MO from $table a where (`条件` not like '%&null' and `条件` not like 'null&%') and `参数名` not in 
(select parameterName from mongs.parameterCompareWhiteList b where b.MO=a.MO)");
        $items = array(); //存储最终结果
        //print_r($totalMOs);
        if ($totalMOs) {
            $totalMOsArr = $totalMOs->fetchAll(PDO::FETCH_ASSOC);
            $data['total'] = count($totalMOsArr);
            $rsMOs = $db->query("select distinct MO from $table a where (`条件` not like '%&null' and `条件` not like 'null&%') and `参数名` not in 
(select parameterName from mongs.parameterCompareWhiteList b where b.MO=a.MO) $limit"); //筛选所有user
            if ($rsMOs) {
                $rsMOsArr = $rsMOs->fetchAll(PDO::FETCH_ASSOC);
                $idNum = 1;
                foreach ($rsMOsArr as $row) {
                    $result = array(); //存储用户结果
                    $result['id'] = $idNum++;
                    $result['MO'] = $row['MO'];
                    $result['state'] = 'closed';
                    $mo = $row['MO'];
                    $children = array();
                    $sql = "select * from $table where MO='$mo' and (`条件` not like '%&null' and `条件` not like 'null&%') and  `参数名` not in (select parameterName from mongs.parameterCompareWhiteList where MO='$mo' )";
                    $res = $db->query($sql);
                    $resArr = $res->fetchAll(PDO::FETCH_ASSOC);
                    if ($resArr) {
                        foreach ($resArr as $rs) {
                            $conditionArr = explode("&", $rs['条件']);
                            $baseId = $conditionArr[0];
                            $compareId = $conditionArr[1];
                            $param = $rs['参数名'];
                            $baseValue = $rs['基础值'];
                            $compareValue = $rs['对比值'];
                            array_push($children, array("id" => $idNum++, "MO" => '', "baseId" => $baseId, "compareId" => $compareId, "参数名" => $param, "基础值" => $baseValue, "对比值" => $compareValue));
                            $result['children'] = $children;
                        }
                    }
                    array_push($items, $result);
                }
            }
        }
        $data['rows'] = $items;
        //print_r(count($rsMOsArr));
        echo json_encode($data);
    }
    /**
     * 获取对比差异结果
     *
     * @return void
     */
    public function getItemsAdd()
    {
        $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $rows = isset($_REQUEST['rows']) ? intval($_REQUEST['rows']) : 10;
        $offset = ($page - 1) * $rows;
        $limit = " limit $offset,$rows";
        $basedb = Input::get('basedb');
        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('kget', $basedb);
        $table = "TempParamCompareResult";
        $filter = "";
        $totalMOs = $db->query("select distinct MO from $table a where `条件` like '%&null' and `参数名` not in 
(select parameterName from mongs.parameterCompareWhiteList b where b.MO=a.MO)");
        $items = array(); //存储最终结果
        //print_r($totalMOs);
        if ($totalMOs) {
            $totalMOsArr = $totalMOs->fetchAll(PDO::FETCH_ASSOC);
            $data['total'] = count($totalMOsArr);
            $rsMOs = $db->query("select distinct MO from $table a where  `条件` like '%&null' and `参数名` not in 
(select parameterName from mongs.parameterCompareWhiteList b where b.MO=a.MO) $limit"); //筛选所有user
            if ($rsMOs) {
                $rsMOsArr = $rsMOs->fetchAll(PDO::FETCH_ASSOC);
                $idNum = 1;
                foreach ($rsMOsArr as $row) {
                    $result = array(); //存储用户结果
                    $result['id'] = $idNum++;
                    $result['MO'] = $row['MO'];
                    $result['state'] = 'closed';
                    $mo = $row['MO'];
                    $children = array();
                    $sql = "select * from $table where MO='$mo' and `条件` like '%&null' and `参数名` not in (select parameterName from mongs.parameterCompareWhiteList where MO='$mo' )";
                    $res = $db->query($sql);
                    $resArr = $res->fetchAll(PDO::FETCH_ASSOC);
                    if ($resArr) {
                        foreach ($resArr as $rs) {
                            $conditionArr = explode("&", $rs['条件']);
                            $baseId = $conditionArr[0];
                            $compareId = $conditionArr[1];
                            $param = $rs['参数名'];
                            $baseValue = $rs['基础值'];
                            $compareValue = $rs['对比值'];
                            array_push($children, array("id" => $idNum++, "MO" => '', "baseId" => $baseId, "compareId" => $compareId, "参数名" => $param, "基础值" => $baseValue, "对比值" => $compareValue));
                            $result['children'] = $children;
                        }
                    }
                    array_push($items, $result);
                }
            }
        }
        $data['rows'] = $items;
        //print_r(count($rsMOsArr));
        echo json_encode($data);
    }
    /**
     * 获取对比差异结果
     *
     * @return void
     */
    public function getItemsLess()
    {
        $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $rows = isset($_REQUEST['rows']) ? intval($_REQUEST['rows']) : 10;
        $offset = ($page - 1) * $rows;
        $limit = " limit $offset,$rows";
        $basedb = Input::get('basedb');
        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('kget', $basedb);
        $table = "TempParamCompareResult";
        $filter = "";
        $totalMOs = $db->query("select distinct MO from $table a where `条件` like 'null&%' and `参数名` not in 
(select parameterName from mongs.parameterCompareWhiteList b where b.MO=a.MO)");
        $items = array(); //存储最终结果
        //print_r($totalMOs);
        if ($totalMOs) {
            $totalMOsArr = $totalMOs->fetchAll(PDO::FETCH_ASSOC);
            $data['total'] = count($totalMOsArr);
            $rsMOs = $db->query("select distinct MO from $table a where `条件` like 'null&%' and `参数名` not in 
(select parameterName from mongs.parameterCompareWhiteList b where b.MO=a.MO) $limit"); //筛选所有user
            if ($rsMOs) {
                $rsMOsArr = $rsMOs->fetchAll(PDO::FETCH_ASSOC);
                $idNum = 1;
                foreach ($rsMOsArr as $row) {
                    $result = array(); //存储用户结果
                    $result['id'] = $idNum++;
                    $result['MO'] = $row['MO'];
                    $result['state'] = 'closed';
                    $mo = $row['MO'];
                    $children = array();
                    $sql = "select * from $table where MO='$mo' and `条件` like 'null&%' and `参数名` not in (select parameterName from mongs.parameterCompareWhiteList where MO='$mo' )";
                    $res = $db->query($sql);
                    $resArr = $res->fetchAll(PDO::FETCH_ASSOC);
                    if ($resArr) {
                        foreach ($resArr as $rs) {
                            $conditionArr = explode("&", $rs['条件']);
                            $baseId = $conditionArr[0];
                            $compareId = $conditionArr[1];
                            $param = $rs['参数名'];
                            $baseValue = $rs['基础值'];
                            $compareValue = $rs['对比值'];
                            array_push($children, array("id" => $idNum++, "MO" => '', "baseId" => $baseId, "compareId" => $compareId, "参数名" => $param, "基础值" => $baseValue, "对比值" => $compareValue));
                            $result['children'] = $children;
                        }
                    }
                    array_push($items, $result);
                }
            }
        }
        $data['rows'] = $items;
        //print_r(count($rsMOsArr));
        echo json_encode($data);
    }
    /**
     * 导出文件
     *
     * @return mixed 导出结果
     */
    public function exportFile()
    {
        $basedb = Input::get('basedb');
        $comparedb = Input::get('comparedb');
        $base = Input::get('base');
        $compare = Input::get('compare');
        $tabType = Input::get("tabType");
        $dbc = new DataBaseConnection();

        $db = $dbc->getDB('kget', $basedb);
        $filter = '';
        if ($tabType == '基础') {
            $filter = " where `条件` not like '%&null' and `条件` not like 'null&%'";
        } else if ($tabType == '新增') {
            $filter = " where `条件` like '%&null'";
        } else if ($tabType == '缺失') {
            $filter = " where `条件` like 'null&%'";
        }
        $table = "TempParamCompareResult";
        $result['text'] = "MO,参数名,基础值,对比值,条件";
        $fileName = "files/" . $basedb . "-" . $comparedb . "_" . $base . "-" . $compare . "_" . date('YmdHis') . ".csv";
        $column = "MO,参数名,基础值,对比值,条件";
        $sql = "select MO,`参数名`,`基础值`,`对比值`,`条件` from " . $table . $filter;
        $res = $db->query($sql);
        if ($res) {
            $items = $res->fetchAll(PDO::FETCH_ASSOC);
            if (count($items) > 0) {
                $fileUtil = new FileUtil();
                $fileUtil->resultToCSV2($column, $items, $fileName);
                $result['fileName'] = $fileName;
                $result['result'] = true;
            } else {
                $result['result'] = false;
            }
        } else {
            $result['result'] = false;
        }
        return $result;
    }

    /**
     * 参数检查
     *
     * @return void
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
     * 参数对比
     *
     * @return string
     */
    public function getCompareResult()
    {
        $dbc = new DataBaseConnection();
        $basedb = Input::get('basedb');
        $comparedb = Input::get('comparedb');
        $base = Input::get('base');
        $compare = Input::get('compare');
        $baseSpecial = Input::get('baseSpecial');
        $compareSpecial = Input::get('compareSpecial');
        $mos = Input::get('mos');
        $mos = $this->getCompareMos($dbc, $mos, $basedb, $comparedb);
        $citys = Input::get('city');
        $subNetwork = '';
        if ($citys != '') {
            foreach ($citys as $city) {
                if ($city == 'unknow') {
                    $subNetworkIsNull = 'null';
                } else {
                    $subNetwork .= $dbc->getSubNets($city) . ',';
                }
            }
            $subNetwork = substr($subNetwork, 0, -1);
        }
        $filter = '';
        if ($subNetwork != '') {
            $filter = " and subNetwork in(" . $subNetwork . ") ";
        }
        $db = $dbc->getDB('kget', $basedb);
        $db->query("DROP TABLE IF EXISTS `TempParamCompareResult`;
                    create TABLE `TempParamCompareResult` (
                        `MO` varchar(255) DEFAULT NULL,
                        `参数名` varchar(255) DEFAULT NULL,
                        `基础值` varchar(255) DEFAULT NULL,
                        `对比值` varchar(255) DEFAULT NULL,
                        `条件` varchar(255) DEFAULT NULL
                    )ENGINE=MyISAM DEFAULT CHARSET=utf8;");
        $data_values = '';
        if ($baseSpecial != '' && $compareSpecial != '') {//1.指定ID
            $data_values = $this->getCompareResultByMoId($dbc, $db, $basedb, $comparedb, $base, $compare, $mos, $baseSpecial, $compareSpecial, $data_values, $filter);
        } else {
            $data_values = $this->getCompareResultByMeContext($dbc, $db, $basedb, $comparedb, $base, $compare, $mos, $data_values, $filter);
        }
        $query = 'false';
        if ($data_values != '') {
            $data_values = substr($data_values, 0, -1);
            $sql = "insert into TempParamCompareResult values $data_values";
            $query = $db->query($sql);
        }
        if ($query) {
            return 'true';
        } else {
            return 'false';
        }
    }
    /**
     * 参数对比 在指定ID情况下
     *
     * @return string
     */
    function getCompareResultByMoId($dbc, $db, $basedb, $comparedb, $base, $compare, $mos, $baseSpecial, $compareSpecial, $data_values,$filter){
        foreach ($mos as $mo) {
            $moTemp = $mo;
            if (strpos($mo, '_')) {
                $moTemp = strstr($mo, '_', TRUE);
            }
            $moId = strtolower($moTemp . 'Id');
            if ($dbc->columnIfExists($basedb, $mo, $moId)) {
                
                $columns = $this->getCompareColumns($dbc, $mo, $basedb, $comparedb);
                $sql = "select count(*) from $mo where meContext='$base' and $moId='$baseSpecial' $filter union all select count(*) from " . $comparedb . "." . $mo . " where meContext='$compare' and $moId='$compareSpecial' $filter";
                $rs = $db->query($sql)->fetchAll();
                $baseNum = $rs[0][0];
                $compareNum = $rs[1][0];
                if ($baseNum > 0 && $compareNum > 0) {
                    if ($dbc->columnIfExists($basedb, $mo, 'EUtranCellTDD')) {
                        $sql_base = "select mo," . $mo . "Id,EUtranCellTDD from " . $basedb . "." . $mo . " where meContext='$base' and $moId='$baseSpecial' $filter";
                        $sql_compare = "select mo," . $mo . "Id,EUtranCellTDD from " . $comparedb . "." . $mo . " where meContext='$compare' and $moId='$compareSpecial' $filter";
                        $rs_base = $db->query($sql_base, PDO::FETCH_ASSOC);
                        $rs_Compare = $db->query($sql_compare, PDO::FETCH_ASSOC);
                         if ($rs_base && $rs_Compare) {
                            $baseRows = $rs_base->fetchAll();
                            $compareRows = $rs_Compare->fetchAll();
                            if (count($baseRows) > 0 and count($compareRows) > 0) {
                                foreach ($baseRows as $baseRow) {
                                	$baseRow = array_change_key_case($baseRow,CASE_LOWER);
                                    $flag = true;
                                    foreach ($compareRows as $compareRow) {
                                    	$compareRow = array_change_key_case($compareRow,CASE_LOWER);
                                        if ($baseRow['eutrancelltdd'] == $compareRow['eutrancelltdd']) {

                                            $sql = "(select $columns from $mo where meContext='$base' and EUtranCellTDD='".$baseRow['eutrancelltdd']."' and $moId='$baseSpecial'  $filter) union all (select $columns from " . $comparedb . "." . $mo . " where meContext='$compare' and EUtranCellTDD='".$compareRow['eutrancelltdd']."' and $moId='$compareSpecial'  $filter)";
                                            $rs = $db->query($sql, PDO::FETCH_ASSOC);
                                            if ($rs) {
                                                $rows = $rs->fetchAll();
                                                $baseCell = $this->getConditionPrefix($baseRow['mo']);
                                                $compareCell = $this->getConditionPrefix($compareRow['mo']);
                                                if ($baseCell != '' && !substr_count($baseRow[$moId], $baseCell)) {
                                                    $condition_1 = $baseCell . "->" . $baseRow[$moId];
                                                } else {
                                                    $condition_1 = $baseRow[$moId];
                                                }
                                                if ($compareCell != '' && !substr_count($compareRow[$moId], $compareCell)) {
                                                    $condition_2 = $compareCell . "->" . $compareRow[$moId];
                                                } else {
                                                    $condition_2 = $compareRow[$moId];
                                                }
                                                //print_r($condition_1);
                                                //print_r($condition_2);
                                                $data_values = $this->inputTable($mo, $rows, $data_values, $condition_1 . "&" . $condition_2);
                                            }
                                            $flag = false;
                                            break;
                                        }
                                    }
                                    if ($flag) {
                                        $data_values = $this->compareIdIsNull($mo, $baseRow, $data_values, $moId);
                                    }
                                }
                                $data_values = $this->compareIdInBaseRowsIsNull($mo, $baseRows, $compareRows, $data_values, $moId);//需修改
                                $data_values = $this->baseIdInCompareRowsIsNull($mo, $baseRows, $compareRows, $data_values, $moId);//需修改
                            }
                        }

                    } else {//所对比mo中不含EUtranCellTDD字段（不是小区级别的）
                        $sql = "(select $columns from $mo where meContext='$base' and $moId='$baseSpecial' $filter) union all (select $columns from " . $comparedb . "." . $mo . " where meContext='$compare' and $moId='$compareSpecial' $filter)";
                        $rs = $db->query($sql, PDO::FETCH_ASSOC);
                        if ($rs) {
                            $rows = $rs->fetchAll();
                            $baseRow = array_change_key_case($rows[0],CASE_LOWER);
                            $compareRow = array_change_key_case($rows[1],CASE_LOWER);
                            $baseCell = $this->getConditionPrefix($baseRow['mo']);
                            $compareCell = $this->getConditionPrefix($compareRow['mo']);
                            if ($baseCell != '' && !substr_count($baseRow[$moId], $baseCell)) {
                                $condition_1 = $baseCell . "->" . $baseRow[$moId];
                            } else {
                                $condition_1 = $baseRow[$moId];
                            }
                            if ($compareCell != '' && !substr_count($compareRow[$moId], $compareCell)) {
                                $condition_2 = $compareCell . "->" . $compareRow[$moId];
                            } else {
                                $condition_2 = $compareRow[$moId];
                            }
                            $data_values = $this->inputTable($mo, $rows, $data_values, $condition_1 . "&" . $condition_2);
                        }
                    }
                } else if ($baseNum > 0 && $compareNum == 0) {// compareId is null
                    $sql = "select $columns from $mo where meContext='$base' and $moId='$baseSpecial' $filter";
                    $rs = $db->query($sql, PDO::FETCH_ASSOC);
                    $rows = $rs->fetchAll();
                    foreach ($rows as $baseRow) {
                        $data_values = $this->compareIdIsNull($mo, $baseRow, $data_values, $moId);
                    }
                } else if ($baseNum == 0 && $compareNum > 0) {//baseId is null
                    $sql = "select $columns from " . $comparedb . "." . $mo . " where meContext='$compare' and $moId='$compareSpecial' $filter";
                    $rs = $db->query($sql, PDO::FETCH_ASSOC);
                    $rows = $rs->fetchAll();
                    foreach ($rows as $compareRow) {
                        $data_values = $this->baseIdIsNull($mo, $compareRow, $data_values, $moId);
                    }
                }
            }
        }
        return $data_values;
    }
    /**
     * 参数对比 未指定ID情况下
     *
     * @return string
     */
    function getCompareResultByMeContext($dbc, $db, $basedb, $comparedb, $base, $compare, $mos, $data_values,$filter)
    {
        foreach ($mos as $mo) {
            //print_r($data_values);
            $moTemp = $mo;
            if (strpos($mo, '_')) {
                $moTemp = strstr($mo, '_', TRUE);
            }
            $moId = strtolower($moTemp . 'Id');
            if ($dbc->columnIfExists($basedb, $mo, $moId)) {
                $columns = $this->getCompareColumns($dbc, $mo, $basedb, $comparedb);
                $sql = "select count(*) from $mo where meContext='$base' $filter union all select count(*) from " . $comparedb . "." . $mo . " where meContext='$compare' $filter";
                $rs = $db->query($sql)->fetchAll();
                $baseNum = $rs[0][0];
                $compareNum = $rs[1][0];
                //print_r($baseNum);
                //print_r($compareNum);
                if ($baseNum > 0 && $compareNum > 0) {
                    if ($dbc->columnIfExists($basedb, $mo, 'EUtranCellTDD')) {//该MO中含有EUtranCellTDD，小区级MO
                        $sql_base = "select distinct mo,EUtranCellTDD from " . $basedb . "." . $mo . " where meContext='$base' $filter group by EUtranCellTDD";
                        $sql_compare = "select distinct mo,EUtranCellTDD from " . $comparedb . "." . $mo . " where meContext='$compare' $filter group by EUtranCellTDD";
                        $rs_base = $db->query($sql_base, PDO::FETCH_ASSOC);
                        $rs_Compare = $db->query($sql_compare, PDO::FETCH_ASSOC);
                        $baseCellRows = $rs_base->fetchAll();
                        $compareCellRows = $rs_Compare->fetchAll();
                        //print_r($baseCellRows);
                        //print_r($compareCellRows);
                        foreach ($baseCellRows as $baseCellRow) {
                        	$baseCellRow = array_change_key_case($baseCellRow, CASE_LOWER);
                            $flag1 = true;
                            foreach ($compareCellRows as $compareCellRow) {
                            	$compareCellRow = array_change_key_case($compareCellRow, CASE_LOWER);
                                if ($baseCellRow['eutrancelltdd'] == $compareCellRow['eutrancelltdd']) {
                                    $sql_base = "select mo,$moId,EUtranCellTDD from " . $basedb . "." . $mo . " where meContext='$base' and EUtranCellTDD='".$baseCellRow['eutrancelltdd']."' $filter";
                                    $sql_compare = "select mo,$moId,EUtranCellTDD from " . $comparedb . "." . $mo . " where meContext='$compare' and EUtranCellTDD='".$compareCellRow['eutrancelltdd']."' $filter";
                                    $rs_base = $db->query($sql_base, PDO::FETCH_ASSOC);
                                    $rs_Compare = $db->query($sql_compare, PDO::FETCH_ASSOC);
                                    if ($rs_base && $rs_Compare) {
                                        $baseRows = $rs_base->fetchAll();
                                        $compareRows = $rs_Compare->fetchAll();
                                        if (count($baseRows) > 0 and count($compareRows) > 0) {
                                            foreach ($baseRows as $baseRow) {
                                            	$baseRow = array_change_key_case($baseRow, CASE_LOWER);
                                                $flag = true;
                                                foreach ($compareRows as $compareRow) {
                                                	$compareRow = array_change_key_case($compareRow, CASE_LOWER);
                                                    if ($baseRow[$moId] == $compareRow[$moId]) {

                                                        $sql = "(select $columns from $mo where meContext='$base' and EUtranCellTDD='".$baseCellRow['eutrancelltdd']."' and $moId='$baseRow[$moId]' $filter) union all (select $columns from " . $comparedb . "." . $mo . " where meContext='$compare' and EUtranCellTDD='".$baseCellRow['eutrancelltdd']."' and $moId='$compareRow[$moId]' $filter)";
                                                        //print_r($sql);
                                                        $rs = $db->query($sql, PDO::FETCH_ASSOC);
                                                        if ($rs) {
                                                            $rows = $rs->fetchAll();
                                                            //print_r($rows);
                                                            $baseCell = $this->getConditionPrefix($baseRow['mo']);
                                                            $compareCell = $this->getConditionPrefix($compareRow['mo']);
                                                            if ($baseCell != '' && !substr_count($baseRow[$moId], $baseCell)) {
                                                                $condition_1 = $baseCell . "->" . $baseRow[$moId];
                                                            } else {
                                                                $condition_1 = $baseRow[$moId];
                                                            }
                                                            if ($compareCell != '' && !substr_count($compareRow[$moId], $compareCell)) {
                                                                $condition_2 = $compareCell . "->" . $compareRow[$moId];
                                                            } else {
                                                                $condition_2 = $compareRow[$moId];
                                                            }
                                                            $data_values = $this->inputTable($mo, $rows, $data_values, $condition_1 . "&" . $condition_2);
                                                        }
                                                        $flag = false;
                                                        break;
                                                    }
                                                }
                                                if ($flag) {
                                                    $data_values = $this->compareIdIsNull($mo, $baseRow, $data_values, $moId);
                                                }
                                            }
                                            $data_values = $this->compareIdInBaseRowsIsNull($mo, $baseRows, $compareRows, $data_values, $moId);
                                            $data_values = $this->baseIdInCompareRowsIsNull($mo, $baseRows, $compareRows, $data_values, $moId);
                                        }
                                    }

                                    $flag1 = false;
                                }
                            }
                            if ($flag1) {//相对于baseEUtranCellTDD compareEUtranCellTDD is null
                                $data_values = $this->compareIdIsNull($mo, $baseCellRow,  $data_values, 'eutrancelltdd');
                            }
                        }
                        //compareEUtranCellTDD 相对于baseEUtranCellTDD is null
                        $data_values = $this->compareIdInBaseRowsIsNull($mo, $baseCellRows, $compareCellRows, $data_values, 'eutrancelltdd');
                        $data_values = $this->baseIdInCompareRowsIsNull($mo, $baseCellRows, $compareCellRows, $data_values, 'eutrancelltdd');
                    } else {//该MO中不含有EUtranCellTDD，基站级MO
                        //print_r("testtest------------------");
                        $sql_base = "select mo,$moId from " . $basedb . "." . $mo . " where meContext='$base' $filter";
                        $sql_compare = "select mo,$moId from " . $comparedb . "." . $mo . " where meContext='$compare' $filter";
                        $rs_base = $db->query($sql_base, PDO::FETCH_ASSOC);
                        $rs_Compare = $db->query($sql_compare, PDO::FETCH_ASSOC);
                        $data_values = $this->inputTableValueMeContext($db, $dbc, $mo, $basedb, $comparedb, $base, $compare, $rs_base, $rs_Compare, $data_values, $filter);
                    }
                } else if ($baseNum > 0 && $compareNum == 0) {//meContext在base中存在，在compare中不存在
                    $sql_base = "select mo,$moId from " . $basedb . "." . $mo . " where meContext='$base' $filter";
                    $rs_base = $db->query($sql_base, PDO::FETCH_ASSOC);
                    $baseRows = $rs_base->fetchAll();
                    foreach ($baseRows as $baseRow) {
                        $data_values = $this->compareIdIsNull($mo, $baseRow, $data_values, $moId);
                    }
                } else if ($baseNum == 0 && $compareNum > 0) {//meContext在base中不存在，在compare中存在
                    $sql_compare = "select mo,$moId from " . $comparedb . "." . $mo . " where meContext='$compare' $filter";
                    $rs_compare = $db->query($sql_compare, PDO::FETCH_ASSOC);
                    $compareRows = $rs_compare->fetchAll();
                    foreach ($compareRows as $compareRow) {
                        $data_values = $this->baseIdIsNull($mo, $compareRow, $data_values, $moId);
                    }
                }
            }
        }
        return $data_values;
    }
    /**
     *获取需要对比的列
     */
    function getCompareColumns($dbc, $mo, $basedb, $comparedb){
        $sql_column = "select a.COLUMN_NAME from (select COLUMN_NAME from information_schema.COLUMNS where TABLE_NAME='$mo' and TABLE_SCHEMA='$basedb') a join (select COLUMN_NAME from information_schema.COLUMNS where TABLE_NAME='$mo' and TABLE_SCHEMA='$comparedb') b on a.COLUMN_NAME=b.COLUMN_NAME;";
        $info_db = $dbc->getDB('kget','information_schema');
        $rs = $info_db->query($sql_column);
        $res = $rs->fetchAll(PDO::FETCH_ASSOC);
        $columnArr = array();
        foreach ($res as $row) {
           array_push($columnArr, "`".$row['COLUMN_NAME']."`");
        }
        $columns = implode(',', $columnArr);
        return $columns;
    }
    /**
     *获取需要对比的MO
     */
    function getCompareMos($dbc, $mos, $basedb, $comparedb){
        $mosValue = "";
        foreach ($mos as $mo) {
            $mosValue.= "'".$mo."',";
        }
        $mosValue = "(".substr($mosValue, 0, -1).")";
        $sql_mos = "select DISTINCT TABLE_NAME from information_schema.`TABLES` WHERE TABLE_SCHEMA='$basedb' 
                        AND TABLE_NAME IN $mosValue and TABLE_NAME in(select DISTINCT TABLE_NAME from information_schema.`TABLES` WHERE TABLE_SCHEMA='$comparedb' AND TABLE_NAME IN $mosValue)";
                            //print_r($sql_column);
        $info_db = $dbc->getDB('kget', 'information_schema');
        $rs = $info_db->query($sql_mos);
        $res = $rs->fetchAll(PDO::FETCH_ASSOC);
        $commonMos = array();
        foreach ($res as $row) {
           array_push($commonMos, $row['TABLE_NAME']);
        }
        return $commonMos;
    }
    /**
     * 写入比较结果
     *
     * @param mixed  $db          数据库连接句柄
     * @param mixed  $dbc         数据库连接句柄
     * @param string $mo          参数表名
     * @param string $basedb      基础DB
     * @param string $comparedb   比较DB
     * @param string $base        基础值
     * @param string $compare     比较值
     * @param array  $rs_base     基础结果集
     * @param array  $rs_Compare  比较结果集
     * @param array  $data_values 对比结果
     * @param string $column      列名
     *
     * @return string
     */
    public function inputTableValueCell($db, $dbc, $mo, $basedb, $comparedb, $base, $compare, $rs_base, $rs_Compare, $data_values, $column)
    {
        $sql_column = "select a.COLUMN_NAME from (select COLUMN_NAME from information_schema.COLUMNS where TABLE_NAME='$mo' and TABLE_SCHEMA='$basedb') a join (select COLUMN_NAME from information_schema.COLUMNS where TABLE_NAME='$mo' and TABLE_SCHEMA='$comparedb') b on a.COLUMN_NAME=b.COLUMN_NAME;";
        $info_db = $dbc->getDB('kget','information_schema');
        $rs = $info_db->query($sql_column);
        $res = $rs->fetchAll(PDO::FETCH_ASSOC);
        $columnArr = array();
        foreach ($res as $row) {
           array_push($columnArr, $row['COLUMN_NAME']);
        }
        $columns = implode(',', $columnArr);
        $moTemp = $mo;
        if (strpos($mo, '_')) {
                $moTemp = strstr($mo, '_', TRUE);
            }
        $moId = $moTemp . "Id";
        if ($rs_base && $rs_Compare) {
            $baseRows = $rs_base->fetchAll();
            $compareRows = $rs_Compare->fetchAll();
            if (count($baseRows) > 0 and count($compareRows) > 0) {
                foreach ($baseRows as $baseRow) {

                    $flag = true;
                    foreach ($compareRows as $compareRow) {
                        if ($baseRow[$moId] == $compareRow[$moId]) {
                            $sql = "(select $columns from $mo where $column='$base' and $moId='$baseRow[$moId]') union all (select $columns from " . $comparedb . "." . $mo . " where $column='$compare' and $moId='$compareRow[$moId]')";
                            $rs = $db->query($sql, PDO::FETCH_ASSOC);
                            if ($rs) {
                                $baseCell = $this->getConditionPrefix($baseRow['mo']);
                                $compareCell = $this->getConditionPrefix($compareRow['mo']);
                                if ($baseCell != '' && !substr_count($baseRow[$moId], $baseCell)) {
                                    $condition_1 = $baseCell . "->" . $baseRow[$moId];
                                } else {
                                    $condition_1 = $baseRow[$moId];
                                }
                                if ($compareCell != '' && !substr_count($compareRow[$moId], $compareCell)) {
                                    $condition_2 = $compareCell . "->" . $compareRow[$moId];
                                } else {
                                    $condition_2 = $compareRow[$moId];
                                }
                                $data_values = $this->inputTable($mo, $rs, $data_values, $condition_1 . "&" . $condition_2);
                            }
                            $flag = false;
                            break;
                        }
                    }
                    if ($flag) {
                        $data_values = $this->baseIdIsNull($mo, $baseRow, $compareRow, $data_values, $moId);
                    }
                }
                $data_values = $this->compareIdIsNull($mo, $baseRows, $compareRows, $data_values, $moId);
            }
        }
        return $data_values;
    }

    /**
     * 获得前缀名
     *
     * @param string $moValue MO参数值
     *
     * @return string
     */
    public function getConditionPrefix($moValue)
    {
        $cell = '';
        if (strpos($moValue, "EUtranCellTDD")) {
            $substr = substr($moValue, strpos($moValue, "EUtranCellTDD="));
            $cell = substr($substr, strpos($substr, "=") + 1, strpos($substr, ",") - 1 - strpos($substr, "="));
        } else if (strpos($moValue, "EUtranCellFDD")) {
            $substr = substr($moValue, strpos($moValue, "EUtranCellFDD="));
            $cell = substr($substr, strpos($substr, "=") + 1);
        } else if (strpos($moValue, "AuxPlugInUnit")) {
            $substr = substr($moValue, strpos($moValue, "AuxPlugInUnit="));
            $cell = substr($substr, strpos($substr, "_") + 1);
        }
        return $cell;
    }

    /**
     * 写入表格
     *
     * @param string $mo          MO对象名
     * @param array  $rs          查询结果集
     * @param string $data_values 比较结果集合
     * @param string $condition   附加条件?
     *
     * @return string
     */
    public function inputTable($mo, $rows, $data_values, $condition)
    {
        //$rows = $rs->fetchAll();
        //print_r($rows);
        if (count($rows) == 2) {
            $row_base = array_change_key_case($rows[0],CASE_LOWER);
            $row_compare =array_change_key_case($rows[1],CASE_LOWER);
            foreach ($row_base as $key => $value) {
               /* if ($key != 'id' && $key != 'recordTime' && $key != 'mo' && $key != 'subNetwork' && $key != 'meContext' && $key != 'EUtranCellTDD' && $key !='removingMonitoringStart' && $key !='timeOfLastModification' && $key !='ipAddr') {*///&& $key !='removingMonitoringStart' && $key !='timeOfLastModification'
               if ($key != 'id' && $key != 'recordtime' && $key != 'mo' && $key != 'subnetwork' && $key != 'mecontext' && $key != 'eutrancelltdd' && $key !='removingmonitoringstart' && $key !='timeoflastmodification' && $key !='ipaddr') {
                    if ($value != $row_compare[$key]) {
                        $param = $key;
                        $baseValue = $value;
                        $compareValue = $row_compare[$key];
                        $data_values .= "('$mo','$param','$baseValue','$compareValue','$condition'),";
                    }
                }
            }
        }
        //print_r($data_values);
        return $data_values;
    }
    /**
     * CompareID NULL Check
     *
     * @param string $mo          MO对象名
     * @param array  $baseRow     基础数据
     * @param array  $data_values 比较结果集
     * @param string $moId        MOID
     *
     * @return string
     */
    public function compareIdIsNull($mo, $baseRow, $data_values, $moId)
    {
    	$baseRow = array_change_key_case($baseRow,CASE_LOWER);
    	$moId = strtolower($moId);
        $moValue = $baseRow['mo'];
        $cell = $this->getConditionPrefix($moValue);
        $condition = $cell . "->" . $baseRow[$moId] . "&" . 'null';
        $data_values .= "('$mo','','','','$condition'),";
        return $data_values;
    }
    /**
     * BaseID NULL Check
     *
     * @param string $mo          MO对象名
     * @param array  $compareRow  比较数据
     * @param array  $data_values 比较结果集
     * @param string $moId        MOID
     *
     * @return string
     */
    public function baseIdIsNull($mo, $compareRow, $data_values, $moId)
    {
    	$compareRow = array_change_key_case($compareRow,CASE_LOWER);
    	$moId = strtolower($moId);
        $moValue = $compareRow['mo'];
        $cell = $this->getConditionPrefix($moValue);
        $condition = 'null' . "&" . $cell . "->" . $compareRow[$moId];
        $data_values .= "('$mo','','','','$condition'),";
        return $data_values;
    }
    /**
     * compareId in baseRows is null check
     *
     * @param string $mo          mo对象名
     * @param array  $baseRows    基础数据
     * @param array  $compareRows 比较数据
     * @param array  $data_values 比较结果
     * @param string $moId        MOID
     *
     * @return string
     */
    public function compareIdInBaseRowsIsNull($mo, $baseRows, $compareRows, $data_values, $moId)
    {
    	$moId = strtolower($moId);
        foreach ($compareRows as $compareRow) {
        	$compareRow = array_change_key_case($compareRow,CASE_LOWER);
        	
            $flag = true;
            foreach ($baseRows as $baseRow) {
            	$baseRow = array_change_key_case($baseRow,CASE_LOWER);
                if ($baseRow[$moId] == $compareRow[$moId]) {
                    $flag = false;
                    break;
                }
            }
            if ($flag) {
                $moValue = $compareRow['mo'];
                $cell = $this->getConditionPrefix($moValue);
                $condition = 'null' . "&" . $cell . "->" . $compareRow[$moId];
                $data_values .= "('$mo','','','','$condition'),";
            }
        }
        return $data_values;
    }
    /**
     * compareId in baseRows is null check
     *
     * @param string $mo          mo对象名
     * @param array  $baseRows    基础数据
     * @param array  $compareRows 比较数据
     * @param array  $data_values 比较结果
     * @param string $moId        MOID
     *
     * @return string
     */
    public function baseIdInCompareRowsIsNull($mo, $baseRows, $compareRows, $data_values, $moId)
    {
    	$moId = strtolower($moId);
        foreach ($baseRows as $baseRow) {
        	$baseRow = array_change_key_case($baseRow,CASE_LOWER);
            $flag = true;
            foreach ($compareRows as $compareRow) {
            	$compareRow = array_change_key_case($compareRow,CASE_LOWER);
                if ($compareRow[$moId] == $compareRow[$moId]) {
                    $flag = false;
                    break;
                }
            }
            if ($flag) {
                $moValue = $compareRow['mo'];
                $cell = $this->getConditionPrefix($moValue);
                $condition = 'null' . "&" . $cell . "->" . $baseRow[$moId];
                $data_values .= "('$mo','','','','$condition'),";
            }
        }
        return $data_values;


    }

    /**
     * 写入MeContext对比结果
     *
     * @param mixed  $db          数据库连接句柄
     * @param mixed  $dbc         数据库连接句柄
     * @param string $mo          MO对象名
     * @param string $basedb      基础DB
     * @param string $comparedb   对比DB
     * @param string $base        基础值
     * @param string $compare     对比值
     * @param array  $rs_base     基础集合
     * @param array  $rs_Compare  对比集合
     * @param array  $data_values 对比结果集
     *
     * @return string
     */
    public function inputTableValueMeContext($db, $dbc, $mo, $basedb, $comparedb, $base, $compare, $rs_base, $rs_Compare, $data_values, $filter)
    {
        $columns = $this->getCompareColumns($dbc, $mo, $basedb, $comparedb);
        $moTemp = $mo;
        if (strpos($mo,'_')) {
                $moTemp = strstr($mo, '_', TRUE);
            }
        $moId = strtolower($moTemp . "Id");

        if ($rs_base && $rs_Compare) {
            $baseRows = $rs_base->fetchAll();
            $compareRows = $rs_Compare->fetchAll();
            if (count($baseRows) > 0 and count($compareRows) > 0) {
                foreach ($baseRows as $baseRow) {
                	$baseRow = array_change_key_case($baseRow, CASE_LOWER);
                    $flag = true;
                    foreach ($compareRows as $compareRow) {
                    	$compareRow = array_change_key_case($compareRow, CASE_LOWER);
                        if ($baseRow[$moId] == $compareRow[$moId]) {

                            $sql = "(select $columns from $mo where meContext='$base' and $moId='$baseRow[$moId]' $filter) union all (select $columns from " . $comparedb . "." . $mo . " where meContext='$compare' and $moId='$compareRow[$moId]' $filter)";
                            //print_r($sql);
                            $rs = $db->query($sql, PDO::FETCH_ASSOC);
                            if ($rs) {
                                $rows = $rs->fetchAll();
                                //print_r($rows);
                                $baseCell = $this->getConditionPrefix($baseRow['mo']);
                                $compareCell = $this->getConditionPrefix($compareRow['mo']);
                                if ($baseCell != '' && !substr_count($baseRow[$moId], $baseCell)) {
                                    $condition_1 = $baseCell . "->" . $baseRow[$moId];
                                } else {
                                    $condition_1 = $baseRow[$moId];
                                }
                                if ($compareCell != '' && !substr_count($compareRow[$moId], $compareCell)) {
                                    $condition_2 = $compareCell . "->" . $compareRow[$moId];
                                } else {
                                    $condition_2 = $compareRow[$moId];
                                }
                                $data_values = $this->inputTable($mo, $rows, $data_values, $condition_1 . "&" . $condition_2);
                            }
                            $flag = false;
                            break;
                        }
                    }
                    if ($flag) {
                        $data_values = $this->baseIdIsNull($mo, $compareRow, $data_values, $moId);
                    }
                }
                $data_values = $this->compareIdInBaseRowsIsNull($mo, $baseRows, $compareRows, $data_values, $moId);
                $data_values = $this->baseIdInCompareRowsIsNull($mo, $baseRows, $compareRows, $data_values, $moId);
            } else if ($baseNum > 0 && $compareNum == 0) {//meContext在base中存在，在compare中不存在
                $sql_base = "select mo,meContext,$moId from " . $basedb . "." . $mo . " where meContext='$base' $filter";
                $rs_base = $db->query($sql_base, PDO::FETCH_ASSOC);
                $baseRows = $rs_base->fetchAll();
                foreach ($baseRows as $baseRow) {
                    $data_values = $this->compareIdIsNull($mo, $baseRow, $data_values, 'mecontext');
                }
            } else if ($baseNum == 0 && $compareNum > 0) {//meContext在base中不存在，在compare中存在
                $sql_compare = "select mo,meContext,$moId from " . $comparedb . "." . $mo . " where meContext='$compare' $filter";
                $rs_compare = $db->query($sql_compare, PDO::FETCH_ASSOC);
                $compareRows = $rs_compare->fetchAll();
                foreach ($compareRows as $compareRow) {
                    $data_values = $this->baseIdIsNull($mo, $compareRow, $data_values, 'mecontext');
                }
            }
        }
        return $data_values;
    }
}
