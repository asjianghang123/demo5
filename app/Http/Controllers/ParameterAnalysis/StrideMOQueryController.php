<?php

/**
 * StrideMOQueryController.php
 *
 * @category ParameterAnalysis
 * @package  App\Http\Controllers\ParameterAnalysis
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
namespace App\Http\Controllers\ParameterAnalysis;

use App\Http\Controllers\Common\DataBaseConnection;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Utils\FileUtil;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use PDO;
use Config;
use App\Models\Mongs\Task;
use App\Models\Kget\TempMecontextParameterValue;
use App\Models\Mongs\MOsMeContexts;

/**
 * 常用参数查询
 * Class StrideMOQueryController
 *
 * @category ParameterAnalysis
 * @package  App\Http\Controllers\ParameterAnalysis
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class StrideMOQueryController extends Controller
{
    /**
     * 获得kget数据库列表
     *
     * @return string
     */
    public function getData()
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
        return json_encode($items);//需要通过response返回响应数据

    }

    /**
     * 获取文件内容入库
     *
     * @return string 入库结果
     */
    public function getFileContent()
    {
        $date = Input::get("date");
        $fileName = Input::get('fileName');
        $fileUtil = new FileUtil();
        $result = $fileUtil->parseFile($fileName);
        $len_result = count($result);
        if ($len_result == 0) {
            echo '没有任何数据！';
            exit;
        }
        MOsMeContexts::delete();
        $data_values = '';
        for ($i = 1; $i < $len_result; $i++) {
            $MeContext = $result[$i][0];
            $data_values .= "('$MeContext'),";

        }
        $data_values = mb_convert_encoding(substr($data_values, 0, -1), 'UTF-8', 'GBK');//解析文件编码是UTF-8无需转码
        $MOsMeContexts = new MOsMeContexts;
        $MOsMeContexts->meContext = $data_values;
        $MOsMeContexts->save();
        $query = $db->query("call meContext_param_pro('" . $date . "');");
        if ($query) {
            echo 'true';
        } else {
            echo 'false';
        }
    }

    /**
     * 导入文件
     *
     * @return void
     */
    public function insertFile()
    {
        $cellInput = input::get("cellInput");
        $cellInput = $this->check_input($cellInput);
        $date = Input::get("date");
        $date = $this->check_input($date);
        $cellArr = explode(',', $cellInput);
        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('mongs', 'mongs');
        MOsMeContexts::on()->delete();
        $data_values = '';
        for ($i = 0; $i < count($cellArr); $i++) {
            $MeContext = $cellArr[$i];
            $data_values .= "('$MeContext'),";

        }
        $data_values = mb_convert_encoding(substr($data_values, 0, -1), 'UTF-8', 'GBK');//解析文件编码是UTF-8无需转码
        $MOsMeContexts = new MOsMeContexts;
        $MOsMeContexts->meContext = $data_values;
        $MOsMeContexts->save();
        $query = $db->query("call meContext_param_pro('" . $date . "');");
    }
    function check_input($value)
    {
        //去除斜杠
        if (get_magic_quotes_gpc()) {
            $value=stripslashes($value);
        }
        return $value;
    }
    /**
     * 文件导出
     *
     * @return string 导出结果
     */
    public function downloadFile()
    {
        $dbname = input::get("dataBase");
        Config::set("database.connections.kget.database", $dbname);
        $result = array();
        $fileContent = array();
        $csvContent = "";
        $fileName = "common/files/TempMecontextParameterValue_" . date('YmdHis') . ".csv";
        $column = ' mo,subNetwork,meContext,tableName,parameter,values';
        $items = TempMecontextParameterValue::get()->toArray();
        $fileUtil = new FileUtil();
        $fileUtil->resultToCSV2($column, $items, $fileName);
        $result['fileName'] = $fileName;
        $result['result'] = 'true';
        echo json_encode($result);
    }

    /**
     * 获得查询结果表头
     *
     * @return array
     */
    public function getStrideMOQueryDataHeader()
    {
        $dbname = input::get("dataBase");
        $result = array();
        Config::set("database.connections.kget.database", $dbname);
        $conn = new TempMecontextParameterValue;
        if ($conn->exists()) {
            return $conn->first()->toArray();
        } else {
            $result['error'] = 'error';
            return $result;
        }
    }

    /**
     * 获得查询结果(分页)
     *
     * @return void
     */
    public function getStrideMOQueryData()
    {
        $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $limit = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 50;

        $result = array();
        $dbname = input::get("dataBase");
        Config::set("database.connections.kget.database", $dbname);
        $rows = TempMecontextParameterValue::paginate($limit)->toArray();

        $result["total"] = $rows['total'];
        $result['records'] = $rows['data'];

        echo json_encode($result);
    }

    /**
     * 获得参数列名
     *
     * @return array
     */
    // public function paramDataHeader()
    // {
    //     $dbc = new DataBaseConnection();
    //     $db = $dbc->getDB('mongs', 'mongs');
    //     $table = 'MOsCondition';
    //     $result = array();
    //     $sql = "select `﻿mo`,parameter from $table limit 1";
    //     $rs = $db->query($sql, PDO::FETCH_ASSOC);
    //     if ($rs) {
    //         $rows = $rs->fetchall();
    //         if (count($rows) > 0) {
    //             return $rows[0];
    //         } else {
    //             $result['error'] = 'error';
    //             return $result;
    //         }
    //     } else {
    //         $result['error'] = 'error';
    //         return $result;
    //     }
    // }

    /**
     * 获得参数列值
     *
     * @return void
     */
    // public function paramData()
    // {
    //     $limit = '';
    //     $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
    //     $rows = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 50;
    //     $offset = ($page - 1) * $rows;
    //     $limit = " limit $offset,$rows";

    //     $result = array();
    //     $dbc = new DataBaseConnection();
    //     $db = $dbc->getDB('mongs', 'mongs');
    //     $table = 'MOsCondition';

    //     $rs = $db->query("select count(*) totalCount from " . $table . "");
    //     $row = $rs->fetchAll(PDO::FETCH_ASSOC);

    //     $result["total"] = $row[0]['totalCount'];

    //     $sql = "select `﻿mo`,parameter from " . $table . " " . $limit;
    //     $res = $db->query($sql);
    //     $row = $res->fetchAll(PDO::FETCH_ASSOC);
    //     if (count($row) == 0) {
    //         $result['error'] = 'error';
    //         return json_encode($result);
    //     }
    //     $items = array();
    //     foreach ($row as $qr) {
    //         array_push($items, $qr);
    //     }
    //     $result['records'] = $items;

    //     echo json_encode($result);
    // }
}