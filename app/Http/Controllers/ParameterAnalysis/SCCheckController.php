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
use App\Models\Mongs\Databaseconns;
use App\Models\Mongs\SystemConstantsTemplate;
use App\Models\Kget\TempSystemConstantsCheck;

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
class SCCheckController extends Controller
{
    /**
     * 获取日期（数据库名称）
     *
     * @return string
     */
    public function getTasks()
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
     *
     *@return array
     */
    public function getCityList()
    {
        $dbc = new DataBaseConnection();
        return $dbc->getCityOptions();
    }//end getCityList()

    /**
     * 导入模板数据
     *
     * @return void
     */
    public function getFileContent()
    {
        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('mongs', 'mongs');
        $fileName = Input::get('fileName');
        $fileUtil = new FileUtil();
        $result = $fileUtil->parseFile($fileName);
        $len_result = count($result);
        if ($len_result == 0) {
            echo '没有任何数据！';
            exit;
        }
        $db->query("delete from SystemConstantsTemplate");
        $fileName   = "common/files/".$fileName;
        $sql = "LOAD DATA LOCAL INFILE '$fileName' INTO TABLE SystemConstantsTemplate character set GBK FIELDS terminated by ',' LINES TERMINATED BY '\n' IGNORE 1 LINES(SCName,SCNo,SCValue,CoverageType,`是否8TX`,softwareVersion)";
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
        $result = array();
        $fileName = "common/files/SC分场景核查_". date('YmdHis') . ".csv";
        $rows = SystemConstantsTemplate::selectRaw("SCName,SCNo,SCValue,CoverageType,`是否8TX`,softwareVersion")->get()->toArray();
        $fp = fopen($fileName, "w");
        $column = implode(",", array_keys($rows[0]));
        $csvContent = mb_convert_encoding($column . "\n", 'GBK');
        fwrite($fp, $csvContent);
        foreach ($rows as $row) {
            $newRow = array();
            foreach ($row as $key => $value) {
                $newRow[$key] = mb_convert_encoding($value, 'GBK');
            }
            fputcsv($fp, $newRow);
        }
        fclose($fp);
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
        $citys = Input::get('citys');
        $task = Input::get("task");
        $pdo = $dbc->getDB('kget', $task);
        foreach ($citys as $city) {
            $rs = $dbc->getCityByCityChinese($city);
            $city = $rs[0]->connName;
            $sql = "call $task.SystemConstants_Check(:city)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':city', $city);
            if ($stmt->execute()) {
                return 'true';
            } else {
                return 'false';
            }
        }
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
        Config::set("database.connections.kget.database", $task);
        $result = array();
        $citys = Input::get('citys');
        $table = 'TempSystemConstantsCheck';
        if ($dbc->tableIfExists($task, $table)) {
            $conn = new TempSystemConstantsCheck;
            if ($citys) {
                $cityArr = Databaseconns::whereIn('cityChinese', $citys)->get(['connName'])->toArray();
                $conn = $conn->whereIn('city', $cityArr);
            }
            if ($conn->exists()) {
                return $conn->first()->toArray();
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
        $task = Input::get("task");
        Config::set("database.connections.kget.database", $task);
        $page = Input::get('page');
        $limit = Input::get('limit');
        $citys = Input::get('citys');
        $result = array();
        $conn = new TempSystemConstantsCheck;
        if ($citys) {
            $cityArr = Databaseconns::whereIn('cityChinese', $citys)->get(['connName'])->toArray();
            $conn = $conn->whereIn('city', $cityArr);
        }
        $rows = $conn->paginate($limit)->toArray();
        $result["total"] = $rows['total'];
        $result["records"] = $rows['data'];

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
        Config::set("database.connections.kget.database", $task);
        $citys = Input::get('citys');
        $table = Input::get("table");
        if ($dbc->tableIfExists($task, $table)) {
            $fileName = "common/files/SC分场景核查_TempSystemConstantsCheck_" . date('YmdHis') . ".csv";
            $conn = new TempSystemConstantsCheck;
            if ($citys) {
                $cityArr = Databaseconns::whereIn('cityChinese', $citys)->get(['connName'])->toArray();
                $conn = $conn->whereIn('city', $cityArr);
            }
            $rows = $conn->get()->toArray();
            $fp = fopen($fileName, "w");
            $column = implode(",", array_keys($rows[0]));
            $csvContent = mb_convert_encoding($column . "\n", 'GBK');
            fwrite($fp, $csvContent);
            foreach ($rows as $row) {
                $newRow = array();
                foreach ($row as $key => $value) {
                    $newRow[$key] = mb_convert_encoding($value, 'GBK');
                }
                fputcsv($fp, $newRow);
            }
            fclose($fp);

            $result['fileName'] = $fileName;
            $result['result'] = true;

        } else {
            $result['result'] = false;
        }
        return $result;
    }
}