<?php

/**
* StrongInterferenceCellController.php
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
use DateInterval;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use PDO;
use Illuminate\Support\Facades\Auth;

use App\Models\Mongs\Task;

/**
 * 强干扰小区处理
 * Class StrongInterferenceCellController
 *
 * @category SpecialFunction
 * @package  App\Http\Controllers\SpecialFunction
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class StrongInterferenceCellController extends Controller
{

     /**
     * 获取日期（数据库名称）
     *
     * @return string
     */
    public function getTasks()
    {
        $user     = Auth::user();//获取登录用户信息
        $userName = $user->user;
        $tasks    = Task::where('type', 'parameter')->where('status', 'complete')->where('taskName', 'like', 'kget______');
        if ($userName != 'admin') {
            $tasks = $tasks->whereIn('owner', [$userName,'admin']);
        }
        $tasks = $tasks->orderBy('taskName', 'desc')->get()->toArray();
        $items = array();
        foreach ($tasks as $task) {
            $items[] = array("text"=>$task['taskName'], "id"=>$task['taskName']);
        }
        return response()->json($items);//需要通过response返回响应数据
    }// end getTasks()
    /**
     * 获取导入的强干扰小区列表
     *
     * @return array
     */
    public function getFileContent()
    {
        $fileName   = Input::get('fileName');
        $fileUtil   = new FileUtil();
        $result     = $fileUtil->parseFile($fileName);
        $len_result = count($result);
        if ($len_result == 0) {
            echo '没有任何数据！';
            exit;
        }
        $task        = Input::get('task');
        $dbc         = new DataBaseConnection();
        $db          = $dbc->getDB('kget', $task);
        $date        = new DateTime();
        $dateTime    = $date->format("YmdHis");
        $table       = "StrongInterferenceCells".$dateTime;
        $resultTable = "StrongInterferenceCellsResult_".$dateTime;
        $sql         = "drop table if exists $table;";
        $stmt        = $db->prepare($sql);
        $stmt->execute();
        $sql         = "CREATE TABLE `$table` (
              `cell` varchar(255) DEFAULT NULL
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='强干扰小区列表';";
        $stmt        = $db->prepare($sql);
        $stmt->execute();

        $data_values = '';
        for ($i = 1; $i < $len_result; $i++) {
            $cell         = $result[$i][0];
            $data_values .= "('$cell'),";
        }
        $data_values = mb_convert_encoding(substr($data_values, 0, -1), 'UTF-8', 'GBK');//解析文件编码是UTF-8无需转码
        $sql         = "insert into $table (cell) values $data_values";
        $stmt        = $db->prepare($sql);
        $stmt->execute();
        $sql         = "call StrongInterferenceCellHandler(:dateTime,:table)";
        $stmt        = $db->prepare($sql);
        $stmt->bindValue(':dateTime', $dateTime);
        $stmt->bindValue(':table', $table);
        $query       = $stmt->execute();
        $result      = array();
        if ($query) {
            $result['result'] = 'true';
            $result['table']  = $resultTable;
        } else {
            $result['result'] = 'false';
        }
        return $result;
    }//end getFileContent()
     /**
     * 获取强干扰小区参数信息表头
     *
     * @return array
     */
    function getTableField()
    {
        $task   = Input::get('task');
        $table  = Input::get('table');
        $dbc    = new DataBaseConnection();
        $db     = $dbc->getDB('kget', $task);
        $result = array();
        if ($dbc->tableIfExists($task, $table)) {
            $sql    = "select * from " . $table . " limit 1";
            $stmt   = $db->prepare($sql);
            $stmt->execute();
            if ($stmt) {
                $rs = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (count($rs) > 0) {
                    return $rs[0];
                } else {
                    $result['result'] = 'error';
                }
            } else {
                $result['result']     = 'error';

            }
        } else {
            $result['result']         = 'error';

        }
        return $result;

    }//end getTableField()
    /**
     * 获取强干扰小区参数列表
     *
     * @return array
     */
    public function getItems()
    {
        $dbc             = new DataBaseConnection();
        $task            = Input::get("task");
        $table           = Input::get('table');
        $db              = $dbc->getDB('kget', $task);
        $displayStart    = Input::get('page');
        $displayLength   = Input::get('limit');
        $offset          = ($displayStart - 1) * $displayLength;
        $limit           = " limit $offset,$displayLength ";
        $result          = array();
        $sqlCount        = "select count(*) from " . $table;
        $stmt            = $db->prepare($sqlCount);
        $stmt->execute();
        $result["total"] = $stmt->fetchColumn();
        $sql             = "select * from $table $limit";
        $stmt            = $db->prepare($sql);
        $stmt->execute();
        if ($stmt) {
            $result["records"] = $stmt->fetchAll(PDO::FETCH_OBJ);
        }
        return $result;
    }
     /**
     * 导出强干扰小区参数列表
     *
     * @return array
     */
    public function downloadFile()
    {
        $dbc   = new DataBaseConnection();
        $task  = Input::get("task");
        $table = Input::get('table');
        $db    = $dbc->getDB('kget', $task);
        if ($dbc->tableIfExists($task, $table)) {
            $sql      = "select * from $table";
            $fileName = "files/强干扰小区参数列表_$task". "_" . date('YmdHis') . ".csv";
            $stmt     = $db->prepare($sql);
            if ($stmt->execute()) {
                $fileUtil           = new FileUtil();
                $totalCount         = $fileUtil->resultToCSV($stmt, $fileName);
                $result['fileName'] = $fileName;
                $result['result']   = true;
            } else {
                $result['result']   = false;
            }
        } else {
                $result['result']   = false;
            }
        return $result;
    }// end downloadFile()
     /**
     * 导入强干扰小区参数列表(非文件形式)
     *
     * @return array
     */
    public function insertCellList()
    {
        $task        = Input::get('task');
        $dbc         = new DataBaseConnection();
        $db          = $dbc->getDB('kget', $task);
        $cellList    = Input::get('cellList');
        $cellsArr    = explode(",", $cellList);
        $date        = new DateTime();
        $dateTime    = $date->format("YmdHis");
        $table       = "StrongInterferenceCells".$dateTime;
        $resultTable = "StrongInterferenceCellsResult_".$dateTime;
        $sql         = "drop table if exists $table;";
        $stmt        = $db->prepare($sql);
        $stmt->execute();
        $sql         = "CREATE TABLE `$table` (
              `cell` varchar(255) DEFAULT NULL
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='强干扰小区列表';";
        $stmt        = $db->prepare($sql);
        $stmt->execute();

        $data_values = '';
        foreach ($cellsArr as $cell) {
            $data_values .= "('$cell'),";
        }
        $data_values = mb_convert_encoding(substr($data_values, 0, -1), 'UTF-8', 'GBK');//解析文件编码是UTF-8无需转码
        $sql         = "insert into $table (cell) values $data_values";
        $stmt        = $db->prepare($sql);
        $stmt->execute();
        $sql         = "call StrongInterferenceCellHandler(:dateTime,:table)";
        $stmt        = $db->prepare($sql);
        $stmt->bindValue(':dateTime', $dateTime);
        $stmt->bindValue(':table', $table);
        $query       = $stmt->execute();
        $result      = array();
        if ($query) {
            $result['result'] = 'true';
            $result['table']  = $resultTable;
        } else {
            $result['result'] = 'false';
        }
        return $result;
    }// end insertCellList()
}