<?php

/**
 * ParamsController.php
 *
 * @category SystemManage
 * @package  App\Http\Controllers\SystemManage
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
namespace App\Http\Controllers\SystemManage;

use App\Http\Controllers\Common\DataBaseConnection;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Utils\FileUtil;
use PDO;
use Cache;
use App\Models\Mongs\TemplateParaBaseline;
use App\Models\Mongs\FormulaParaBaseline;
use App\Models\Mongs\Task;
use App\Models\Mongs\TaskParaBaseline;
use App\Models\Mongs\BaselineCheckWhiteList;
use App\Models\COLUMNS;


/**
 * 参数管理
 * Class ParamsController
 *
 * @category SystemManage
 * @package  App\Http\Controllers\SystemManage
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class ParamsController extends Controller
{


    /**
     * 获得BaseLine 模板Tree
     *
     * @return mixed
     */
    public function getBaselineTreeData()
    {
       
        $users   = TemplateParaBaseline::query()->selectRaw('distinct user')->orderBy('user', 'asc')->get();
        $arrUser = array();
        $items   = array();
        $itArr   = array();
        foreach ($users as $user) {
            $userStr       = $user->user;
            $templates = TemplateParaBaseline::where('user', $userStr)->get();
            foreach ($templates as $template) {
                array_push($arrUser, array("text" => $template->templateName, "id" => $template->id, "user" => $template->user));
            }
            $items["text"]  = $userStr;
            $items["nodes"] = $arrUser;
            $arrUser        = array();
            array_push($itArr, $items);
        }

        return response()->json($itArr);

    }//end getBaselineTreeData()


    /**
     * 检索Baseline 模板
     *
     * @return mixed
     */
    public function searchBaselineTreeData()
    {
        $inputData = Input::get('inputData');
        $inputData = "%".$inputData."%";

        $users   = TemplateParaBaseline::query()->selectRaw('distinct user')->orderBy('user', 'asc')->get();
        $arrUser = array();
        $items   = array();
        $itArr   = array();
        foreach ($users as $user) {
            $userStr       = $user->user;
            $templateNames = TemplateParaBaseline::where('user', $userStr)->where('templateName', 'like', $inputData)->get()->toArray();
            if ($templateNames) {
                foreach ($templateNames as $templateName) {
                    $temp['text'] = $templateName['templateName'];
                    $temp['id']   = $templateName['id'];
                    array_push($arrUser, $temp);
                }

                $items["text"]  = $userStr;
                $items["nodes"] = $arrUser;
                $arrUser        = array();
                array_push($itArr, $items);
            }
        }

        return response()->json($itArr);

    }//end searchBaselineTreeData()


    /**
     * 获得Baseline模板内容
     *
     * @return void
     */
    public function getBaselineTableData()
    {
        $templateId = input::get("templateId");
        if ($templateId == null) {
            $row = FormulaParaBaseline::get()->toArray();
        } else {
            $row = FormulaParaBaseline::where('templateId', $templateId)->get()->toArray();
        }
        $items = array();
        foreach ($row as $qr) {
            $templateIdTmp    = $qr['templateId'];
            $qr['templateId'] = TemplateParaBaseline::where('id', $templateIdTmp)->first()->templateName;

            array_push($items, $qr);
        }
        $result         = array();
        $result['text'] = 'id,moName,qualification,qualificationValue,parameter,recommendedValue,category,templateName,version,highTraffic,highInterference,HST';
        $result['rows'] = $items;
        echo json_encode($result);

    }//end getBaselineTableData()
    /**
     * 获得Baseline模板白名单内容
     *
     * @return void
     */
    public function getWhiteList()
    {
        $templateId = input::get("templateId");
        $templateName = TemplateParaBaseline::find($templateId)->templateName;
        $rows = BaselineCheckWhiteList::where('templateId', $templateId)->get()->toArray();
        $result = array();
        $items = array();
        if ($rows) {
            $result['text'] = implode(',', array_keys($rows[0]));
            foreach ($rows as $row) {
                $row['templateId'] = $templateName;
                array_push($items, $row);
            }
            $result['rows'] = $items;
            $result['total'] = count($items);
        } else {
            $result['total'] = 0;
        }
        echo json_encode($result);

    }//end getWhiteList()
    /**
     * 导出Baseline模板白名单内容
     *
     * @return void
     */
    public function exportWhiteList()
    {
        $templateId   = input::get("templateId");
        $templateName = TemplateParaBaseline::find($templateId)->templateName;
        $fileName     = "files/baseline白名单_".$templateName."_".date('YmdHis').".csv";
        $rows         = BaselineCheckWhiteList::where('templateId', $templateId)->get()->toArray();
        $result       = array();
        if ($rows) {
            array_pop($rows[0]);
            $column = implode(',', array_keys($rows[0]));
            
        } else {
            $templateId = 0;
            $rows         = BaselineCheckWhiteList::where('templateId', $templateId)->get()->toArray();
            array_pop($rows[0]);
            $column = implode(',', array_keys($rows[0]));
        }
        $result['rows']     = $rows;
        $result['total']    = count($rows);
        $result['result']   = 'true';
        $result['fileName'] = $fileName;
        $fileUtil           = new fileUtil();
        $fileUtil->resultToCSV2($column, $rows, $fileName);
        return $result;
    }//end exportWhiteList()
    /**
     * 读取CSV文件
     *
     * @return void
     */
    public function getFileContent()
    {
        $dbc        = new DataBaseConnection();
        $db         = $dbc->getDB('mongs', 'mongs');
        $templateId = input::get("templateId");
        $fileName   = Input::get('fileName');
        $fileUtil   = new FileUtil();
        $result     = $fileUtil->parseFile($fileName);
        $len_result = count($result);
        if ($len_result == 0) {
            echo '没有任何数据！';
            exit;
        }
        $fileName   = "common/files/".$fileName;
        BaselineCheckWhiteList::where('templateId', $templateId)->delete();

        $sql = "LOAD DATA LOCAL INFILE '$fileName' INTO TABLE baselineCheckWhiteList character set GBK FIELDS terminated by ',' enclosed by '\"' LINES TERMINATED BY '\n' IGNORE 1 LINES(mo,subNetwork,meContext,cellId,moName,qualification,qualificationValue,parameter,realValue) set templateId = '$templateId'";
        $query = $db -> exec($sql);
        Cache::store('file')->flush(); //清除缓存
        if ($query) {
            echo 'true';
        } else {
            echo 'false';
        }

    }//end getFileContent()
    /**
     * 下载Baseline模板
     *
     * @return void
     */
    public function downloadFile()
    {
        $dbc          = new DataBaseConnection();
        $db           = $dbc->getDB('mongs', 'mongs');
        $result       = array();
        $templateId   = isset($_REQUEST['templateId']) ? $_REQUEST['templateId'] : '';
        $templateName = isset($_REQUEST['templateName']) ? $_REQUEST['templateName'] : '';
        $filename     = "common/files/参数Baseline管理_".$templateName."_".date('YmdHis').".csv";

        $column         = 'moName,qualification,qualificationValue,parameter,recommendedValue,category,version,highTraffic,highInterference,HST,RRU';
        $result["text"] = $column;
        if ($templateId == '') {
            $row = FormulaParaBaseline::query()->selectRaw($column)->get()->toArray();
        } else {
            $row = FormulaParaBaseline::query()->selectRaw($column)->where('templateId', $templateId)->get()->toArray();
        }
        $items = array();
        foreach ($row as $qr) {
            array_push($items, $qr);
        }

        $result['rows']   = $items;
        $result['total']  = count($items);
        $result['result'] = 'true';

        $this->resultToCSV2($result, $filename);
        $result['filename'] = $filename;
        if (count($items) > 1000) {
            $result['rows'] = array_slice($items, 0, 1000);
        }
        echo json_encode($result);

    }//end downloadFile()


    /**
     * 写入CSV文件
     *
     * @param array  $result   Baseline模板内容
     * @param string $filename CSV文件名
     *
     * @return void
     */
    protected function resultToCSV2($result, $filename)
    {
        $csvContent = mb_convert_encoding($result['text']."\n", 'GBK');
        $fp         = fopen($filename, "w");
        fwrite($fp, $csvContent);
        $flag=array(" ","　","\t","\n","\r");
        foreach ($result['rows'] as $row) {
            $newRow = array();
            foreach ($row as $key => $value) {
                $newRow[$key] = mb_convert_encoding(str_replace($flag, '', $value), 'GBK');
            }

            fputcsv($fp, $newRow);
        }

        fclose($fp);

    }//end resultToCSV2()


    /**
     * 更新Baseline模板
     *
     * @return void
     */
    public function uploadFile()
    {
        $dbc = new DataBaseConnection();
        $db  = $dbc->getDB('mongs', 'mongs');
        $templateId   = input::get("templateId");
        $filename = $_FILES['fileImport']['tmp_name'];
        if (empty($filename)) {
            echo '请选择要导入的CSV文件！';
            exit;
        }

        if (file_exists("common/files/".$_FILES['fileImport']['name'])) {
            unlink("common/files/".$_FILES['fileImport']['name']);
        }

        move_uploaded_file($filename, "common/files/".$_FILES['fileImport']['name']);
        setlocale(LC_ALL, null);
        $handle = fopen("common/files/".$_FILES['fileImport']['name'], 'r');
        $result = $this->inputCsv($handle);
        // 解析csv
        $len_result = count($result);
        if ($len_result == 0) {
            echo '没有任何数据！';
            exit;
        }
        $filename = "common/files/".$_FILES['fileImport']['name'];
        FormulaParaBaseline::where('templateId', input::get("templateId"))->delete();

        $sql = "LOAD DATA LOCAL INFILE '$filename' INTO TABLE formulaParaBaseline character set GBK FIELDS terminated by ',' LINES TERMINATED BY '\n' IGNORE 1 LINES(moName,qualification,qualificationValue,parameter,recommendedValue,category,version,highTraffic,highInterference,HST,RRU) set templateId = '$templateId'";
        $query = $db -> exec($sql);

        if ($query) {
            echo "true";
        } else {
            echo 'false';
        }

    }//end uploadFile()


    /**
     * 读取CSV文件
     *
     * @param mixed $handle CSV文件句柄
     *
     * @return string array
     */
    protected function inputCsv($handle)
    {
        $out = array();
        $n   = 0;
        while ($data = fgetcsv($handle, 10000)) {
            $num = count($data);
            for ($i = 0; $i < $num; $i++) {
                $out[$n][$i] = $data[$i];
            }

            $n++;
        }

        return $out;

    }//end inputCsv()


    /**
     * 创建Baseline模板
     *
     * @return void
     */
    public function addOrUpdateTemplate()
    {
        $templateName = input::get('modeName');
        $description  = input::get('modeDescription');
        $networkStandard = input::get("networkStandard");
        $isAutoExecute = input::get('isAutoExecute');
        $isNewSite = input::get('isNewSite');
        $templateId = Input::get('templateId');
        $citys = Input::get("citys");
        if ($citys) {
            $citys = implode(",", $citys);
        }
        $user = Auth::user()->user;
        $res = '';
        if ($templateId) {
            $template = TemplateParaBaseline::find($templateId);
            $template->templateName = $templateName;
            $template->user = $user;
            $template->description = $description;
            $template->networkStandard = $networkStandard;
            $template->isAutoExecute = $isAutoExecute;
            $template->isNewSite = $isNewSite;
            $template->city = $citys;
            $res = $template->save();
        } else {
            $newTemplate = new TemplateParaBaseline;
            $newTemplate->templateName = $templateName;
            $newTemplate->user = $user;
            $newTemplate->description = $description;
            $newTemplate->networkStandard = $networkStandard;
            $newTemplate->isAutoExecute = $isAutoExecute;
            $newTemplate->isNewSite = $isNewSite;
            $newTemplate->city = $citys;
            $res = $newTemplate->save();
        }
        if ($res) {
            echo true;
        } else {
            echo false;
        }
    }//end addOrUpdateTemplate()

    /**
     * 获取Baseline模板信息
     *
     * @return void
     */
    public function getTemplate()
    {
        $templateId = input::get('templateId');
        $row = TemplateParaBaseline::where('id', $templateId)->get()->toArray();
        return $row[0];
    }//end getTemplate()

    /**
     * 删除Baseline模板
     *
     * @return void
     */
    public function deleteMode()
    {
        $id = input::get('id');
        $user = Auth::user()->user;
        if ($user == "admin") {
            $res = TemplateParaBaseline::destroy($id);
            if ($res) {
                FormulaParaBaseline::where('templateId', $id)->delete();
                echo "1";
            } else {
                echo "2";
            }
        } else {
            $res = TemplateParaBaseline::where('id', $id)->where('user', $user)->delete();
            if ($res) {
                FormulaParaBaseline::where('templateId', $id)->delete();
                echo "1";
            } else {
                echo "3";
            }
        }//end if
    }//end deleteMode()


    /**
     * 获得Baseline任务列表
     *
     * @return void
     */
    public function getDate()
    {
        $items  = array();
        $user   = Auth::user();
        // 获取登录用户信息
        $userName = $user->user;
        if ($userName != 'admin') {
            $row = Task::where('taskName', 'like', 'kget______')
                        ->where('type', 'parameter')
                        ->where('status', 'complete')
                        ->whereIn('owner', [$userName,'admin'])
                        ->orderBy('taskName', 'desc')
                        ->get()
                        ->toArray();
        } else {
            $row = Task::where('taskName', 'like', 'kget______')
                        ->where('type', 'parameter')
                        ->where('status', 'complete')
                        ->orderBy('taskName', 'desc')
                        ->get()
                        ->toArray();
        }
        foreach ($row as $task) {
            array_push($items, ["id" => $task["taskName"], "text" => $task["taskName"]]);
        }
        echo json_encode($items);
    }//end getDate()


    /**
     * 获得BaseLine任务详细
     *
     * @return void
     */
    public function getBaselineTaskTable()
    {
        $id     = input::get("id");
        // $page   = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $rows   = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 50;
        $row = TaskParaBaseline::where('templateId', $id)->orderBy('createTime', 'desc')->paginate($rows)->toArray();
        $result["total"] = $row['total'];
        $result['records'] = $row['data'];
        echo json_encode($result);
    }//end getBaselineTaskTable()


    /**
     * 创建Baseline任务
     *
     * @return void
     */
    public function addTask()
    {
        $taskName = input::get('taskName');
        if (TaskParaBaseline::where('taskName', $taskName)->exists() or strpos($taskName, 
            "-") !== false) {
            echo "false";
            return;
        }

        $templateName = input::get("templateName");
        $templateId   = input::get("templateId");
        date_default_timezone_set("PRC");
        $createTime   = date("Y-m-d H:i:s");
        $databaseDate = input::get("databaseDate");
        $owner        = Auth::user()->user;

        $newTask = new TaskParaBaseline;
        $newTask->taskName = $taskName;
        $newTask->status = 'prepare';
        $newTask->owner = $owner;
        $newTask->createTime = $createTime;
        $newTask->databaseDate = $databaseDate;
        $newTask->templateName = $templateName;
        $newTask->templateId = $templateId;
        $newTask->save();

        echo "true";

    }//end addTask()


    /**
     * 删除Baseline任务
     *
     * @return void
     */
    public function deleteTask()
    {
        $dbc = new DataBaseConnection();
        $db  = $dbc->getDB('mongs', 'mongs');
        $taskId = input::get('taskId');

        $row = TaskParaBaseline::where('id', $taskId)->first()->toArray();
        $taskName = $row['taskName'];
        $status = $row['status'];

        TaskParaBaseline::destroy($taskId);
        if ($status != 'prepare') {
            DB::statement("DROP DATABASE IF EXISTS ".$taskName);
        }
        echo "true";

    }//end deleteTask()

    /**
     * 执行baseline检查算法之前核查mo与参数的准确性
     *
     * @return void
     */
    public function runTaskCheck()
    {
        $templateId   = Input::get("templateId");
        $databaseDate = Input::get("databaseDate");
        $formulaParaBaseline = FormulaParaBaseline::select('moName', 'parameter','qualification')->where('templateId', $templateId);
        $first = $formulaParaBaseline->leftJoin(DB::raw("(select TABLE_NAME,COLUMN_NAME from information_schema.COLUMNS where TABLE_SCHEMA='$databaseDate') as a"),function($join){
            $join->on('moName', '=', 'TABLE_NAME')->on('parameter', '=', 'COLUMN_NAME');
        })->whereNull('TABLE_NAME')->get()->toArray();
        $second =  FormulaParaBaseline::select('moName', 'parameter','qualification')->where('templateId', $templateId)->leftJoin(DB::raw("(select TABLE_NAME,COLUMN_NAME from information_schema.COLUMNS where TABLE_SCHEMA='$databaseDate') as b"),function($join){
            $join->on('moName', '=', 'TABLE_NAME')->on('qualification', '=', 'COLUMN_NAME');
        })->whereNull('TABLE_NAME')->whereRaw("qualification != '' and qualification is not null")->get()->toArray();
        $result = array_merge($first, $second);
        return json_encode($result);

    }//end runTaskCheck
    /**
     * 执行Baseline检查
     *
     * @return void
     */
    public function runTask()
    {
        $dbc = new DataBaseConnection();
        $db  = $dbc->getDB('mongs', 'mongs');

        $taskName   = input::get('taskName');
        $templateId = input::get("templateId");
        date_default_timezone_set("PRC");
        $startTime    = date("Y-m-d H:i:s");
        $databaseDate = input::get("databaseDate");

        TaskParaBaseline::where('taskName', $taskName)->update(['status'=>'ongoing','startTime'=>$startTime]);
        $templateParaBaseline = TemplateParaBaseline::select("networkStandard","city")->where('id', $templateId)->get()->toArray()[0];
        /*$query = DB::select("call mongs.baselinecheckTest0801(:databaseDate,:templateId);",['databaseDate'=>$databaseDate,'templateId'=>$templateId]);*/
        //print_r("call mongs.baselinecheckByCity('$databaseDate','$templateId','$networkStandard','','','$citys');");
        $stmt = $db->prepare("call mongs.baselinecheckByCity(:databaseDate,:templateId,:networkStandard,'',:functionName,:citys);");
        $stmt->bindParam(':databaseDate', $databaseDate);
        $stmt->bindParam(':templateId', $templateId);
        $stmt->bindParam(':networkStandard', $templateParaBaseline['networkStandard']);
        $stmt->bindParam(':functionName', $taskName);
        $stmt->bindParam(':citys', $templateParaBaseline['city']);
        $stmt->execute();
        $row = TaskParaBaseline::where('taskName', $taskName)->first()->toArray();
        if ($row['status'] == 'abort') {
            $return['status'] = "abort";
            $return['row']    = array(
                                 'taskName'     => $row['taskName'],
                                 'status'       => $row['status'],
                                 'startTime'    => $row['startTime'],
                                 'endTime'      => $row['endTime'],
                                 'owner'        => $row['owner'],
                                 'createTime'   => $row['createTime'],
                                 'databaseDate' => $row['databaseDate'],
                                 'templateName' => $row['templateName'],
                                );
            echo json_encode($return);
        } else {
            $endTime = date('y-m-d H:i:s', time());
            TaskParaBaseline::where('taskName', $taskName)->update(['status'=>'ongoing','endTime'=>$endTime]);
            $return['status'] = "true";
            $return['row']    = array(
                                 'taskName'     => $row['taskName'],
                                 'status'       => 'ongoing',
                                 'startTime'    => $row['startTime'],
                                 'endTime'      => $endTime,
                                 'owner'        => $row['owner'],
                                 'createTime'   => $row['createTime'],
                                 'databaseDate' => $row['databaseDate'],
                                 'templateName' => $row['templateName'],
                                );
            echo json_encode($return);
        }//end if

    }//end runTask()


    /**
     * 停止Baseline任务检查
     *
     * @return void
     */
    public function stopTask()
    {
        $dbc = new DataBaseConnection();
        $db  = $dbc->getDB('mongs', 'mongs');

        $taskName = input::get('taskName');
        date_default_timezone_set("PRC");
        $endTime = date('y-m-d H:i:s', time());

        TaskParaBaseline::where('taskName', $taskName)->update(['status'=>'abort','endTime'=>$endTime]);
        $row = TaskParaBaseline::where('taskName', $taskName)->first()->toArray();
        $return['status'] = "abort";
        $return['row']    = array(
                             'taskName'     => $row['taskName'],
                             'status'       => "abort",
                             'startTime'    => $row['startTime'],
                             'endTime'      => $endTime,
                             'owner'        => $row['owner'],
                             'createTime'   => $row['createTime'],
                             'databaseDate' => $row['databaseDate'],
                             'templateName' => $row['templateName'],
                            );
        echo json_encode($return);

    }//end stopTask()

    /**
     * 更新Baseline任务运行日志
     *
     * @return void
     */
    public function updateTaskLog()
    {
        $taskName = Input::get("taskName");
        $taskLog    = Input::get("taskLog");
        TaskParaBaseline::where('taskName', $taskName)->update(['taskLog'=>$taskLog]);
    }
    /**
     * 获得城市列表
     *
     * @return string
     */
    public function getCitySelect()
    {
        $templateId = Input::get("templateId");
        $items = array();
        if ($templateId) {
            $databaseConns = DB::select('select a.cityNameChinese cityChinese,a.cityName,b.selected from city a LEFT JOIN 
(select cityName,"true" selected from city where FIND_IN_SET(cityName,(select city from templateParaBaseline where id='.$templateId.')) !=0)b
on a.cityName=b.cityName');
            foreach ($databaseConns as $databaseConn) {
                $city = '{"text":"' . $databaseConn->cityChinese . '","value":"' . $databaseConn->cityName.'","selected":"'.$databaseConn->selected.'"}';
                array_push($items, $city);
            }
        } else {
            $databaseConns = DB::select('select a.cityNameChinese cityChinese,a.cityName from city a');
            foreach ($databaseConns as $databaseConn) {
                $city = '{"text":"' . $databaseConn->cityChinese . '","value":"' . $databaseConn->cityName.'"}';
                array_push($items, $city);
            }
        }

        return response()->json($items);

    }//end getCitySelect()
}//end class
