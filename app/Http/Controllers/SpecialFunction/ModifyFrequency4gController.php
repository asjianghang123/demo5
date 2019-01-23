<?php

/**
 * ModifyFrequency4gController.php
 *
 * @category SpecialFunction
 * @package  App\Http\Controllers\SpecialFunction
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
namespace App\Http\Controllers\SpecialFunction;

use App\Http\Controllers\Common\DataBaseConnection;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Utils\FileUtil;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use PDO;

/**
 * 4G翻频
 * Class ModifyFrequency4gController
 *
 * @category SpecialFunction
 * @package  App\Http\Controllers\SpecialFunction
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class ModifyFrequency4gController extends Controller
{
    /**
     * 获取日期（数据库名称）
     *
     * @return string
     */
    public function getTasks()
    {
        $filter = '';
        $items = array();
        $i = 0;
        $user = Auth::user();//获取登录用户信息
        $userName = $user->user;//需要获取当前登录用户！！！！！
        if ($user != 'admin') {
            $filter = " and owner in('$userName','admin')";
        }
        $tasks = DB::select("select taskName from task where type=:type and status=:status and taskName like 'kget______' $filter order by taskName desc", ['type' => 'parameter', 'status' => 'complete']);

        foreach ($tasks as $task) {
            $items[$i++] = '{"text":"' . $task->taskName . '"}';
        }
        return response()->json($items);//需要通过response返回响应数据
    }

    /**
     * 获得城市列表
     *
     * @return void
     */
    public function getCityTree()
    {
        $table = input::get("table");
        $text = input::get("text");
        $value = input::get("value");
        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('mongs', 'mongs');
        $sql = "select id, cityChinese,connName from " . $table . " group by cityChinese";
        $res = $db->query($sql);
        $row = $res->fetchAll(PDO::FETCH_ASSOC);
        $citys = array();
        array_push($citys, array("id" => 0, "text" => "全部城市", "value" => "city"));
        foreach ($row as $qr) {
            $array = array("id" => $qr["id"], "text" => $qr[$text], "value" => $qr[$value]);
            array_push($citys, $array);
        }
        echo json_encode($citys);
    }

    /**
     * 导入模板数据
     *
     * @return void
     */
    public function getFileContent()
    {
        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('mongs', 'mongs');
        $table = input::get("table");
        $city = input::get("city");
        $fileName = Input::get('fileName');
        $fileUtil = new FileUtil();
        $result = $fileUtil->parseFile($fileName);
        $len_result = count($result);
        if ($len_result == 0) {
            echo '没有任何数据！';
            exit;
        }
        $db->query("delete from $table where city like '" . $city . "%'");
        $data_values = '';
        for ($i = 1; $i < $len_result; $i++) {
            $EUtranCellTDD = $result[$i][0];
            $earfcn = $result[$i][1];
            $importDate = input::get("importDate");
            $data_values .= "('$EUtranCellTDD','$earfcn','$city','$importDate'),";

        }
        $data_values = mb_convert_encoding(substr($data_values, 0, -1), 'UTF-8', 'GBK');//解析文件编码是UTF-8无需转码
        $sql = "insert into $table (EUtranCellTDD,earfcn,city,importDate) values $data_values";
        $query = $db->query($sql);
        if ($query) {
            echo 'true';
        } else {
            echo 'false';
        }
    }

    /**
     * 导出模板数据
     *
     * @return array 导出结果
     */
    public function downloadTemplateFile()
    {
        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('mongs', 'mongs');
        $result = array();
        $table = input::get("table");
        $city = input::get("city");
        $fileName = "common/files/4G翻频_" . $city . "_" . date('YmdHis') . ".csv";
        $column = 'EUtranCellTDD,earfcn';
        $sql = "select " . $column . " from $table where city='$city'";
        $res = $db->query($sql);
        $items = $res->fetchAll(PDO::FETCH_ASSOC);
        $fileUtil = new FileUtil();
        $fileUtil->resultToCSV2($column, $items, $fileName);
        $result['fileName'] = $fileName;
        $result['result'] = 'true';
        return $result;
    }

    /**
     * 执行存储过程
     *
     * @return string
     */
    public function runProcedure()
    {
        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('mongs', 'mongs');
        $city = Input::get("city");
        $task = Input::get("task");
        $rs = $db->query("call modifyFrequency4G('$task','$city')");
        if ($rs)
            return 'true';
        else return 'false';
    }

    /**
     * 获得列名集合
     *
     * @return array
     */
    public function getTableField()
    {
        $dbc = new DataBaseConnection();
        $task = Input::get("task");
        $db = $dbc->getDB('kget', $task);
        $result = array();
        $city = Input::get("city");
        $table = 'TempModifyFrequency4GCompare';
        $filter = " where city='$city' ";
        if ($dbc->tableIfExists($task, $table)) {
            $sql = "select * from " . $table . $filter . " limit 1";
            $rs = $db->query($sql, PDO::FETCH_ASSOC);
            if ($rs) {
                $rs = $rs->fetchAll();
                if (count($rs) > 0) {
                    return $rs[0];
                } else {
                    $result['result'] = 'error';
                }
            } else {
                $result['result'] = 'error';

            }
        } else {
            $result['result'] = 'error';

        }
        return $result;
    }

    /**
     * 生成检查结果
     *
     * @return array
     */
    public function getItems()
    {
        $dbc = new DataBaseConnection();
        $task = Input::get("task");
        $db = $dbc->getDB('kget', $task);
        $city = Input::get("city");
        $displayStart = Input::get('page');
        $displayLength = Input::get('limit');
        $offset = ($displayStart - 1) * $displayLength;
        $limit = " limit $offset,$displayLength ";
        $filter = " where city='$city' ";
        $table = 'TempModifyFrequency4GCompare';
        $result = array();
        $sqlCount = "select count(*) from " . $table . $filter;
        $rs = $db->query($sqlCount, PDO::FETCH_ASSOC);
        $result["total"] = $rs->fetchColumn();
        $sql = "select * from $table $filter $limit";
        $rs = $db->query($sql, PDO::FETCH_OBJ);
        $res = $rs->fetchAll();
        if ($res) {
            $result["records"] = $res;
        }
        return $result;
    }

    /**
     * 下载检查结果
     *
     * @return mixed
     */
    public function downloadFile()
    {
        $dbc = new DataBaseConnection();
        $task = Input::get("task");
        $db = $dbc->getDB('kget', $task);
        $city = Input::get("city");
        $filter = " where city='$city' ";
        $table = Input::get("table");

        $city = input::get("city");
        $type = Input::get('type');
        if ($dbc->tableIfExists($task, $table)) {
            $fileName = "common/files/" . $type . "_" . $city . "_" . date('YmdHis') . ".csv";
            $sql = "select * from $table $filter";
            $rs = $db->query($sql, PDO::FETCH_ASSOC);
            if ($rs) {
                $items = $rs->fetchAll();
                if (count($items) > 0) {
                    $row = $items[0];
                    $column = implode(",", array_keys($row));
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
        } else {
            $result['result'] = false;
        }
        return $result;
    }
}