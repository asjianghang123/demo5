<?php

/**
 * CustomQueryController.php
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
use PDO;
use App\Models\Mongs\CustomTemplate;
use App\Models\Mongs\Users;
use App\Models\Mongs\Databaseconns;
use App\Models\Mongs\Databaseconn2G;

use SubnetWork;

/**
 * 自定义SQL语句指标查询
 * Class CustomQueryController
 *
 * @category QueryAnalysis
 * @package  App\Http\Controllers\QueryAnalysis
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class CustomQueryController extends GetTreeData
{

    public function getCheckedCustomTreeData() {
        $type = Input::get('type');
        $login_user = Auth::user()->user;
        if ($login_user == "admin") {
            $users      = CustomTemplate::where("type", $type)->distinct('user')->get(['user']);
        } else {
            $users      = CustomTemplate::where("type", $type)->whereIn("user", [$login_user, "admin"])->distinct('user')->get(['user']);
        }
        $arrUser    = array();
        $items      = array();
        $itArr      = array();

        foreach ($users as $user) {
            $userStr       = $user->user;
            $templateNames = CustomTemplate::where("type", $type)->where('user', $userStr)->get();
            foreach ($templateNames as $templateName) {
                array_push($arrUser, array("text" => $templateName->templateName, "value"=>$templateName->id));
            }

            $items["text"]  = $userStr;
            $items["nodes"] = $arrUser;
            $arrUser        = array();
            array_push($itArr, $items);
        }

        for ($i = 0; $i < count($itArr); $i++) {
            $user = $itArr[$i]['text'];
            if ($user == "admin") {
                $itArr[$i]['text'] = "通用模板";
            } else if ($user == "system") {
                $itArr[$i]['text'] = "系统模板";
            } else {
                $nameCNSql         = Users::where('user', $user)->first(); 
                if ($nameCNSql) {
                    $itArr[$i]['text'] = $nameCNSql->name;
                }
            }
        }

        return response()->json($itArr);
    }

    /**
     * 获得模板列表
     *
     * @return mixed
     */
    public function getTreeData()
    {
        $type = Input::get('type');
        $login_user = Auth::user()->user;
        if ($login_user == "admin") {
            $users      = CustomTemplate::where("type", $type)->distinct('user')->get(['user']);
        } else {
            $users      = CustomTemplate::where("type", $type)->whereIn("user", [$login_user,"admin"])->distinct('user')->get(['user']);
        }
        $arrUser    = array();
        $items      = array();
        $itArr      = array();

        foreach ($users as $user) {
            $userStr       = $user->user;
            $templateNames = CustomTemplate::where("type", $type)->where('user', $userStr)->get();
            foreach ($templateNames as $templateName) {
                array_push($arrUser, array("text" => $templateName->templateName,"value"=>$templateName->id));
            }

            $items["text"]  = $userStr;
            $items["nodes"] = $arrUser;
            $arrUser        = array();
            array_push($itArr, $items);
        }

        for ($i = 0; $i < count($itArr); $i++) {
            $user = $itArr[$i]['text'];
            if ($user == "admin") {
                $itArr[$i]['text'] = "通用模板";
            } else if ($user == "system") {
                $itArr[$i]['text'] = "系统模板";
            } else {
                $nameCNSql         = Users::where('user', $user)->first(); 
                if ($nameCNSql) {
                    $itArr[$i]['text'] = $nameCNSql->name;
                }
            }
        }

        return response()->json($itArr);

    }//end getTreeData()


    /**
     * 更新模板
     *
     * @return string|void
     */
    public function saveModeChange()
    {
        $templateId  = Input::get('templateId');
        $customContext = Input::get('content');
        CustomTemplate::where('id', $templateId)->update(['kpiformula'=>$customContext]);
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
        $con=mysqli_connect("localhost", "root", "mongs", "mongs");
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
     * 检索模板
     *
     * @return mixed
     */
    public function getSearchCustomTreeData()
    {
        $type = Input::get("type");
        $login_user = Auth::user()->user;
        $inputData  = Input::get('inputData');
        $inputData=$this->check_input($inputData);
        $inputData  = "%".$inputData."%";
        if ($login_user == "admin") {
            $users      = CustomTemplate::where("type", $type)->where('templateName', 'like', $inputData)->get();
        } else {
            $users      = CustomTemplate::where("type", $type)->whereIn("user", [$login_user, "admin"])->where('templateName','like', $inputData)->get();
        }
        $items      = array();
        foreach ($users as $user) {
            array_push($items, array("text" => $user->templateName,"value"=>$user->id));
        }

        return response()->json($items);

    }//end getSearchCustomTreeData()


    /**
     * 获得城市列表
     *
     * @return string
     */
    public function getAllCity()
    {
        $type = Input::get("type");
        $cityClass = new DataBaseConnection();
        if ($type == "LTE") {
            return $cityClass->getCityOptions();
        } else {
            return $cityClass->getCityGSMOptions();
        }
        

    }//end getAllCity()


    /**
     * 获得SQL语句
     *
     * @return mixed
     */
    public function getKpiFormula()
    {
        $templateId = Input::get('treeData');
        return CustomTemplate::where('id', $templateId)->first()->kpiformula;

    }//end getKpiFormula()


    /**
     * 获得查询结果
     *
     * @return void
     */
    public function getTable()
    {
        $type = Input::get("type");
        $cityArr = Input::get('city');
        if ($type == "LTE") {
            $citys = Databaseconns::whereIn('cityChinese', $cityArr)->get(['connName'])->toArray();
        } else {
            $citys = Databaseconn2G::whereIn('cityChinese', $cityArr)->get(['connName'])->toArray();
        }
        
        $templateId = Input::get('templateId');
        $conn = CustomTemplate::where('id', $templateId)->first();
        $webSql = $conn->kpiformula;
        $dbc          = new DataBaseConnection();
        $db           = $dbc->getDB('mongs', 'mongs');
        if ($db == null) {
            $result["result"] = "false";
            $result["reason"] = "Failed to connect to database.";
            echo json_encode($result);
            return;
        }

        $result = array();
        $count  = array();

        $templateName = $conn->templateName;
        $filename     = "common/files/".$templateName.date('YmdHis').".csv";
        $filename     = preg_replace('/[\\(\\)]/', '-', $filename);

        foreach ($citys as $city) {
            if ($type == "LTE") {
                $row      = Databaseconns::where('connName', $city)->first()->toArray();
            } else {
                $row      = Databaseconn2G::where('connName', $city)->first()->toArray();
            }
            // $row      = Databaseconns::where('connName',$city)->first()->toArray();
            $host     = $row['host'];
            $port     = $row['port'];
            $dbName   = $row['dbName'];
            $userName = $row['userName'];
            $password = $row['password'];

            $pmDbDSN = "dblib:host=".$host.":".$port.";dbname=".$dbName;
            $pmDB    = new PDO($pmDbDSN, $userName, $password);

            if ($pmDB == null) {
                $result["result"] = "false";
                $result["reason"] = "Failed to connect to database.";
                echo json_encode($result);
                return;
            }
            if($city['connName'] == 'changzhou3') {
                $webSql = str_replace("substring(SN,charindex('=',substring(SN,32,20))+32,charindex('_',substring(SN,32,20))-charindex('=',substring(SN,32,20))-1)", "substring(SN, charindex('=', SN)+1, charindex(',', SN)-charindex('=', SN)-1)", $webSql);
            }

            $webSql = SubnetWork::is_ReplaceDc($webSql, $city['connName']);
            $rows = $pmDB->query($webSql);
            if ($pmDB->errorCode() != '00000') {
                // $result['failed'] = 'failed';
                // echo json_encode($result);
                // return;
            } else {
                if ($rows) {
                    $i = 0;
                    while ($res = $rows->fetch(PDO::FETCH_ASSOC)) {
                        // 查询
                        array_push($count, $res);
                        $i++;
                        if ($i >= 1003) {
                            // 超过1000条跳出循环
                            break;
                        }
                    }
                    if (count($count) != 0) {
                        $keys = array_keys($count[0]);
                        // 获取表头
                        $result['text'] = implode(",", $keys);
                        $csvContent     = mb_convert_encoding($result['text']."\n", 'gb2312', 'utf-8');
                        $fp = fopen($filename, "a+");
                        fwrite($fp, $csvContent);
                        $rows = $pmDB->query($webSql);
                        while ($res = $rows->fetch(PDO::FETCH_ASSOC)) {
                            // 导出
                            fputcsv($fp, $res);
                        }

                        fclose($fp);
                    }
                    /*$keys = array_keys($count[0]);
                    // 获取表头
                    $result['text'] = implode(",", $keys);
                    $csvContent     = mb_convert_encoding($result['text']."\n", 'gb2312', 'utf-8');
                    $fp = fopen($filename, "a+");
                    fwrite($fp, $csvContent);
                    $rows = $pmDB->query($webSql);
                    while ($res = $rows->fetch(PDO::FETCH_ASSOC)) {
                        // 导出
                        fputcsv($fp, $res);
                    }

                    fclose($fp);*/
                }//end if
            }//end if
        }//end foreach

        if (count($count)!=0) {
            if (array_key_exists("datetime_id", $count[0])) {
                $i = 0;
                foreach ($count as $counts) {
                    $count[$i]['datetime_id'] = date('Y-m-d ', strtotime($counts['datetime_id']));
                    $i++;
                }
            }

            $result['total'] = count($count);
            if (count($count) > 1000) {
                $result['rows'] = array_slice($count, 0, 1000);
            } else {
                $result['rows'] = $count;
            }

            $keys           = array_keys($count[0]);
            $result['text'] = implode(",", $keys);
            $result['filename'] = $filename;
            echo json_encode($result);
        }else{
            $result['total'] = 0;
            $result['rows'] = [];
            echo json_encode($result);
        }
        

    }//end getTable()


    /**
     * 删除模板
     *
     * @return void
     */
    public function deleteMode()
    {
        $templateId = Input::get('templateId');
        CustomTemplate::destroy($templateId);

    }//end deleteMode()


    /**
     * 新建模板
     *
     * @return string|void
     */
    public function insertMode()
    {
        $type = Input::get("type");
        $templateName = Input::get('insertName');
        $user         = Auth::user();
        $userName = $user->user;
        $rs = CustomTemplate::where('user', $userName)->where('templateName', $templateName);
        if ($rs->exists()) {
            return 'wrong';
        } else {
            $newCustomTemplate = new CustomTemplate;
            $newCustomTemplate->templateName = $templateName;
            $newCustomTemplate->user = $userName;
            $newCustomTemplate->type = $type;
            $newCustomTemplate->save();

            return 'success';
        }
        // }//end if

    }//end insertMode()


    /**
     * 保存模板
     *
     * @return string|void
     */
    // public function saveMode()
    // {
    //     $templateName  = Input::get('templateName');
    //     $customContext = Input::get('customContext');
    //     $user          = Auth::user();
    //     // 获取登录用户信息
    //     if ($user == null) {
    //         $items[$i] = 'login';
    //         return 'login';
    //     } else {
    //         $userName = $user->user;
    //         $dbc      = new DataBaseConnection();
    //         $db       = $dbc->getDB('mongs', 'mongs');
    //         if ($db == null) {
    //             $result["result"] = "false";
    //             $result["reason"] = "Failed to connect to database.";
    //             echo json_encode($result);
    //             return;
    //         }

    //         $db->query(
    //             "update customTemplate set kpiformula = '$customContext' 
    //                     where templateName = '$templateName' and user = '$userName';"
    //         );
    //         return 'success';
    //     }

    // }//end saveMode()


    /**
     * 写入CSV文件
     *
     * @param array  $result   表头
     * @param string $filename CSV文件名
     * @param array  $count    数据集
     *
     * @return void
     */
   /* protected function resultToCSV2($result, $filename, $count)
    {
        $csvContent = mb_convert_encoding($result['text']."\n", 'gb2312', 'utf-8');
        $fp         = fopen($filename, "w");
        fwrite($fp, $csvContent);
        foreach ($count as $row) {
            fputcsv($fp, $row);
        }

        fclose($fp);

    }//end resultToCSV2()  */


}//end class
