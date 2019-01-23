<?php

/**
 * StoreManageController.php
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
use PDO;
use App\Models\Mongs\Databaseconns;
use App\Models\Mongs\TraceServerInfo;

/**
 * 本地log存储管理
 * Class StoreManageController
 *
 * @category SystemManage
 * @package  App\Http\Controllers\SystemManage
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class StoreManageController extends Controller
{
     /**
     * 获得城市列表
     *
     * @return void
     */
    public function treeQuery()
    {
        $row = Databaseconns::groupBy('cityChinese')->get()->toArray();
        $citys = array();
        array_push($citys, array("id" => 0, "text" => "全部城市", "value" => "city"));
        foreach ($row as $qr) {
            $array = array(
                      "id"    => $qr["id"],
                      "text"  => $qr['cityChinese'],
                      "value" => $qr['connName'],
                     );
            array_push($citys, $array);
        }
        return json_encode($citys);
    }//end TreeQuery()

    /**
     * 获得改城市的log存储信息详情
     *
     * @return mixed
     */
    public function getTableData()
    {
        $city   = input::get("city");
        // $page   = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $rows   = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 50;
        if ($city == "city") {
            $row = TraceServerInfo::paginate($rows)->toArray();
        } else {
            $row = TraceServerInfo::where('city', $city)->paginate($rows)->toArray();
        }
        $result["total"] = $row['total'];
        $result['records'] = $row['data'];
        return json_encode($result);
    }//end getTableData()

    /**
     * 更新log存储信息
     *
     * @return mixed
     */
    public function updateDownload()
    {
        $downloadId = input::get("downloadId");
        $serverName = input::get("serverName");
        $city       = input::get("citys");
        $type       = input::get("type");
        $ipAddress  = input::get("ipAddress");
        $sshUserName   = input::get("sshUserName");
        $sshPassword   = input::get("sshPassword");
        $ftpUserName   = input::get("ftpUserName");
        $ftpPassword   = input::get("ftpPassword");
        $fileDir    = input::get("fileDir");
        if ($downloadId) {
            $res = TraceServerInfo::where('id', $downloadId)->update([
                                                        'serverName'=>$serverName,
                                                        'city'=>$city,
                                                        'type'=>$type,
                                                        'ipAddress'=>$ipAddress,
                                                        'fileDir'=>$fileDir,
                                                        'sshUserName'=>$sshUserName,
                                                        'sshPassword'=>$sshPassword,
                                                        'ftpUserName'=>$ftpUserName,
                                                        'ftpPassword'=>$ftpPassword
                                                    ]);
        } else {
            $newTrace = new TraceServerInfo;
            $newTrace->serverName = $serverName;
            $newTrace->city = $city;
            $newTrace->type = $type;
            $newTrace->ipAddress = $ipAddress;
            $newTrace->fileDir = $fileDir;
            $newTrace->sshUserName = $sshUserName;
            $newTrace->sshPassword = $sshPassword;
            $newTrace->ftpUserName = $ftpUserName;
            $newTrace->ftpPassword = $ftpPassword;
            $res = $newTrace->save();
        }
        return strval($res);
    }//end updateDownload()


    /**
     * 删除log存储信息
     *
     * @return mixed
     */
    public function deleteDownload()
    {
        $id  = input::get("id");
        $res = TraceServerInfo::destroy($id);
        return $res;
    }//end deleteDownload()


    /**
     * 获得城市列表
     *
     * @return void
     */
    public function getCitys()
    {
        $row = Databaseconns::groupBy('cityChinese')->orderBy('connName', 'asc')->get()->toArray();
        $items = array();
        foreach ($row as $r) {
            array_push($items, array("label" => $r['cityChinese'], "value" => $r['connName']));
        }
        return json_encode($items);
    }//end getCitys()


    /**
     * 获得trace-Server类别
     *
     * @return mixed
     */
    public function getTypes()
    {
        $row = TraceServerInfo::query()->selectRaw('DISTINCT type')->get()->toArray();
        $items = array();
        foreach ($row as $r) {
            array_push($items, array("label" => $r['type'], "value" => $r['type']));
        }
        return json_encode($items);
    }//end getTypes()
}//end class
