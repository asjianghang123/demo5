<?php

/**
 * GSMQueryController.php
 *
 * @category QueryAnalysis
 * @package  App\Http\Controllers\QueryAnalysis
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
namespace App\Http\Controllers\QueryAnalysis;

use App\Http\Controllers\Common\DataBaseConnection;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\Models\Mongs\Databaseconn2G;
use App\Models\Mongs\Template_2G;
use App\Models\Mongs\Kpiformula2G;
use PDO;

/**
 * GSM指标查询
 * Class GSMQueryController
 *
 * @category QueryAnalysis
 * @package  App\Http\Controllers\QueryAnalysis
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class GSMQueryController extends GetTreeData
{


    /**
     * 获得GSM查询视图
     *
     * @return mixed
     */
    public function init()
    {
        return view('QueryAnalysis.GSMQuery');

    }//end init()


    /**
     * 获得模板列表
     *
     * @return mixed
     */
    public function getTreeData()
    {
        if (!Auth::user()) {
            echo "login";
            return;
        }

        $login_user = Auth::user()->user;
        $users      = DB::select('select distinct user from template_2G');
        $arrUser    = array();
        $items      = array();
        $itArr      = array();
        if ($login_user === 'admin') {
            foreach ($users as $user) {
                $userStr       = $user->user;
                $templateNames = DB::table('template_2G')->where('user', '=', $userStr)->get();
                foreach ($templateNames as $templateName) {
                    array_push($arrUser, array("text" => $templateName->templateName, "id" => $templateName->id));
                }

                $items["text"]  = $userStr;
                $items["nodes"] = $arrUser;
                $arrUser        = array();
                array_push($itArr, $items);
            }
        } else {
            foreach ($users as $user) {
                if ($user->user === 'system') {
                    continue;
                } else if ($user->user == "admin" || $user->user == $login_user) {
                    $userStr       = $user->user;
                    $templateNames = DB::table('template_2G')->where('user', '=', $userStr)->get();
                    foreach ($templateNames as $templateName) {
                        array_push($arrUser, array("text" => $templateName->templateName, "id" => $templateName->id));
                    }

                    $items["text"]  = $userStr;
                    $items["nodes"] = $arrUser;
                    $arrUser        = array();
                    array_push($itArr, $items);
                }
            }
        }//end if

        for ($i = 0; $i < count($itArr); $i++) {
            $user = $itArr[$i]['text'];
            if ($user == "admin") {
                $itArr[$i]['text'] = "通用模板";
            } else if ($user == "system") {
                $itArr[$i]['text'] = "系统模板";
            } else {
                $nameCNSql         = DB::table('users')->where('user', '=', $user)->get();
                $itArr[$i]['text'] = $nameCNSql[0]->name;
            }
        }

        return response()->json($itArr);

    }//end getTreeData()


    /**
     * 检索指定模板
     *
     * @return mixed
     */
    public function searchGSMTreeData()
    {
        $inputData  = Input::get('inputData');
        $inputData  = "%".$inputData."%";
        $users      = DB::select('select distinct user from template_2G');
        $arrUser    = array();
        $items      = array();
        $itArr      = array();
        $login_user = Auth::user()->user;
        if ($login_user === 'admin') {
            foreach ($users as $user) {
                $userStr       = $user->user;
                $templateNames = DB::table('template_2G')->where('templateName', 'like', $inputData)->where('user', '=', $userStr)->get();
                foreach ($templateNames as $templateName) {
                    array_push($arrUser, array("text" => $templateName->templateName, "id" => $templateName->id));
                }

                $items["text"]  = $userStr;
                $items["nodes"] = $arrUser;
                $arrUser        = array();
                array_push($itArr, $items);
            }
        } else {
            foreach ($users as $user) {
                if ($user->user === 'system') {
                    continue;
                } else if ($user->user == "admin" || $user->user == $login_user) {
                    $userStr       = $user->user;
                    $templateNames = DB::table('template_2G')->where('templateName', 'like', $inputData)->where('user', '=', $userStr)->get();
                    foreach ($templateNames as $templateName) {
                        array_push($arrUser, array("text" => $templateName->templateName, "id" => $templateName->id));
                    }

                    $items["text"]  = $userStr;
                    $items["nodes"] = $arrUser;
                    $arrUser        = array();
                    array_push($itArr, $items);
                }
            }
        }//end if
        for ($i = 0; $i < count($itArr); $i++) {
            $user = $itArr[$i]['text'];
            if ($user == "admin") {
                $itArr[$i]['text'] = "通用模板";
            } else if ($user == "system") {
                $itArr[$i]['text'] = "系统模板";
            } else {
                $nameCNSql         = DB::table('users')->where('user', '=', $user)->get();
                $itArr[$i]['text'] = $nameCNSql[0]->name;
            }
        }

        return response()->json($itArr);

    }//end searchGSMTreeData()


    /**
     * 获得城市列表
     *
     * @return mixed
     */
    public function getAllCity()
    {
        $databaseConns = DB::select('select * from databaseconn_2G');
        $items         = array();
        foreach ($databaseConns as $databaseConn) {
            $city = '{"text":"'.$databaseConn->cityChinese.'","value":"'.$databaseConn->connName.'"}';
            array_push($items, $city);
        }

        return response()->json($items);

    }//end getAllCity()


    /**
     * 查询模板
     *
     * @return void
     */
    public function templateQuery()
    {
        $dbc = new DataBaseConnection();
        $db  = $dbc->getDB('mongs', 'mongs');
        if ($db == null) {
            $result["result"] = "false";
            $result["reason"] = "Failed to connect to database.";
            echo json_encode($result);
            return;
        }

        $citys       = json_decode(Input::get('city'), true);
        $startTime   = Input::get('startTime');
        $endTime     = Input::get('endTime');
        $timeDim     = Input::get('timeDim');
        $locationDim = Input::get('locationDim');
        $counters    = $this->loadCounters2G();
        $kpis        = $this->getKpis($db);

        $items      = array();
        $csvContent = "";
        foreach ($citys as $city) {
            //添加适配信息
            $url = explode("/", $_SERVER['REQUEST_URI'])[3];
            $filename = "common/txt/ControllerUserCheck.txt";
            $myfile = fopen($filename, "r") or die("Unable to open file!");
            $g = "Online";
            while (!feof($myfile)) {
                $arr = explode(",", fgets($myfile));
                if ($arr[0] === $url && $arr[1] === $city) {
                    $g = trim($arr[3]);
                    break;
                }
            }
            if ($g == "Online") {
                // $counters    = $this->loadCounters2G();
                // $kpis        = $this->getKpis($db);
                $sql      = "SELECT host,port,dbName,userName,password FROM databaseconn_2G 
                    WHERE connName = '".$city."'";
                $res      = $db->query($sql);
                $row      = $res->fetch(PDO::FETCH_ASSOC);
                $host     = $row['host'];
                $port     = $row['port'];
                $dbName   = $row['dbName'];
                $userName = $row['userName'];
                $password = $row['password'];
                $pmDbDSN  = "dblib:host=".$host.":".$port.";".((float)phpversion()>7.0?'dbName':'dbname')."=".$dbName;
                $pmDB     = new PDO($pmDbDSN, $userName, $password);
                if ($pmDB == null) {
                    $result["result"] = "false";
                    $result["reason"] = "Failed to connect to database.";
                    echo json_encode($result);
                    return;
                }
                $resultText  = "";
                $queryResult = $this->queryTemplate(
                    $db,
                    $pmDB,
                    $counters,
                    $timeDim,
                    $locationDim,
                    $startTime,
                    $endTime,
                    $city,
                    $resultText
                );
                foreach ($queryResult['records'] as $qr) {
                    $csvContent = $csvContent.implode(",", $qr)."\n";
                    array_push($items, $qr);
                }

                $result['text'] = $resultText.$kpis['names'];
                $pmDB           = null;
            } else {
                $localQuery = new localQuery();
                $counters    = $localQuery->loadCounters2G();
                $kpis        = $localQuery->getKpis($db);
                $dbc = new DataBaseConnection();
                $pmDB = $dbc->getDB("apg_sts", "apg_sts");

                $queryResult = $localQuery->queryTemplate(
                    $db,
                    $pmDB,
                    $counters,
                    $timeDim,
                    $locationDim,
                    $startTime,
                    $endTime,
                    $city,
                    $resultText
                );
                foreach ($queryResult['records'] as $qr) {
                    $csvContent = $csvContent.implode(",", $qr)."\n";
                    array_push($items, $qr);
                }

                $result['text'] = $resultText.$kpis['names'];
                $pmDB           = null;
            }     
        }//end foreach

        $result['total']   = count($items);
        $result['records'] = $items;
        $result['result']  = 'true';
        $template          = Input::get('template');
        $filename          = "common/files/".$template.date('YmdHis').".csv";
        $filename          = preg_replace('/[\\(\\)]/', '-', $filename);
        $this->resultToCSV2($result, $filename);
        $result['filename'] = $filename;
        echo json_encode($result);

    }//end templateQuery()


    /**
     * 获得2G计数器集
     *
     * @return array
     */
    protected function loadCounters2G()
    {
        $result = array();
        if (file_exists("common/txt/Counters_2G.txt")) {
            $result = $this->loadCountersFromFile();
        }

        return $result;

    }//end loadCounters_2G()


    /**
     * 获得2G计数器
     *
     * @return array
     */
    protected function loadCountersFromFile()
    {
        $result = array();
        $lines  = file("common/txt/Counters_2G.txt");
        foreach ($lines as $line) {
            $pair = explode("=", $line);
            $result[$pair[0]] = $pair[1];
        }

        return $result;

    }//end loadCountersFromFile()


    /**
     * 获得指标集合
     *
     * @param mixed $localDB 数据库连接句柄
     *
     * @return array
     */
    protected function getKpis($localDB)
    {
        $templateName = Input::get('template');
        $templateId = Input::get('templateId');
        //$queryKpiset  = "select elementId from `template_2G` 
        //                where templateName='$templateName'";
        $queryKpiset  = "select elementId from `template_2G` 
                        where id='$templateId'";
        $res          = $localDB->query($queryKpiset, PDO::FETCH_ASSOC);
        $kpis         = $res->fetchColumn();
        $queryKpiName = "select kpiName,instr('$kpis',id) as sort from kpiformula_2G 
                    where id in ($kpis) order by sort";
        $res          = $localDB->query($queryKpiName, PDO::FETCH_ASSOC);
        $kpiNames     = "";
        foreach ($res as $row) {
            $kpiNames = $kpiNames.$row['kpiName'].",";
        }

        $kpiNames        = substr($kpiNames, 0, (strlen($kpiNames) - 1));
        $result          = array();
        $result['ids']   = $kpis;
        $result['names'] = $kpiNames;
        return $result;

    }//end getKpis()


    /**
     * 模板查询
     *
     * @param mixed  $localDB     MYSQL数据库连接句柄
     * @param mixed  $pmDB        SYBASE数据库连接句柄
     * @param array  $counters    计数器集
     * @param string $timeDim     时间维度
     * @param string $locationDim 地域维度
     * @param string $startTime   起始时间
     * @param string $endTime     结束时间
     * @param string $city        城市
     * @param string $resultText  查询结果表头
     *
     * @return array
     */
    protected function queryTemplate($localDB, $pmDB, $counters, $timeDim,
        $locationDim, $startTime, $endTime, $city, &$resultText
    ) {
        $result         = array();
        $kpis           = $this->getKpis($localDB);
        $result['text'] = $kpis['names'];
        $sql            = $this->createSQL(
            $localDB,
            $pmDB,
            $kpis['ids'],
            $counters,
            $timeDim,
            $locationDim,
            $startTime,
            $endTime,
            $city,
            $resultText
        );
        // echo $sql;return;
        $result['records'] = $pmDB->query($sql, PDO::FETCH_ASSOC);
        return $result;

    }//end queryTemplate()


    /**
     * 创建SQL语句
     *
     * @param mixed  $localDB     MYSQL数据库连接句柄
     * @param mixed  $pmDB        SYBASE数据库连接句柄
     * @param array  $kpiName     KPI集合
     * @param array  $counters    COUNTER集合
     * @param string $timeDim     时间维度
     * @param string $locationDim 地域维度
     * @param string $startTime   起始时间
     * @param string $endTime     结束时间
     * @param string $city        城市
     * @param string $resultText  查询结果表头
     *
     * @return string
     */
    protected function createSQL($localDB, $pmDB, $kpiName, $counters, $timeDim,
        $locationDim, $startTime, $endTime, $city, &$resultText
    ) {

        $kpiset       = "(".$kpiName.")";
        $kpis         = "";
        $queryFormula = "select kpiName,kpiFormula,kpiPrecision,instr('$kpiName',id) as sort 
                    from kpiformula_2G where id in ".$kpiset." order by sort";
        $index        = 0;
        $selectSQL    = "SELECT";
        $counterMap   = array();
        foreach ($localDB->query($queryFormula) as $row) {
            $kpi = $row['kpiFormula'];
            $this->parserKPI($kpi, $counters, $counterMap);
            $formula = $row['kpiFormula'];
            $formula = "cast(".$this->formulaTransform($formula)." as decimal(18,".$row['kpiPrecision']."))";
            $kpis    = $kpis.$formula." as kpi".$index.",";
            $index++;
        }

        $kpis     = substr($kpis, 0, (strlen($kpis) - 1));
        $time_id  = "date_id";
        if ($timeDim == 'day' || $timeDim == 'hour' || $timeDim == 'hourgroup') {
            $timelevel = "HOUR";
        } else {
            $timelevel = "15MIN";
        }
        $whereSQL = " where $time_id>='$startTime' and $time_id<='$endTime' AND TIMELEVEL='$timelevel' ";

        $aggGroupSQL  = "";
        $aggSelectSQL = "";
        $aggOrderSQL  = "";
        $myGroup      = "";
        if ($timeDim == 'day') {
            if ($locationDim == 'erbs') {
                $selectSQL   = $selectSQL." DATETIME_ID,CONVERT(char,date_id) as day,";
                $aggGroupSQL = " GROUP BY day,DATETIME_ID,BSC,";
            } else {
                // $selectSQL   = $selectSQL." MOID,SESSION_ID,DATETIME_ID,CONVERT(char,date_id) as day,";
                $selectSQL   = $selectSQL." CONVERT(char,date_id) as day,";
                // $aggGroupSQL = " GROUP BY day,DATETIME_ID,SESSION_ID,MOID,";
                $aggGroupSQL = " GROUP BY day,";
            }

            $myGroup      = " GROUP BY AGG_TABLE0.day,";
            $aggSelectSQL = " SELECT AGG_TABLE0.day";
            $resultText   = $resultText."day,";
            $aggOrderSQL  = " ORDER BY AGG_TABLE0.day";
        } else if ($timeDim == 'hour') {
            if ($locationDim == 'erbs') {
                // $selectSQL   = $selectSQL." DATETIME_ID,CONVERT(char,date_id) as day, hour_id as hour,";
                // $aggGroupSQL = " GROUP BY day,hour,DATETIME_ID,BSC,";
                $selectSQL   = $selectSQL." CONVERT(char,date_id) as day, hour_id as hour,";
                $aggGroupSQL = " GROUP BY day,hour,BSC,";
            } else {
                // $selectSQL   = $selectSQL." MOID,SESSION_ID,DATETIME_ID,CONVERT(char,date_id) as day, hour_id as hour,";
                // $aggGroupSQL = " GROUP BY day,hour,DATETIME_ID,SESSION_ID,MOID,";
                $selectSQL   = $selectSQL." CONVERT(char,date_id) as day, hour_id as hour,";
                $aggGroupSQL = " GROUP BY day,hour,";
            }

            $myGroup      = " GROUP BY AGG_TABLE0.day,AGG_TABLE0.hour,";
            $aggSelectSQL = " SELECT AGG_TABLE0.day,AGG_TABLE0.hour";
            $resultText   = $resultText."day,hour,";
            $aggOrderSQL  = " ORDER BY AGG_TABLE0.day,AGG_TABLE0.hour";
        } else if ($timeDim == 'quarter') {
            if ($locationDim == 'erbs') {
                // $selectSQL   = $selectSQL." DATETIME_ID,CONVERT(char,date_id) as day, hour_id as hour, min_id as minute,";
                // $aggGroupSQL = " GROUP BY day,hour,minute,DATETIME_ID,BSC,";
                $selectSQL   = $selectSQL." CONVERT(char,date_id) as day, hour_id as hour, min_id as minute,";
                $aggGroupSQL = " GROUP BY day,hour,minute,BSC,";
            } else {
                // $selectSQL   = $selectSQL." MOID,SESSION_ID,DATETIME_ID,CONVERT(char,date_id) as day, hour_id as hour, min_id as minute,";
                // $aggGroupSQL = " GROUP BY day,hour,minute,DATETIME_ID,SESSION_ID,MOID,";
                $selectSQL   = $selectSQL." CONVERT(char,date_id) as day, hour_id as hour, min_id as minute,";
                $aggGroupSQL = " GROUP BY day,hour,minute,";
            }

            $myGroup      = " GROUP BY AGG_TABLE0.day,AGG_TABLE0.hour,AGG_TABLE0.minute,";
            $aggSelectSQL = "SELECT AGG_TABLE0.day,AGG_TABLE0.hour,AGG_TABLE0.minute";
            $resultText   = $resultText."day,hour,minute,";
            $aggOrderSQL  = " ORDER BY AGG_TABLE0.day,AGG_TABLE0.hour,AGG_TABLE0.minute";
        } else if ($timeDim == 'hourgroup') {
            if ($locationDim == 'erbs') {
                $hourcollection = Input::get('hour');
                // $selectSQL      = $selectSQL." DATETIME_ID,convert(char,date_id) as day,'".$hourcollection."' as hour,";
                // $aggGroupSQL    = " group by day,hour,DATETIME_ID,BSC,";
                $selectSQL      = $selectSQL." convert(char,date_id) as day,'".$hourcollection."' as hour,";
                $aggGroupSQL    = " group by day,hour,BSC,";
            } else {
                $hourcollection = Input::get('hour');
                // $selectSQL      = $selectSQL." MOID,SESSION_ID,DATETIME_ID,convert(char,date_id) as day,'".$hourcollection."' as hour,";
                // $aggGroupSQL    = " group by day,hour,DATETIME_ID,SESSION_ID,MOID,";
                $selectSQL      = $selectSQL." convert(char,date_id) as day,'".$hourcollection."' as hour,";
                $aggGroupSQL    = " group by day,";
            }

            $myGroup      = " GROUP BY AGG_TABLE0.day,AGG_TABLE0.hour,";
            $aggSelectSQL = " SELECT AGG_TABLE0.day,AGG_TABLE0.hour";
            $aggOrderSQL  = " order by AGG_TABLE0.day,AGG_TABLE0.hour";
            $resultText   = $resultText."day,hour,";
        }//end if

        if ($locationDim == 'city') {
            $selectSQL    = $selectSQL." '$city' as location,";
            $myGroup      = $myGroup." AGG_TABLE0.location";
            $aggGroupSQL  = $aggGroupSQL." location";
            $aggSelectSQL = $aggSelectSQL.",AGG_TABLE0.location,";
            $resultText   = $resultText."location,";
        } else if ($locationDim == 'cell') {
            $selectSQL    = $selectSQL." '$city' as location, CELL_NAME,";
            $myGroup      = $myGroup." AGG_TABLE0.location,AGG_TABLE0.CELL_NAME";
            $aggGroupSQL  = $aggGroupSQL." location,CELL_NAME";
            $aggSelectSQL = $aggSelectSQL.",AGG_TABLE0.location,AGG_TABLE0.CELL_NAME,";
            $resultText   = $resultText." location,CELL_NAME,";
        } else if ($locationDim == 'erbs') {
            $selectSQL    = $selectSQL." '$city' as location, BSC,";
            $myGroup      = $myGroup." AGG_TABLE0.location,AGG_TABLE0.BSC";
            $aggGroupSQL  = $aggGroupSQL." location";
            $aggSelectSQL = $aggSelectSQL.",AGG_TABLE0.location,AGG_TABLE0.BSC,";
            $resultText   = $resultText." location,BSC,";
        } else if ($locationDim == 'erbsGroup') {
            $selectSQL    = $selectSQL." '$city' as location,";
            $myGroup      = $myGroup." AGG_TABLE0.location";
            $aggGroupSQL  = $aggGroupSQL." location";
            $aggSelectSQL = $aggSelectSQL.",AGG_TABLE0.location,";
            $resultText   = $resultText." location,";
        } else if ($locationDim == 'cellGroup') {
            $selectSQL    = $selectSQL." '$city' as location,";
            $myGroup      = $myGroup." AGG_TABLE0.location";
            $aggGroupSQL  = $aggGroupSQL." location";
            $aggSelectSQL = $aggSelectSQL.",AGG_TABLE0.location,";
            $resultText   = $resultText." location,";
        }

        $inputErbs = Input::get('erbs');
        $erbs      = isset($inputErbs) ? $inputErbs : "";
        if ($locationDim == "erbs") {
            if ($erbs != '') {
                $erbs     = "('".str_replace(",", "','", $erbs)."')";
                $whereSQL = $whereSQL." and BSC in ".$erbs;
            }
        }

        $inputCell = Input::get('cell');
        $cell      = isset($inputCell) ? $inputCell : "";
        if ($cell != "" && $locationDim == "cell" || $locationDim == "cellGroup") {
            $cell     = "('".str_replace(",", "','", $cell)."')";
            $whereSQL = $whereSQL." and CELL_NAME in ".$cell;
        }

        $inputHour = Input::get('hour');
        $inputHour = ltrim($inputHour, '[');
        $inputHour = rtrim($inputHour, ']');
        $inputHour = ltrim($inputHour, '"');
        $inputHour = rtrim($inputHour, '"');
        $inputHour = str_replace('","', ',', $inputHour);
        $hour      = isset($inputHour) ? $inputHour : "";

        if ($hour != 'null' && ($timeDim == "hour" || $timeDim == "quarter")) {
            $hour     = "(".$hour.")";
            $whereSQL = $whereSQL." and hour_id in ".$hour;
        }

        if ($hour == 'null' && ($timeDim == "hour" || $timeDim == "quarter")) {
            $hour     = "(0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23)";
            $whereSQL = $whereSQL." and hour_id in ".$hour;
        }

        if ($hour == 'null' && $timeDim == "hourgroup") {
            $hour     = "(0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23)";
            $whereSQL = $whereSQL." and hour_id in ".$hour;
        }
        
        $inputMinute = Input::get('minute');
        $inputMinute = ltrim($inputMinute, '[');
        $inputMinute = rtrim($inputMinute, ']');
        $inputMinute = ltrim($inputMinute, '"');
        $inputMinute = rtrim($inputMinute, '"');
        $inputMinute = str_replace('","', ',', $inputMinute);
        $min         = isset($inputMinute) ? $inputMinute : "";
        if ($min != 'null' && $timeDim == "quarter") {
            $min      = "(".$min.")";
            $whereSQL = $whereSQL." and min_id in ".$min;
        }

        if ($min == 'null' && $timeDim == "quarter") {
            $min      = "(0,15,30,45)";
            $whereSQL = $whereSQL." and min_id in ".$min;
        }

        @$tables = array_keys(array_count_values($counterMap));
        $templateNameCheck = Input::get('template');
        
        if (count($tables) == 1) {
            $currTable = $tables[0];
            if (trim(substr($currTable, 0, (strlen($currTable) - 5))) == "DC_E_BSS_CELL_ADJ" && $templateNameCheck == 'gsm_to_gsm_ho_sts') {
                $aggSelectSQL = $aggSelectSQL."AGG_TABLE0.relation,";
                $selectSQL    = $selectSQL."CELL_NAME_NEIGHBOUR as relation,";
                $aggGroupSQL  = $aggGroupSQL.",relation";
                $resultText   = $resultText."CELL_NAME_NEIGHBOUR,"; 
                $myGroup      = $myGroup.",AGG_TABLE0.relation";
            }
            //添加邻区
        }

        $tempTableSQL = "";
        $index        = 0;

        foreach ($tables as $table) {
            $countersForQuery = array_keys($counterMap, $table);
            $tableSQL         = $this->createTempTable1(
                $selectSQL,
                $whereSQL,
                $table,
                $countersForQuery,
                $aggGroupSQL,
                $timeDim
            );
            $tableSQL         = $tableSQL."as AGG_TABLE$index ";
            if ($index == 0) {
                if ($index != (sizeof($tables) - 1)) {
                    $tableSQL = $tableSQL." left join";
                }
            } else {
                if ($timeDim == "day") {
                    if ($locationDim == 'erbs') {
                        // $tableSQL = $tableSQL."on AGG_TABLE0.day = AGG_TABLE$index.day 
                        //     and AGG_TABLE0.DATETIME_ID=AGG_TABLE$index.DATETIME_ID 
                        //     and AGG_TABLE0.BSC=AGG_TABLE$index.BSC";
                        $tableSQL = $tableSQL."on AGG_TABLE0.day = AGG_TABLE$index.day                         
                            and AGG_TABLE0.BSC=AGG_TABLE$index.BSC";
                    } elseif ($locationDim == 'cell') {
                        $tableSQL = $tableSQL."on AGG_TABLE0.day = AGG_TABLE$index.day                         
                            and AGG_TABLE0.CELL_NAME=AGG_TABLE$index.CELL_NAME";
                    } else {
                        // $tableSQL = $tableSQL."on AGG_TABLE0.day = AGG_TABLE$index.day 
                        //     and AGG_TABLE0.DATETIME_ID=AGG_TABLE$index.DATETIME_ID 
                        //     and AGG_TABLE0.SESSION_ID=AGG_TABLE$index.SESSION_ID 
                        //     and AGG_TABLE0.MOID=AGG_TABLE$index.MOID";
                        $tableSQL = $tableSQL."on AGG_TABLE0.day = AGG_TABLE$index.day";
                    }
                } else if ($timeDim == "hour") {
                    if ($locationDim == 'erbs') {
                        // $tableSQL = $tableSQL."on AGG_TABLE0.day = AGG_TABLE$index.day 
                        //     and AGG_TABLE0.hour = AGG_TABLE$index.hour
                        //     and AGG_TABLE0.DATETIME_ID=AGG_TABLE$index.DATETIME_ID 
                        //     and AGG_TABLE0.BSC=AGG_TABLE$index.BSC";
                        $tableSQL = $tableSQL."on AGG_TABLE0.day = AGG_TABLE$index.day 
                            and AGG_TABLE0.hour = AGG_TABLE$index.hour
                            and AGG_TABLE0.BSC=AGG_TABLE$index.BSC";
                    } elseif ($locationDim == 'cell') {
                        $tableSQL = $tableSQL."on AGG_TABLE0.day = AGG_TABLE$index.day  
                            and AGG_TABLE0.hour = AGG_TABLE$index.hour                       
                            and AGG_TABLE0.CELL_NAME=AGG_TABLE$index.CELL_NAME";
                    } else {
                        // $tableSQL = $tableSQL."on AGG_TABLE0.day = AGG_TABLE$index.day 
                        //     and AGG_TABLE0.hour = AGG_TABLE$index.hour
                        //     and AGG_TABLE0.DATETIME_ID=AGG_TABLE$index.DATETIME_ID 
                        //     and AGG_TABLE0.SESSION_ID=AGG_TABLE$index.SESSION_ID 
                        //     and AGG_TABLE0.MOID=AGG_TABLE$index.MOID";
                        $tableSQL = $tableSQL."on AGG_TABLE0.day = AGG_TABLE$index.day 
                            and AGG_TABLE0.hour = AGG_TABLE$index.hour";    
                    }
                } else if ($timeDim == "quarter") {
                    if ($locationDim == 'erbs') {
                        // $tableSQL = $tableSQL."on AGG_TABLE0.day = AGG_TABLE$index.day 
                        //     and AGG_TABLE0.hour = AGG_TABLE$index.hour 
                        //     and AGG_TABLE0.minute = AGG_TABLE$index.minute 
                        //     and AGG_TABLE0.DATETIME_ID=AGG_TABLE$index.DATETIME_ID 
                        //     and AGG_TABLE0.BSC=AGG_TABLE$index.BSC";
                        $tableSQL = $tableSQL."on AGG_TABLE0.day = AGG_TABLE$index.day 
                            and AGG_TABLE0.hour = AGG_TABLE$index.hour 
                            and AGG_TABLE0.minute = AGG_TABLE$index.minute
                            and AGG_TABLE0.BSC=AGG_TABLE$index.BSC";
                    } elseif ($locationDim == 'cell') {
                        $tableSQL = $tableSQL."on AGG_TABLE0.day = AGG_TABLE$index.day  
                            and AGG_TABLE0.hour = AGG_TABLE$index.hour  
                            and AGG_TABLE0.minute = AGG_TABLE$index.minute                     
                            and AGG_TABLE0.CELL_NAME=AGG_TABLE$index.CELL_NAME";
                    } else {
                        // $tableSQL = $tableSQL."on AGG_TABLE0.day = AGG_TABLE$index.day 
                        //     and AGG_TABLE0.hour = AGG_TABLE$index.hour 
                        //     and AGG_TABLE0.minute = AGG_TABLE$index.minute 
                        //     and AGG_TABLE0.DATETIME_ID=AGG_TABLE$index.DATETIME_ID 
                        //     and AGG_TABLE0.SESSION_ID=AGG_TABLE$index.SESSION_ID 
                        //     and AGG_TABLE0.MOID=AGG_TABLE$index.MOID";
                        $tableSQL = $tableSQL."on AGG_TABLE0.day = AGG_TABLE$index.day 
                            and AGG_TABLE0.hour = AGG_TABLE$index.hour 
                            and AGG_TABLE0.minute = AGG_TABLE$index.minute";
                    }
                } else if ($timeDim == "hourgroup") {
                    if ($locationDim == 'erbs') {
                        // $tableSQL = $tableSQL."on AGG_TABLE0.day = AGG_TABLE$index.day 
                        //     and AGG_TABLE0.hour = AGG_TABLE$index.hour 
                        //     and AGG_TABLE0.DATETIME_ID=AGG_TABLE$index.DATETIME_ID 
                        //     and AGG_TABLE0.BSC=AGG_TABLE$index.BSC";
                        $tableSQL = $tableSQL."on AGG_TABLE0.day = AGG_TABLE$index.day 
                            and AGG_TABLE0.hour = AGG_TABLE$index.hour";
                    } elseif ($locationDim == 'cell') {
                        $tableSQL = $tableSQL."on AGG_TABLE0.day = AGG_TABLE$index.day  
                            and AGG_TABLE0.hour = AGG_TABLE$index.hour";
                    } else {
                        // $tableSQL = $tableSQL."on AGG_TABLE0.day = AGG_TABLE$index.day 
                        //     and AGG_TABLE0.hour = AGG_TABLE$index.hour 
                        //     and AGG_TABLE0.DATETIME_ID=AGG_TABLE$index.DATETIME_ID 
                        //     and AGG_TABLE0.SESSION_ID=AGG_TABLE$index.SESSION_ID 
                        //     and AGG_TABLE0.MOID=AGG_TABLE$index.MOID";
                        $tableSQL = $tableSQL."on AGG_TABLE0.day = AGG_TABLE$index.day";
                    }
                }//end if

                if ($index != (sizeof($tables) - 1)) {
                    $tableSQL = $tableSQL." left join ";
                }
            }//end if
            $tempTableSQL = $tempTableSQL.$tableSQL;
            $index++;
        }//end foreach

        $sql = $aggSelectSQL.$kpis." FROM ".$tempTableSQL.$myGroup.$aggOrderSQL;
        return $sql;

    }//end createSQL()


    /**
     * 分解KPI
     *
     * @param string $kpi        指标公式
     * @param array  $counters   计数器集
     * @param array  $counterMap 计数器和表名映射
     *
     * @return void
     */
    protected function parserKPI($kpi, $counters, &$counterMap)
    {
        $pattern = "/[\(\)\+\*-\/]/";
        $columns = preg_split($pattern, $kpi);
        foreach ($columns as $column) {
            $column      = trim($column);
            $counterName = $column;
            @$table      = $counters[strtolower($counterName)];
            if (!array_key_exists($column, $counterMap)) {
                $counterMap[$column] = $table;
            }
        }

    }//end parserKPI()


    /**
     * 公式转换
     *
     * @param string $formula 指标公式
     *
     * @return mixed|string
     */
    protected function formulaTransform($formula)
    {
        if (strpos($formula, 'AVG') === 0 || strpos($formula, 'avg') === 0) {
            // $formula = "AVG(".$formula.")";
            return $formula;
        } else if (strpos($formula, '(') == false && strpos($formula, ')') == false) {
            $formula = "SUM(".$formula.")";
            return $formula;
        } else {
            $firStr  = '';
            $finStr  = '';
            $formula = preg_replace("/\s/", "", $formula);
            $firPos  = strpos($formula, '(');
            if ($firPos != 0) {
                $firStr  = substr($formula, 0, $firPos);
                $formula = substr($formula, $firPos);
            }

            $finPos = strrpos($formula, ')');
            if (($finPos + 1) != strlen($formula)) {
                $finStr  = substr($formula, ($finPos + 1));
                $formula = substr($formula, 0, ($finPos + 1));
            }

            $arr = [0];
            $sum = 0;
            for ($i = 0; $i < strlen($formula); $i++) {
                if ($formula[$i] == '(') {
                    $sum = ($sum + 1);
                }

                if ($formula[$i] == ')') {
                    $sum = ($sum - 1);
                }

                if ($sum == 0) {
                    array_push($arr, $i);
                }
            }

            $comStr = $this->formulaAddSum($arr, $formula);

            if (strlen($firStr) > 0 && strlen($finStr) == 0) {
                $comStr = $firStr.$comStr;
            } else if (strlen($firStr) == 0 && strlen($finStr) > 0) {
                $comStr = $comStr.$finStr;
            } else if (strlen($firStr) > 0 && strlen($finStr) > 0) {
                $comStr = $firStr.$comStr.$finStr;
            }

            return $comStr;
        }//end if

    }//end formulaTransform()


    /**
     * 公式SUM
     *
     * @param array  $arr     位置列表
     * @param string $formula 指标公式
     *
     * @return bool|string
     */
    protected function formulaAddSum($arr, $formula)
    {
        if ((count($arr) % 2) != 0 && count($arr) < 2) {
            return false;
        }

        $comStr = '';
        if (count($arr) == 2) {
            $comStr = "SUM".$formula;
        } else if (count($arr) > 2) {
            for ($i = 0; $i < (count($arr) - 1); $i++) {
                if (($i % 2) == 0 && $i == 0) {
                    $comStr = $comStr."SUM".substr($formula, $arr[$i], ($arr[($i + 1)] - $arr[$i] + 1));
                } else if (($i % 2) == 0 && $i != 0 && $i != (count($arr) - 2)) {
                    $comStr = $comStr."SUM".substr($formula, ($arr[$i] + 1), ($arr[($i + 1)] - $arr[$i]));
                }

                if (($i % 2) == 1) {
                    $comStr = $comStr.$formula[$arr[($i + 1)]];
                }

                if ($i == (count($arr) - 2)) {
                    $comStr = $comStr."SUM".substr($formula, ($arr[$i] + 1), ($arr[($i + 1)] - $arr[$i] + 1));
                }
            }
        }

        return $comStr;

    }//end formulaAddSum()


    /**
     * 创建临时表
     *
     * @param string $selectSQL SELECT字串
     * @param string $whereSQL  WHERE字串
     * @param string $tableName 表名
     * @param array  $counters  计数器集
     * @param string $groupSQL  GROUP字串
     * @param string $timeDim   时间维度
     *
     * @return string
     */
    protected function createTempTable1($selectSQL, $whereSQL, $tableName, $counters, $groupSQL, $timeDim)
    {
        foreach ($counters as $counter) {
            $counter     = trim($counter);
            $counterName = $counter;

            $selectSQL = $selectSQL." sum(".$counter.") as '$counterName',";
        }

        $selectSQL = substr($selectSQL, 0, (strlen($selectSQL) - 1));
        return "($selectSQL from dc.$tableName $whereSQL $groupSQL)";

    }//end createTempTable1()


    /**
     * 写入CSV文件
     *
     * @param array     $result   查询结果
     * 
     * @param $filename $filename CSV文件名
     *
     * @return void
     */
    protected function resultToCSV2($result, $filename)
    {
        $csvContent = mb_convert_encoding($result['text']."\n", 'gb2312', 'utf-8');
        $fp         = fopen($filename, "w");
        fwrite($fp, $csvContent);
        foreach ($result['records'] as $row) {
            fputcsv($fp, $row);
        }

        fclose($fp);

    }//end resultToCSV2()


    /**
     * 检索指定模板
     *
     * @return void
     */
    public function getElementTree()
    {
        $templateName = input::get('templateName');
     
        $elementId    = Template_2G::select('elementId')->where('id',$templateName)->get();
        echo json_encode($elementId[0]);

    }//end getElementTree()


    /**
     * 获得KPI名
     *
     * @return void
     */
    public function getKpiNamebyId()
    {
        $idarr = explode(',', input::get("id"));
        $items = array();
        foreach ($idarr as $id) {
            $row = Kpiformula2G::select()->where('id',$id)->get();
            if ($row) {
                $data['text']    = $row[0]['kpiName'];
                $data['id']      = $row[0]['id'];
                $data['user']    = $row[0]['user'];
                $data['formula'] = $row[0]['kpiFormula'];
                array_push($items, $data);
            }
        }

        echo json_encode($items);

    }//end getKpiNamebyId()


    /**
     * 转换内部计数器
     *
     * @param string $counterName 计数器名
     * @param int    $index       向量值
     *
     * @return mixed
     */
    protected function convertInternalCounter($counterName, $index)
    {
        $SQL = "sum(case DCVECTOR_INDEX when $index then $counterName else 0 end)";
        return str_replace("\n", "", $SQL);

    }//end convertInternalCounter()


    /**
     * 获得城市名
     *
     * @param string $city 城市信息
     *
     * @return array
     */
    protected function parseCity($city)
    {
        $result = array();
        foreach ($city as $cityRow) {
            if ($cityRow['checked'] === true) {
                $result[] = $cityRow['text'];
            }
        }

        return $result;

    }//end parseCity()


}//end class

class localQuery 
{
    /**
     * 获得2G计数器
     *
     * @return array
     */
    protected function loadCountersFromFile()
    {
        $result = array();
        // $lines  = file("common/txt/Counters_2G.txt");
        $lines = file("common/txt/QY2GCounters.txt");
        foreach ($lines as $line) {
            $pair = explode("=", $line);
            $result[$pair[0]] = $pair[1];
        }

        return $result;

    }//end loadCountersFromFile()

    /**
     * 获得2G计数器集
     *
     * @return array
     */
    public function loadCounters2G()
    {
        $result = array();
        // if (file_exists("common/txt/Counters_2G.txt")) {
        if (file_exists("common/txt/QY2GCounters.txt")) {
            $result = $this->loadCountersFromFile();
        }

        return $result;

    }//end loadCounters_2G()

    /**
     * 模板查询
     *
     * @param mixed  $localDB     MYSQL数据库连接句柄
     * @param mixed  $pmDB        SYBASE数据库连接句柄
     * @param array  $counters    计数器集
     * @param string $timeDim     时间维度
     * @param string $locationDim 地域维度
     * @param string $startTime   起始时间
     * @param string $endTime     结束时间
     * @param string $city        城市
     * @param string $resultText  查询结果表头
     *
     * @return array
     */
    public function queryTemplate($localDB, $pmDB, $counters, $timeDim,
        $locationDim, $startTime, $endTime, $city, &$resultText
    ) {
        $resultText = '';
        $result         = array();
        $kpis           = $this->getKpis($localDB);
        $result['text'] = $kpis['names'];
        $sql            = $this->createSQL(
            $localDB,
            $pmDB,
            $kpis['ids'],
            $counters,
            $timeDim,
            $locationDim,
            $startTime,
            $endTime,
            $city,
            $resultText
        );
        // print_r($sql);return;
        $result['records'] = $pmDB->query($sql, PDO::FETCH_ASSOC);
        return $result;

    }//end queryTemplate()

    /**
     * 分解KPI
     *
     * @param string $kpi        指标公式
     * @param array  $counters   计数器集
     * @param array  $counterMap 计数器和表名映射
     *
     * @return void
     */
    protected function parserKPI($kpi, $counters, &$counterMap)
    {
        $pattern = "/[\(\)\+\*-\/]/";
        $columns = preg_split($pattern, $kpi);
        foreach ($columns as $column) {
            $column      = trim($column);
            $counterName = $column;
            // @$table      = $counters[strtolower($counterName)];
            @$table      = $counters[strtoupper($counterName)];
            if (!array_key_exists($column, $counterMap)) {
                $counterMap[$column] = $table;
            }
        }

    }//end parserKPI()

    /**
     * 公式转换
     *
     * @param string $formula 指标公式
     *
     * @return mixed|string
     */
    protected function formulaTransform($formula)
    {
        if (strpos($formula, '(') == false && strpos($formula, ')') == false) {
            $formula = "SUM(".$formula.")";
            return $formula;
        } else {
            $firStr  = '';
            $finStr  = '';
            $formula = preg_replace("/\s/", "", $formula);
            $firPos  = strpos($formula, '(');
            if ($firPos != 0) {
                $firStr  = substr($formula, 0, $firPos);
                $formula = substr($formula, $firPos);
            }

            $finPos = strrpos($formula, ')');
            if (($finPos + 1) != strlen($formula)) {
                $finStr  = substr($formula, ($finPos + 1));
                $formula = substr($formula, 0, ($finPos + 1));
            }

            $arr = [0];
            $sum = 0;
            for ($i = 0; $i < strlen($formula); $i++) {
                if ($formula[$i] == '(') {
                    $sum = ($sum + 1);
                }

                if ($formula[$i] == ')') {
                    $sum = ($sum - 1);
                }

                if ($sum == 0) {
                    array_push($arr, $i);
                }
            }

            $comStr = $this->formulaAddSum($arr, $formula);

            if (strlen($firStr) > 0 && strlen($finStr) == 0) {
                $comStr = $firStr.$comStr;
            } else if (strlen($firStr) == 0 && strlen($finStr) > 0) {
                $comStr = $comStr.$finStr;
            } else if (strlen($firStr) > 0 && strlen($finStr) > 0) {
                $comStr = $firStr.$comStr.$finStr;
            }

            return $comStr;
        }//end if

    }//end formulaTransform()

    /**
     * 公式SUM
     *
     * @param array  $arr     位置列表
     * @param string $formula 指标公式
     *
     * @return bool|string
     */
    protected function formulaAddSum($arr, $formula)
    {
        if ((count($arr) % 2) != 0 && count($arr) < 2) {
            return false;
        }

        $comStr = '';
        if (count($arr) == 2) {
            $comStr = "SUM".$formula;
        } else if (count($arr) > 2) {
            for ($i = 0; $i < (count($arr) - 1); $i++) {
                if (($i % 2) == 0 && $i == 0) {
                    $comStr = $comStr."SUM".substr($formula, $arr[$i], ($arr[($i + 1)] - $arr[$i] + 1));
                } else if (($i % 2) == 0 && $i != 0 && $i != (count($arr) - 2)) {
                    $comStr = $comStr."SUM".substr($formula, ($arr[$i] + 1), ($arr[($i + 1)] - $arr[$i]));
                }

                if (($i % 2) == 1) {
                    $comStr = $comStr.$formula[$arr[($i + 1)]];
                }

                if ($i == (count($arr) - 2)) {
                    $comStr = $comStr."SUM".substr($formula, ($arr[$i] + 1), ($arr[($i + 1)] - $arr[$i] + 1));
                }
            }
        }

        return $comStr;

    }//end formulaAddSum()

    /**
     * 创建临时表
     *
     * @param string $selectSQL SELECT字串
     * @param string $whereSQL  WHERE字串
     * @param string $tableName 表名
     * @param array  $counters  计数器集
     * @param string $groupSQL  GROUP字串
     * @param string $timeDim   时间维度
     *
     * @return string
     */
    protected function createTempTable1($selectSQL, $whereSQL, $tableName, $counters, $groupSQL, $timeDim)
    {
        foreach ($counters as $counter) {
            $counter     = trim($counter);
            $counterName = $counter;

            $selectSQL = $selectSQL." sum(".$counter.") as '$counterName',";
        }

        $selectSQL = substr($selectSQL, 0, (strlen($selectSQL) - 1));
        // return "($selectSQL from dc.$tableName $whereSQL $groupSQL)";
        return "($selectSQL from $tableName $whereSQL";
    }//end createTempTable1()

    /**
     * 创建SQL语句
     *
     * @param mixed  $localDB     MYSQL数据库连接句柄
     * @param mixed  $pmDB        SYBASE数据库连接句柄
     * @param array  $kpiName     KPI集合
     * @param array  $counters    COUNTER集合
     * @param string $timeDim     时间维度
     * @param string $locationDim 地域维度
     * @param string $startTime   起始时间
     * @param string $endTime     结束时间
     * @param string $city        城市
     * @param string $resultText  查询结果表头
     *
     * @return string
     */
    protected function createSQL($localDB, $pmDB, $kpiName, $counters, $timeDim,
        $locationDim, $startTime, $endTime, $city, &$resultText
    ) {

        $kpiset       = "(".$kpiName.")";
        $kpis         = "";
        $queryFormula = "select kpiName,kpiFormula,kpiPrecision,instr('$kpiName',id) as sort 
                    from kpiformula_2G where id in ".$kpiset." order by sort";
        $index        = 0;
        $selectSQL    = "SELECT";
        $counterMap   = array();
        foreach ($localDB->query($queryFormula) as $row) {
            $kpi = $row['kpiFormula'];
            $this->parserKPI($kpi, $counters, $counterMap);
            $formula = $row['kpiFormula'];
            $formula = "cast(".$this->formulaTransform($formula)." as decimal(18,".$row['kpiPrecision']."))";
            $kpis    = $kpis.$formula." as kpi".$index.",";
            $index++;
        }

        $kpis     = substr($kpis, 0, (strlen($kpis) - 1));
        $time_id  = "date_id";
        // $time_id = "DATE_FORMAT(DATETIME_ID, '%Y-%m-%d')";
        $whereSQL = " where $time_id>='$startTime' and $time_id<='$endTime'";

        $aggGroupSQL  = "";
        $aggSelectSQL = "";
        $aggOrderSQL  = "";
        $myGroup      = "";
        if ($timeDim == 'day') {
            if ($locationDim == 'erbs') {
                // $selectSQL   = $selectSQL." DATETIME_ID,CONVERT(char,date_id) as day,";
                // $selectSQL   = $selectSQL." DATE_FORMAT(DATETIME_ID, '%Y-%m-%d') AS DATETIME_ID,";
                $selectSQL   = $selectSQL." date_id AS DATETIME_ID,";
                $aggGroupSQL = " GROUP BY DATETIME_ID,BSC,";
            } else {
                // $selectSQL   = $selectSQL." MOID,SESSION_ID,DATETIME_ID,CONVERT(char,date_id) as day,";
                // $selectSQL   = $selectSQL." DATE_FORMAT(DATETIME_ID, '%Y-%m-%d') AS DATETIME_ID, MO,";
                $selectSQL   = $selectSQL." date_id AS DATETIME_ID, MO,";
                $aggGroupSQL = " GROUP BY DATETIME_ID,MO,";
            }

            // $myGroup      = " GROUP BY AGG_TABLE0.day,";
            $myGroup      = " GROUP BY AGG_TABLE0.DATETIME_ID,";
            // $aggSelectSQL = " SELECT AGG_TABLE0.day";
            $aggSelectSQL = " SELECT AGG_TABLE0.DATETIME_ID";
            $resultText   = $resultText."day,";
            // $aggOrderSQL  = " ORDER BY AGG_TABLE0.day";
            $aggOrderSQL  = " ORDER BY AGG_TABLE0.DATETIME_ID";
        } else if ($timeDim == 'hour') {
            if ($locationDim == 'erbs') {
                // $selectSQL   = $selectSQL." DATETIME_ID,CONVERT(char,date_id) as day, hour_id as hour,";
                // $selectSQL   = $selectSQL." DATE_FORMAT(DATETIME_ID, '%Y-%m-%d') AS DATETIME_ID, DATE_FORMAT(DATETIME_ID, '%H') as hour,";
                $selectSQL   = $selectSQL." date_id AS DATETIME_ID, hour_id as hour,";
                // $aggGroupSQL = " GROUP BY day,hour,DATETIME_ID,BSC,";
                $aggGroupSQL = " GROUP BY hour,DATETIME_ID,BSC,";
            } else {
                // $selectSQL   = $selectSQL." MOID,SESSION_ID,DATETIME_ID,CONVERT(char,date_id) as day, hour_id as hour,";
                // $selectSQL   = $selectSQL." DATE_FORMAT(DATETIME_ID, '%Y-%m-%d') AS DATETIME_ID,MO, DATE_FORMAT(DATETIME_ID, '%H') as hour,";
                $selectSQL   = $selectSQL." date_id AS DATETIME_ID,MO, hour_id as hour,";
                // $aggGroupSQL = " GROUP BY day,hour,DATETIME_ID,SESSION_ID,MOID,";
                $aggGroupSQL = " GROUP BY hour,DATETIME_ID,MO,";
            }

            // $myGroup      = " GROUP BY AGG_TABLE0.day,AGG_TABLE0.hour,";
            $myGroup      = " GROUP BY AGG_TABLE0.DATETIME_ID,AGG_TABLE0.hour,";
            $aggSelectSQL = " SELECT AGG_TABLE0.DATETIME_ID,AGG_TABLE0.hour";
            $resultText   = $resultText."day,hour,";
            $aggOrderSQL  = " ORDER BY AGG_TABLE0.DATETIME_ID,AGG_TABLE0.hour";
        } else if ($timeDim == 'quarter') {
            if ($locationDim == 'erbs') {
                $selectSQL   = $selectSQL." DATETIME_ID,CONVERT(char,date_id) as day, hour_id as hour, min_id as minute,";
                $aggGroupSQL = " GROUP BY day,hour,minute,DATETIME_ID,BSC,";
            } else {
                $selectSQL   = $selectSQL." MOID,SESSION_ID,DATETIME_ID,CONVERT(char,date_id) as day, hour_id as hour, min_id as minute,";
                $aggGroupSQL = " GROUP BY day,hour,minute,DATETIME_ID,SESSION_ID,MOID,";
            }

            $myGroup      = " GROUP BY AGG_TABLE0.day,AGG_TABLE0.hour,AGG_TABLE0.minute,";
            $aggSelectSQL = "SELECT AGG_TABLE0.day,AGG_TABLE0.hour,AGG_TABLE0.minute";
            $resultText   = $resultText."day,hour,minute,";
            $aggOrderSQL  = " ORDER BY AGG_TABLE0.day,AGG_TABLE0.hour,AGG_TABLE0.minute";
        } else if ($timeDim == 'hourgroup') {
            if ($locationDim == 'erbs') {
                $inputHour = Input::get('hour');
                // $selectSQL      = $selectSQL." DATETIME_ID,convert(char,date_id) as day,'".$hourcollection."' as hour,";
                // $inputHour = Input::get('hour');
                $inputHour = ltrim($inputHour, '[');
                $inputHour = rtrim($inputHour, ']');
                $inputHour = ltrim($inputHour, '"');
                $inputHour = rtrim($inputHour, '"');
                $inputHour = str_replace('","', ',', $inputHour);
                $hour      = isset($inputHour) ? $inputHour : "";
                // $selectSQL      = $selectSQL." DATE_FORMAT(DATETIME_ID, '%Y-%m-%d') AS DATETIME_ID,'".$inputHour."' as hour,";
                $selectSQL      = $selectSQL." date_id AS DATETIME_ID,'".$inputHour."' as hour,";
                $aggGroupSQL    = " group by hour,DATETIME_ID,BSC,";
            } else {
                $inputHour = Input::get('hour');
                // $inputHour = Input::get('hour');
                $inputHour = ltrim($inputHour, '[');
                $inputHour = rtrim($inputHour, ']');
                $inputHour = ltrim($inputHour, '"');
                $inputHour = rtrim($inputHour, '"');
                $inputHour = str_replace('","', ',', $inputHour);
                $hour      = isset($inputHour) ? $inputHour : "";
                // $selectSQL      = $selectSQL." DATE_FORMAT(DATETIME_ID, '%Y-%m-%d') AS DATETIME_ID,'".$inputHour."' as hour,";
                $selectSQL      = $selectSQL." date_id AS DATETIME_ID,'".$inputHour."' as hour,";
                $aggGroupSQL    = " group by hour,DATETIME_ID,";
            }

            // $myGroup      = " GROUP BY AGG_TABLE0.day,AGG_TABLE0.hour,";
            $myGroup      = " GROUP BY AGG_TABLE0.DATETIME_ID,AGG_TABLE0.hour,";
            $aggSelectSQL = " SELECT AGG_TABLE0.DATETIME_ID,AGG_TABLE0.hour";
            $aggOrderSQL  = " order by AGG_TABLE0.DATETIME_ID,AGG_TABLE0.hour";
            $resultText   = $resultText."day,hour,";
        }//end if

        if ($locationDim == 'city') {
            $selectSQL    = $selectSQL." '$city' as location,";
            $myGroup      = $myGroup." AGG_TABLE0.location";
            $aggGroupSQL  = $aggGroupSQL." location";
            $aggSelectSQL = $aggSelectSQL.",AGG_TABLE0.location,";
            $resultText   = $resultText."location,";
        } else if ($locationDim == 'cell') {
            // $selectSQL    = $selectSQL." '$city' as location, CELL_NAME,";
            // $myGroup      = $myGroup." AGG_TABLE0.location,AGG_TABLE0.CELL_NAME";
            // $aggGroupSQL  = $aggGroupSQL." location,CELL_NAME";
            // $aggSelectSQL = $aggSelectSQL.",AGG_TABLE0.location,AGG_TABLE0.CELL_NAME,";
            // $resultText   = $resultText." location,CELL_NAME,";
            if ($timeDim == "hourgroup") {
                $selectSQL    = $selectSQL." '$city' as location, MO,";
            } else {
                $selectSQL    = $selectSQL." '$city' as location,";
            }
            
            $myGroup      = $myGroup." AGG_TABLE0.location,AGG_TABLE0.MO";

            $aggGroupSQL  = $aggGroupSQL." location";
            $aggSelectSQL = $aggSelectSQL.",AGG_TABLE0.location,AGG_TABLE0.MO,";
            $resultText   = $resultText." location,CELL_NAME,";
        } else if ($locationDim == 'erbs') {
            $selectSQL    = $selectSQL." '$city' as location, BSC,";
            $myGroup      = $myGroup." AGG_TABLE0.location,AGG_TABLE0.BSC";
            $aggGroupSQL  = $aggGroupSQL." location";
            $aggSelectSQL = $aggSelectSQL.",AGG_TABLE0.location,AGG_TABLE0.BSC,";
            $resultText   = $resultText." location,BSC,";
        }

        $inputErbs = Input::get('erbs');
        $erbs      = isset($inputErbs) ? $inputErbs : "";
        if ($locationDim == "erbs") {
            if ($erbs != '') {
                $erbs     = "('".str_replace(",", "','", $erbs)."')";
                $whereSQL = $whereSQL." and BSC in ".$erbs;
            }
        }

        $inputCell = Input::get('cell');
        $cell      = isset($inputCell) ? $inputCell : "";
        if ($cell != "" && $locationDim == "cell" || $locationDim == "cellGroup") {
            $cell     = "('".str_replace(",", "','", $cell)."')";
            $whereSQL = $whereSQL." and MO in ".$cell;
        }

        $inputHour = Input::get('hour');
        $inputHour = ltrim($inputHour, '[');
        $inputHour = rtrim($inputHour, ']');
        $inputHour = ltrim($inputHour, '"');
        $inputHour = rtrim($inputHour, '"');
        $inputHour = str_replace('","', ',', $inputHour);
        $hour      = isset($inputHour) ? $inputHour : "";
        if ($hour != "" && ($timeDim == "hourgroup" || $timeDim == "hour" || $timeDim == "quarter")) {
            $hour     = "(".$hour.")";
            // $whereSQL = $whereSQL." and DATE_FORMAT(DATETIME_ID, '%H') in ".$hour;
            $whereSQL = $whereSQL." and hour_id in ".$hour;
        }

        /*if ($hour != "" && $timeDim == "hourgroup") {
            $hour     = "(".$hour.")";
            $whereSQL = $whereSQL." and DATE_FORMAT(DATETIME_ID, '%H') in ".$hour;
        }*/

        $inputMinute = Input::get('minute');
        $inputMinute = ltrim($inputMinute, '[');
        $inputMinute = rtrim($inputMinute, ']');
        $inputMinute = ltrim($inputMinute, '"');
        $inputMinute = rtrim($inputMinute, '"');
        $inputMinute = str_replace('","', ',', $inputMinute);
        $min         = isset($inputMinute) ? $inputMinute : "";
        if ($min != "" && $timeDim == "quarter") {
            $min      = "(".$min.")";
            $whereSQL = $whereSQL." and min_id in ".$min;
        }

        @$tables = array_keys(array_count_values($counterMap));

        $tempTableSQL = "";
        $index        = 0;

        foreach ($tables as $table) {
            $countersForQuery = array_keys($counterMap, $table);
            $tableSQL         = $this->createTempTable1(
                $selectSQL,
                $whereSQL,
                $table,
                $countersForQuery,
                $aggGroupSQL,
                $timeDim
            );
            if ($index == 0) {
            $tableSQL         = $tableSQL.$aggGroupSQL.")as AGG_TABLE$index ";
        } else {
            $tableSQL         = $tableSQL.")as AGG_TABLE$index ";
        }
            if ($index == 0) {
                if ($index != (sizeof($tables) - 1)) {
                    $tableSQL = $tableSQL." left join";
                }
            } else {
                if ($timeDim == "day") {
                    if ($locationDim == 'erbs') {
                        $tableSQL = $tableSQL."on AGG_TABLE0.DATETIME_ID=AGG_TABLE$index.DATETIME_ID 
                            and AGG_TABLE0.BSC=AGG_TABLE$index.BSC";
                    } else {
                        // $tableSQL = $tableSQL."on AGG_TABLE0.day = AGG_TABLE$index.day 
                        //     and AGG_TABLE0.DATETIME_ID=AGG_TABLE$index.DATETIME_ID 
                        //     and AGG_TABLE0.SESSION_ID=AGG_TABLE$index.SESSION_ID 
                        //     and AGG_TABLE0.MOID=AGG_TABLE$index.MOID";
                        $tableSQL = $tableSQL."on AGG_TABLE0.DATETIME_ID=AGG_TABLE$index.DATETIME_ID 
                            and AGG_TABLE0.MO=AGG_TABLE$index.MO";
                    }
                } else if ($timeDim == "hour") {
                    if ($locationDim == 'erbs') {
                        // $tableSQL = $tableSQL."on AGG_TABLE0.day = AGG_TABLE$index.day 
                        //     and AGG_TABLE0.hour = AGG_TABLE$index.hour
                        //     and AGG_TABLE0.DATETIME_ID=AGG_TABLE$index.DATETIME_ID 
                        //     and AGG_TABLE0.BSC=AGG_TABLE$index.BSC";
                        $tableSQL = $tableSQL."on AGG_TABLE0.DATETIME_ID = AGG_TABLE$index.DATETIME_ID 
                            and AGG_TABLE0.hour = AGG_TABLE$index.hour
                            and AGG_TABLE0.BSC=AGG_TABLE$index.BSC";
                    } else {
                        // $tableSQL = $tableSQL."on AGG_TABLE0.day = AGG_TABLE$index.day 
                        //     and AGG_TABLE0.hour = AGG_TABLE$index.hour
                        //     and AGG_TABLE0.DATETIME_ID=AGG_TABLE$index.DATETIME_ID 
                        //     and AGG_TABLE0.SESSION_ID=AGG_TABLE$index.SESSION_ID 
                        //     and AGG_TABLE0.MOID=AGG_TABLE$index.MOID";
                        $tableSQL = $tableSQL."on AGG_TABLE0.hour = AGG_TABLE$index.hour
                            and AGG_TABLE0.DATETIME_ID=AGG_TABLE$index.DATETIME_ID";
                    }
                } else if ($timeDim == "quarter") {
                    if ($locationDim == 'erbs') {
                        $tableSQL = $tableSQL."on AGG_TABLE0.day = AGG_TABLE$index.day 
                            and AGG_TABLE0.hour = AGG_TABLE$index.hour 
                            and AGG_TABLE0.minute = AGG_TABLE$index.minute 
                            and AGG_TABLE0.DATETIME_ID=AGG_TABLE$index.DATETIME_ID 
                            and AGG_TABLE0.BSC=AGG_TABLE$index.BSC";
                    } else {
                        $tableSQL = $tableSQL."on AGG_TABLE0.day = AGG_TABLE$index.day 
                            and AGG_TABLE0.hour = AGG_TABLE$index.hour 
                            and AGG_TABLE0.minute = AGG_TABLE$index.minute 
                            and AGG_TABLE0.DATETIME_ID=AGG_TABLE$index.DATETIME_ID 
                            and AGG_TABLE0.SESSION_ID=AGG_TABLE$index.SESSION_ID 
                            and AGG_TABLE0.MOID=AGG_TABLE$index.MOID";
                    }
                } else if ($timeDim == "hourgroup") {
                    if ($locationDim == 'erbs') {
                        // $tableSQL = $tableSQL."on AGG_TABLE0.day = AGG_TABLE$index.day 
                        //     and AGG_TABLE0.hour = AGG_TABLE$index.hour 
                        //     and AGG_TABLE0.DATETIME_ID=AGG_TABLE$index.DATETIME_ID 
                        //     and AGG_TABLE0.BSC=AGG_TABLE$index.BSC";
                        $tableSQL = $tableSQL."on AGG_TABLE0.hour = AGG_TABLE$index.hour 
                            and AGG_TABLE0.DATETIME_ID=AGG_TABLE$index.DATETIME_ID 
                            and AGG_TABLE0.BSC=AGG_TABLE$index.BSC";
                    } else {
                        // $tableSQL = $tableSQL."on AGG_TABLE0.day = AGG_TABLE$index.day 
                        //     and AGG_TABLE0.hour = AGG_TABLE$index.hour 
                        //     and AGG_TABLE0.DATETIME_ID=AGG_TABLE$index.DATETIME_ID 
                        //     and AGG_TABLE0.SESSION_ID=AGG_TABLE$index.SESSION_ID 
                        //     and AGG_TABLE0.MOID=AGG_TABLE$index.MOID";
                        $tableSQL = $tableSQL."on AGG_TABLE0.hour = AGG_TABLE$index.hour 
                            and AGG_TABLE0.DATETIME_ID=AGG_TABLE$index.DATETIME_ID";
                    }
                }//end if

                if ($index != (sizeof($tables) - 1)) {
                    $tableSQL = $tableSQL." left join ";
                }
            }//end if
            $tempTableSQL = $tempTableSQL.$tableSQL;
            $index++;
        }//end foreach

        $sql = $aggSelectSQL.$kpis." FROM ".$tempTableSQL.$myGroup.$aggOrderSQL;
        return $sql;

    }//end createSQL()


    /**
     * 获得指标集合
     *
     * @param mixed $localDB 数据库连接句柄
     *
     * @return array
     */
    public function getKpis($localDB)
    {
        $templateName = Input::get('template');
        $templateId = Input::get('templateId');
        //$queryKpiset  = "select elementId from `template_2G` 
        //                where templateName='$templateName'";
        $queryKpiset  = "select elementId from `template_2G` 
                        where id='$templateId'";
        $res          = $localDB->query($queryKpiset, PDO::FETCH_ASSOC);
        $kpis         = $res->fetchColumn();
        $queryKpiName = "select kpiName,instr('$kpis',id) as sort from kpiformula_2G 
                    where id in ($kpis) order by sort";
        $res          = $localDB->query($queryKpiName, PDO::FETCH_ASSOC);
        $kpiNames     = "";
        foreach ($res as $row) {
            $kpiNames = $kpiNames.$row['kpiName'].",";
        }

        $kpiNames        = substr($kpiNames, 0, (strlen($kpiNames) - 1));
        $result          = array();
        $result['ids']   = $kpis;
        $result['names'] = $kpiNames;
        return $result;

    }//end getKpis()
}
