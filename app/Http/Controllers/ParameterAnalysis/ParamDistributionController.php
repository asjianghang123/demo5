<?php

/**
 * ParamDistributionController.php
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
use App\Models\Mongs\Task;

/**
 * 参数分布检查
 * Class ParamDistributionController
 *
 * @category ParameterAnalysis
 * @package  App\Http\Controllers\ParameterAnalysis
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class ParamDistributionController extends Controller
{


    /**
     * 获得日期列表
     *
     * @return void
     */
    public function getDate()
    {
        $user = Auth::user();//获取登录用户信息
        $userName = $user->user;
        $tasks = Task::where('type', 'parameter')->where('status', 'complete')->where('taskName', 'like', 'kget______');
        if ($userName != 'admin') {
            $tasks = $tasks->whereIn('owner', [$userName,'admin']);
        }
        $tasks = $tasks->orderBy('taskName', 'desc')->get()->toArray();
        $items = array();
        foreach ($tasks as $task) {
            $items[] = array("text"=>$task['taskName'], "id"=>$task['taskName']);
        }
        echo json_encode($items);//需要通过response返回响应数据

    }//end getDate()


    /**
     * 获得检查项目列表
     *
     * @return void
     */
    public function getTreeData()
    {
        $schema      = input::get("task");
        $filename    = "common/json/parameterTreeData.json";
        $json_string = file_get_contents($filename);
        $datas       = json_decode($json_string, true);
        $myfile      = fopen("common/txt/paramDistribution.txt", "w") or die("Unable to open file!");
        $this->_fun($datas, $myfile, $schema);
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
    private function _fun($a, $myfile, $schema)
    {
        $dbc = new DataBaseConnection();
        $db  = $dbc->getDB('mongs', 'information_schema');
        foreach ($a as $key => $val) {
            if (is_array($val)) {
                // 如果键值是数组，则进行函数递归调用
                $this->_fun($val, $myfile, $schema);
            } else {
                // 如果键值是数值，则进行输出
                if ($key == 'text') {
                    $sql = "select COLUMN_NAME from `COLUMNS` where TABLE_NAME=:val and TABLE_SCHEMA=:schema";
                    $stmt = $db->prepare($sql);
                    $stmt->bindParam(':val', $val);
                    $stmt->bindParam(':schema', $schema);
                    if ($stmt->execute()) {
                        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($rows as $row) {
                            $txt = $val.'='.$row['COLUMN_NAME']."\r\n";
                            fwrite($myfile, $txt);
                        }
                    }
                }
            }
        }

    }//end fun()


    /**
     * 获得参数列表
     *
     * @return void
     */
    public function getParameterList()
    {
        $schema  = input::get("task");
        $table   = input::get("mo");
        $pattern = input::get("pattern");

        $dbc = new DataBaseConnection();
        $db  = $dbc->getDB('mongs', 'information_schema');
        if ($pattern) {
            $sql = "select COLUMN_NAME from `COLUMNS` where TABLE_NAME=:table and TABLE_SCHEMA=:schema and COLUMN_NAME not LIKE '%id' and COLUMN_NAME LIKE :pattern";
        } else {
            $sql = "select COLUMN_NAME from `COLUMNS` where TABLE_NAME=:table and TABLE_SCHEMA=:schema and COLUMN_NAME not LIKE '%id'";
        }
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':table', $table);
        $stmt->bindParam(':schema', $schema);
        if ($pattern) {
            $pattern = "%$pattern%";
            $stmt->bindParam(':pattern', $pattern);
        }
        $items      = array();
        $returnData = array();
        $i          = 0;
        $j          = 1;
        if ($stmt->execute()) {
            $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($row as $r) {
                if ($pattern) {
                    $j++;
                    $items[$j] = '{"id":'.$j.',"text":"'.$r['COLUMN_NAME'].'"}';
                } else {
                    if ($i++ > 3) {
                        $j++;
                        $items[$j] = '{"id":'.$j.',"text":"'.$r['COLUMN_NAME'].'"}';
                    }
                }
            }
        }

        $content = implode(",", $items);
        $returnData['content'] = '['.$content.']';
        $returnData['count']   = $j;
        echo json_encode($returnData);

    }//end getParameterList()


    /**
     * 更新查询
     *
     * @return void
     */
    public function getUpdateSearch()
    {
        $data    = Input::get('data');
        $handler = fopen("common/txt/paramDistribution.txt", "r") or die("Unable to open file!");
        $m       = [];
        $params  = [];
        while (!feof($handler)) {
            $m[] = fgets($handler, 4096);
        }

        array_pop($m);
        foreach ($m as $val) {
            $val = str_replace("\r\n", "", $val);
            $arr = explode('=', trim($val));
            if (strpos(strtolower(trim($arr[1])), strtolower($data)) !== false) {
            }

            $params[][$arr[0]] = $arr[1];
        }

        $tables  = [];
        $columns = [];
        foreach ($params as $values) {
            foreach ($values as $key => $value) {
                if (strpos(strtolower($value), strtolower($data)) !== false) {
                    array_push($tables, $key);
                    array_push($columns, $value);
                }
            }
        }

        $tables     = array_unique($tables);
        $columns    = array_unique($columns);
        $items      = array();
        $itemsCol   = array();
        $returnData = array();
        $j          = 1;
        foreach ($tables as $value) {
            $j++;
            $items[$j] = '{"id":'.$j.',"text":"'.$value.'"}';
        }

        $content = implode(",", $items);
        $returnData['mo']['content'] = '['.$content.']';
        $i = 1;
        foreach ($columns as $value) {
            $i++;
            $itemsCol[$i] = '{"id":'.$i.',"text":"'.$value.'"}';
        }

        $content = implode(",", $itemsCol);
        $returnData['params']['content'] = '['.$content.']';
        echo json_encode($returnData);

    }//end getUpdateSearch()


    /**
     * 获得城市列表
     *
     * @return void
     */
    public function getCityBak()
    {
        $table  = input::get("table");
        $dbname = input::get("db");
        $dbc    = new DataBaseConnection();
        $db     = $dbc->getDB('mongs', $dbname);
        $items  = array();
        $sql    = "SELECT DISTINCT cityChinese FROM `".$table."` order by id ASC";
        $res    = $db->query($sql);
        if ($res) {
            $row = $res->fetchAll(PDO::FETCH_ASSOC);
            foreach ($row as $r) {
                array_push($items, $r);
            }
        }

        echo json_encode($items);

    }//end getCity()


    /**
     * 参数分布图形数据获取
     *
     * @return string 分布图数据(JSON)
     */
    public function getChartData()
    {
        $table         = input::get("table");
        $table         = $this->check_input($table);
        $parameterName = input::get("parameterName");
        $parameterName = $this->check_input($parameterName);
        $dbname        = input::get("db");
        $dbname        = $this->check_input($dbname);
        $dbc           = new DataBaseConnection();
        $dbn           = $dbc->getDB('mongs', $dbname);
        $categories    = array();
        $sql_parameterDistribute = "select DISTINCT $parameterName from $table ORDER BY CAST($parameterName AS signed)";
        $stmt = $dbn->prepare($sql_parameterDistribute);
        if ($stmt->execute()) {
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                if ($row[$parameterName] != "" && substr($row[$parameterName], 0, 4) != "!!!!") {
                    array_push($categories, $row[$parameterName]);
                }
            }
        }

        $res    = $dbc->getCitySubNetCategories();
        $series = array();
        foreach ($res as $items) {
            $city       = $items->connName;
            $subNetwork = $items->subNetwork;
            $subNetwork = $dbc->reCombine($subNetwork);
            $sql        = "select DISTINCT $parameterName as category,count(*) as num from $table where subNetwork in(".$subNetwork.") GROUP BY CAST($parameterName AS signed) ORDER BY CAST($parameterName AS signed) ";
            $stmt = $dbn->prepare($sql);
            if ($stmt->execute()) {
                $rs     = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $series = $this->getHighChartSeries($rs, $city, $series, $categories);
            }
        }

        $data['category'] = $categories;
        $data['series']   = array();
        foreach ($series as $key => $value) {
            $data['series'][] = [
                                 'name' => $key,
                                 'data' => $value,
                                ];
        }

        return json_encode($data);

    }//end getChartData()
    function check_input($value)
    {
        //去除斜杠
        if (get_magic_quotes_gpc()) {
            $value=stripslashes($value);
        }
        return $value;
    }

    /**
     * 返回结果重新组合
     *
     * @param array  $rs         查询结果
     * @param string $seriesKey  时序键
     * @param array  $series     时序数据
     * @param array  $categories categories
     *
     * @return mixed
     */
    public function getHighChartSeries($rs, $seriesKey, $series, $categories)
    {
        foreach ($categories as $category) {
            $flag = false;
            foreach ($rs as $item) {
                if ($category == $item['category']) {
                    $series[$seriesKey][] = floatval($item['num']);
                    $flag = true;
                    break;
                }
            }

            if (!$flag) {
                $series[$seriesKey][] = floatval(0);
            }
        }

        return $series;

    }//end getHighChartSeries()


    /**
     * NULL判断
     *
     * @param array $arr check数组
     *
     * @return mixed
     */
    public function isNull($arr = array())
    {
        for ($i = 0; $i < count($arr); $i++) {
            for ($j = 0; $j < count($arr[$i]); $j++) {
                if ($arr[$i][$j] != "null") {
                    return $arr[$i][$j];
                }
            }
        }

    }//end isNull()


    /**
     * 获得Y集合
     *
     * @param float $min 最小值
     * @param float $max 最大值
     *
     * @return array Y值集合
     */
    public function getValues($min, $max)
    {
        $y_data    = array();
        $y_data[2] = (($min + $max) / 2);
        $y_data[3] = (($max + $y_data[2]) / 2);
        $y_data[1] = (($min + $y_data[2]) / 2);
        $y_data[0] = $min;
        $y_data[4] = $max;
        for ($i = 0; $i < count($y_data); $i++) {
            $y_data[$i] = round($y_data[$i], 0);
        }

        return $y_data;

    }//end getValues()


    /**
     * 获得子网集合
     *
     * @param mixed  $db   数据库连接句柄
     * @param string $city 城市名
     *
     * @return string
     */
    public function getSubNetsBak($db, $city)
    {
        $SQL        = "select subNetwork from databaseconn where cityChinese = '$city'";
        $res        = $db->query($SQL);
        $row        = $res->fetch(PDO::FETCH_ASSOC);
        $subNets    = $row['subNetwork'];
        $subNetArr  = explode(",", $subNets);
        $subNetsStr = '';
        foreach ($subNetArr as $subNet) {
            $subNetsStr .= "'".$subNet."',";
        }

        return $subNetsStr;

    }//end getSubNets()


    /**
     * 获得城市列表
     *
     * @return string
     */
    public function getCitySelect()
    {
        $dbc = new DataBaseConnection();
        return $dbc->getCityOptions();

    }//end getCitySelect()
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
        $SQL        = "select subNetwork from databaseconn where cityChinese = '$city'";
        $res        = $db->query($SQL);
        $row        = $res->fetch(PDO::FETCH_ASSOC);
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
        if($citys!= ''){
        foreach ($citys as $city) {
            $databaseConns = DB::table('databaseconn')->select('subNetwork')->where('cityChinese', '=', $city)->get();
            foreach ($databaseConns as $databaseConn) {
                $subStr = $databaseConn->subNetwork;
                $subArr = explode(',', $subStr);
                foreach ($subArr as $sub) {
                    $city = '{"text":"'.$sub.'","value":"'.$sub.'"}';
                    array_push($items, $city);
                }
            }
        }
    }
        return response()->json($items);

    }//end getAllSubNetwork()

    /**
     * 获取参数分布详情表头信息
     *
     * @return array
     */
    public function getTableHeader()
    {
        $schema = Input::get('db');
        $table  = Input::get('table');
        $dbc    = new DataBaseConnection();
        $db     = $dbc->getDB('mongs', 'information_schema');
        $sql    = "select COLUMN_NAME from `COLUMNS` where TABLE_NAME=:table and TABLE_SCHEMA=:schema and COLUMN_NAME LIKE '%id'";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':table', $table);
        $stmt->bindParam(':schema', $schema);
        $params = '';
        $i      = 0;
        if ($stmt->execute()) {
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                if ($i++ > 0) {
                    $params = $params.$row['COLUMN_NAME'].',';
                }
            }
        }

        $dbn           = $dbc->getDB('mongs', $schema);
        $parameterName = input::get("parameterName");
        $query         = "select id,recordTime,mo,subNetwork,meContext,$params $parameterName from ".$table." limit 1";
        $rs            = $dbn->query($query, PDO::FETCH_ASSOC);
        if ($rs) {
            $rs = $rs->fetchAll();
            if (count($rs) > 0) {
                return $rs[0];
            } else {
                $result           = array();
                $result['result'] = 'error';
            }
        } else {
            $result           = array();
            $result['result'] = 'error';
        }

        return $result;

    }//end getTableHeader()


    /**
     * 获取参数分布相应参数的信息
     *
     * @return array
     */
    public function getTableData()
    {
        $dbname      = input::get("db");
        $table       = input::get("table");
        $paramLength = Input::get('paramLength');
        $dbc         = new DataBaseConnection();
        $db          = $dbc->getDB('mongs', 'information_schema');
        $sql         = "select COLUMN_NAME from `COLUMNS` where TABLE_NAME=:table and TABLE_SCHEMA=:dbname and COLUMN_NAME LIKE '%id'";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':table', $table);
        $stmt->bindParam(':dbname', $dbname);
        $params      = '';
        $i           = 0;
        if ($stmt->execute()) {
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                if ($i++ > 0) {
                    $params = $params.$row['COLUMN_NAME'].',';
                }
            }
        }

        $dbn           = $dbc->getDB('mongs', $dbname);
        $displayStart  = Input::get('page');
        $displayLength = Input::get('limit');
        $offset        = (($displayStart - 1) * $displayLength);
        $limit         = " limit $offset,$displayLength ";

        $parameterName = input::get("parameterName");
        $sort          = isset($_REQUEST['sort']) ? strval($_REQUEST['sort']) : 'recordTime';
        $order         = isset($_REQUEST['order']) ? strval($_REQUEST['order']) : 'desc';
        $orderFilter   = " order by $sort $order ";
        $filter        = '';

        $citys = Input::get('citys');
        $subNets = Input::get('subNet');
        $subNetwork= '';
        if ($subNets != '') {
            $subNetsstr = implode(',', $subNets);
            $subNetwork = "'".str_replace(",", "','", $subNetsstr)."'";
            $filter = " where subNetwork in (".$subNetwork.") ";
        } else if ($citys != '') {
            foreach ($citys as $city) {
                $subNetwork .= $dbc->getSubNets($city).',';
            }

            $subNetwork = substr($subNetwork, 0, -1);
            $filter = " where subNetwork in (".$subNetwork.") ";
        }
        $result = array();
        $sql = "select count(*) from ".$table.$filter;
        $stmt = $dbn->prepare($sql);
        if ($stmt->execute()) {
            $result["total"] = $stmt->fetchColumn();
        }
        $sql   = "select id,recordTime,mo,subNetwork,meContext,$params $parameterName from ".$table.$filter.$orderFilter.$limit;
        $stmt = $dbn->prepare($sql);
        $items = array();
        if ($stmt->execute()) {
            $res = $stmt->fetchAll(PDO::FETCH_OBJ);
            foreach ($res as $row) {
                array_push($items, $row);
            }

            $result["records"] = $items;
        }

        return $result;

    }//end getTableData()


    /**
     * 文件导出
     *
     * @return array
     */
    public function exportCSV()
    {
        $dbname   = input::get("db");
        $table    = input::get("table");
        $dbc      = new DataBaseConnection();
        $db       = $dbc->getDB('mongs', 'information_schema');
        $fileName = "files/".$table."_".date('YmdHis').".csv";
        $sql      = "select COLUMN_NAME from `COLUMNS` where TABLE_NAME=:table and TABLE_SCHEMA=:dbname and COLUMN_NAME LIKE '%id'";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':table', $table);
        $stmt->bindParam(':dbname', $dbname);
        $params   = '';
        $i        = 0;
        if ($stmt->execute()) {
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                if ($i++ > 0) {
                    $params = $params.$row['COLUMN_NAME'].',';
                }
            }
        }

        $dbn           = $dbc->getDB('mongs', $dbname);
        $parameterName = input::get("parameterName");

        $sort        = isset($_REQUEST['sort']) ? strval($_REQUEST['sort']) : 'recordTime';
        $order       = isset($_REQUEST['order']) ? strval($_REQUEST['order']) : 'desc';
        $orderFilter = " order by $sort $order ";
        $filter      = '';

        $citys      = Input::get('citys');
        $subNets = Input::get('subNet');
        $subNetwork= '';
        if ($subNets != '') {
            $subNetsstr = implode(',',$subNets);
            $subNetwork = "'".str_replace(",", "','", $subNetsstr)."'";
            $filter = " where subNetwork in (".$subNetwork.") ";
        } else if ($citys != '') {
            foreach ($citys as $city) {
                $subNetwork .= $dbc->getSubNets($city).',';
            }

            $subNetwork = substr($subNetwork, 0, -1);
            $filter = " where subNetwork in (".$subNetwork.") ";
        }
        $result = array();
        $sql = "select count(*) from ".$table.$filter;
        $stmt = $dbn->prepare($sql);
        if ($stmt->execute()) {
            $result["total"] = $stmt->fetchColumn();
        }
        $sql = "select id,recordTime,mo,subNetwork,meContext,$params $parameterName from ".$table.$filter.$orderFilter;
        $stmt = $dbn->prepare($sql);
        if ($stmt->execute()) {
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (count($items) > 0) {
                $row      = $items[0];
                $column   = implode(",", array_keys($row));
                $column   = mb_convert_encoding($column, 'gbk', 'utf-8');
                $fileUtil = new FileUtil();
                $fileUtil->resultToCSV2($column, $items, $fileName);
                $result['fileName'] = $fileName;
                $result['result']   = true;
            } else {
                $result['result'] = false;
            }
        } else {
            $result['result'] = false;
        }

        return $result;

    }//end exportCSV()


}//end class
