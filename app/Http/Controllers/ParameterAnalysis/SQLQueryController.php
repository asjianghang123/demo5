<?php
/**
 * SQLQueryController.php
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
use App\Task;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use PDO;
use App\Models\Mongs\SQLTemplate;
use App\Models\TABLES;

/**
 * 自定义SQL语句查询
 * Class SQLQueryController
 *
 * @category ParameterAnalysis
 * @package  App\Http\Controllers\ParameterAnalysis
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class SQLQueryController extends Controller
{


    /**
     * 获得模板列表
     *
     * @return mixed
     */
    public function getCustomTreeData()
    {
        $users = SQLTemplate::distinct('user')->get(['user']);
        $arrUser = array();
        $items   = array();
        $itArr   = array();
        foreach ($users as $user) {
            $userStr       = $user->user;
            $templateNames = SQLTemplate::where('user', $userStr)->get();
            foreach ($templateNames as $templateName) {
                array_push($arrUser, array("text" => $templateName->templateName, "value"=>$templateName->id));
            }

            $items["text"]  = $userStr;
            $items["nodes"] = $arrUser;
            $arrUser        = array();
            array_push($itArr, $items);
        }

        return response()->json($itArr);

    }//end getCustomTreeData()


    /**
     * 模板更新
     *
     * @return string|void
     */
    public function saveModeChange()
    {
        $templateId  = Input::get('templateId');
        $templateId  = $this->check_input($templateId);
        $customContext = Input::get('content');
        $customContext = $this->check_input($customContext);
        SQLTemplate::where('id', $templateId)->update(['kpiformula'=>$customContext]);
        return 'success';

    }//end saveModeChange()

     /**
     * 过滤非法字符
     *
     * @param string $value
     *
     * @return string $value
     */
    function check_input($value)
    {
        // $con=mysqli_connect("localhost", "root", "mongs", "mongs");
        $dbc    = new DataBaseConnection();
        $con     = $dbc->getConnDBi('mongs', 'mongs');
        // 去除斜杠
        if (get_magic_quotes_gpc()) {
            $value = stripslashes($value);
        }
        // 如果不是数字则加引号
        if (preg_match("/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/", $value)) {
            $value = "'" . mysqli_real_escape_string($con, $value) . "'";
        }
        return $value;
    }

    /**
     * 模板检索
     *
     * @return mixed 检索结果
     */
    public function getSearchCustomTreeData()
    {
        $inputData = Input::get('inputData');
        $inputData=$this->check_input($inputData);
        $inputData = "%".$inputData."%";
        $users = SQLTemplate::distinct('user')->get(['user']);
        $arrUser = array();
        $items   = array();
        $itArr   = array();
        foreach ($users as $user) {
            $userStr       = $user->user;
            $templateNames = SQLTemplate::where('user', $userStr)->where('templateName', 'like', $inputData)->get();
            foreach ($templateNames as $templateName) {
                array_push($arrUser, array("text" => $templateName->templateName, "value"=>$templateName->id));
            }
            $items["text"]  = $userStr;
            $items["nodes"] = $arrUser;
            $arrUser        = array();
            array_push($itArr, $items);
        }

        return response()->json($itArr);

    }//end getSearchCustomTreeData()


    /**
     * 获得城市列表
     *
     * @return string
     */
    public function getAllCity()
    {
        $cityClass = new DataBaseConnection();
        return $cityClass->getCityOptions();

    }//end getAllCity()


    /**
     * 获得SQL语句
     *
     * @return mixed
     */
    public function getKpiFormula()
    {
        $templateId = Input::get('treeData');
        $kpiformula = SQLTemplate::where('id', $templateId)->first()->kpiformula;
        return $kpiformula;

    }//end getKpiFormula()


    /**
     * 获得查询表头
     *
     * @return void
     */
    public function getTableHeader()
    {
        $dataBase     = input::get("dataBase");
        $templateId = Input::get('templateId');
        $dbc          = new DataBaseConnection();
        $res          = SQLTemplate::where('id', $templateId)->first()->toArray();
        $webSql       = "select * from (".$res['kpiformula'].") as t";

        $db1            = $dbc->getDB('kget', $dataBase);
        if (strstr($res['kpiformula'], 'newSiteBaselineCheck')) {
            $count = TABLES::where('TABLE_SCHEMA', $dataBase)->where('TABLE_NAME', 'newSiteBaselineCheck')->count();
            if ($count == 0) {
                $db1->query("call newSiteBaselineCheck();");
            }
        }
        $result         = array();
        $rows           = $db1->query($webSql." limit 1")->fetch(PDO::FETCH_ASSOC);
        $keys           = array_keys($rows);
        $result['text'] = implode(",", $keys);
        echo json_encode($result);

    }//end getTableHeader()


    /**
     * 获得查询结果
     *
     * @return void
     */
    public function getTableData()
    {
        $dataBase     = input::get("dataBase");
        $templateId   = Input::get('templateId');
        $dbc          = new DataBaseConnection();
        $res          = SQLTemplate::where('id', $templateId)->first()->toArray();
        $webSql       = "select * from (".$res['kpiformula'].") as t";
        $page         = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $rows         = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 50;
        $offset       = (($page - 1) * $rows);
        $limit        = " limit $offset,$rows";

        // 获得总数
        $db1      = $dbc->getDB('kget', $dataBase);
        $result   = array();
        $countall = "select count(*) from (".$res['kpiformula'].") as t";
        $rows     = $db1->query($countall)->fetch(PDO::FETCH_ASSOC);
        $result["total"] = $rows['count(*)'];

        $rows  = $db1->query($webSql.$limit)->fetchAll(PDO::FETCH_ASSOC);
        $items = array();
        foreach ($rows as $qr) {
            array_push($items, $qr);
        }

        $result['records'] = $items;
        echo json_encode($result);

    }//end getTableData()


    /**
     * 导出查询结果
     *
     * @return string 导出结果
     */
    public function getAllTableData()
    {
        $dataBase     = input::get("dataBase");
        $templateId   = Input::get('templateId');
        $dbc          = new DataBaseConnection();
        $res          = SQLTemplate::where('id', $templateId)->first()->toArray();
        $webSql       = "select * from (".$res['kpiformula'].") as t";

        $db1      = $dbc->getDB('kget', $dataBase);
        $fileName = "common/files/".$res['templateName'].date('YmdHis').".csv";
        $result   = array();
        $rs       = $db1->query($webSql);
        if ($rs) {
            $items = $rs->fetchAll(PDO::FETCH_ASSOC);
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

        return $fileName;

    }//end getAllTableData()


    /**
     * 获得分页查询结果
     *
     * @return void
     */
    // public function getTable()
    // {
    //     $dataBase     = input::get("dataBase");
    //     $templateName = Input::get('templateName');
    //     $templateName = trim($templateName);
    //     $databaseconn = DB::table('SQLTemplate')->
    //     where('templateName', 'like', $templateName)->get();
    //     $webSql       = $databaseconn[0]->kpiformula;
    //     $dbc          = new DataBaseConnection();
    //     $db           = $dbc->getDB('mongs', $dataBase);
    //     $result       = array();
    //     $count        = array();
    //     $templateName = Input::get('templateName');
    //     $filename     = "common/files/".$templateName.date('YmdHis').".csv";

    //     $rows = $db->query($webSql);
    //     if ($rows) {
    //         $i = 0;
    //         while ($res = $rows->fetch(PDO::FETCH_ASSOC)) {
    //             // 查询
    //             array_push($count, $res);
    //             $i++;
    //             if ($i >= 1003) {
    //                 // 超过1000条跳出循环
    //                 break;
    //             }
    //         }

    //         $keys = array_keys($count[0]);
    //         // 获取表头
    //         $result['text'] = implode(",", $keys);
    //         $csvContent     = mb_convert_encoding($result['text']."\n", 'gb2312', 'utf-8');
    //         $fp = fopen($filename, "a+");
    //         fwrite($fp, $csvContent);
    //         $rows = $db->query($webSql);
    //         while ($res = $rows->fetch(PDO::FETCH_ASSOC)) {
    //             // 导出
    //             fputcsv($fp, $res);
    //         }

    //         fclose($fp);
    //     }//end if

    //     if (array_key_exists("datetime_id", $count[0])) {
    //         $i = 0;
    //         foreach ($count as $counts) {
    //             $count[$i]['datetime_id'] = date('Y-m-d ', strtotime($counts['datetime_id']));
    //             $i++;
    //         }
    //     }

    //     $result['total'] = count($count);
    //     if (count($count) > 1000) {
    //         $result['rows'] = array_slice($count, 0, 1000);
    //     } else {
    //         $result['rows'] = $count;
    //     }

    //     $keys           = array_keys($count[0]);
    //     $result['text'] = implode(",", $keys);
    //     $result['filename'] = $filename;
    //     echo json_encode($result);

    // }//end getTable()


    /**
     * 删除模板
     *
     * @return void
     */
    public function deleteMode()
    {
        $id = input::get("id");
        $user         = Auth::user();
        $userName = $user->user;
        $res = SQLTemplate::destroy($id);
        return $res;

    }//end deleteMode()


    /**
     * 新建模板
     *
     * @return string|void
     */
    public function insertMode()
    {
        $templateName = Input::get('insertName');
        $user         = Auth::user();
        $userName = $user->user;
        $totalNum = SQLTemplate::where('user', $userName)->where('templateName', $templateName)->count();
        if ($totalNum > 0) {
            return 'wrong';
        } else {
            $sqlTemplate = new SQLTemplate;
            $sqlTemplate->templateName = $templateName;
            $sqlTemplate->user = $userName;
            $res = $sqlTemplate->save();
            if ($res = 1) {
                return 'success';
            } else {
                return 'wrong';
            }
        }

    }//end insertMode()


    /**
     * 更新模板
     *
     * @return string|void
     */
    // public function saveMode()
    // {
    //     $templateName  = Input::get('templateName');
    //     $customContext = Input::get('customContext');
    //     $user          = Auth::user();
    //     $userName = $user->user;
    //     SQLTemplate::where('user',$userName)->where('templateName',$templateName)->update(['kpiformula'=>$customContext]);
    //     return 'success';

    // }//end saveMode()


    /**
     * 写入CSV文件
     *
     * @param array  $result   表头
     * @param string $filename CSV文件名
     * @param array  $count    内容
     *
     * @return void
     */
    protected function resultToCSV2($result, $filename, $count)
    {
        $csvContent = mb_convert_encoding($result['text']."\n", 'gb2312', 'utf-8');
        $fp         = fopen($filename, "w");
        fwrite($fp, $csvContent);
        foreach ($count as $row) {
            fputcsv($fp, $row);
        }

        fclose($fp);

    }//end resultToCSV2()


}//end class
