<?php

/**
 * DownloadManageController.php
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
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use PDO;
use App\Models\Mongs\FtpServerInfo;
use App\Models\Mongs\Databaseconns;

/**
 * FTP数据源管理
 * Class DownloadManageController
 *
 * @category SystemManage
 * @package  App\Http\Controllers\SystemManage
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class DownloadManageController extends Controller
{


    /**
     * 获得log类型列表
     *
     * @return mixed
     */
    public function treeQuery()
    {
        $row = FtpServerInfo::query()->selectRaw('DISTINCT type')->get()->toArray();
        $types = array();
        array_push($types, array("id" => 0, "text" => "全部log", "value" => "type"));
        $i = 1;
        foreach ($row as $qr) {
            $array = array(
                      "id"    => $i++,
                      "text"  => $qr['type'],
                      "value" => $qr['type'],
                     );
            array_push($types, $array);
        }
        return json_encode($types);
    }//end treeQuery()


    /**
     * 获得FTP-Server信息
     *
     * @return mixed
     */
    public function getTableData()
    {
        $type = Input::get("type");
        $flag = Input::get("flag");
        // $page   = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $rows   = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 50;
        if ($type == "type") {
            $row = FtpServerInfo::sortBy('type')->paginate($rows)->toArray();
        } else {
            $row = FtpServerInfo::where('type', $type)->paginate($rows)->toArray();
        }
        $result["total"] = $row['total'];
        $items = array();
        foreach ($row['data'] as $qr) {
            $externalAddress = $qr['externalAddress'];
            $userName        = $qr['userName'];
            $password        = $qr['password'];
            $fileDir         = $qr['fileDir'];
            if ($flag == "true") {
                try {
                    $conn = ftp_connect($externalAddress);
                    if ($conn) {
                        $ftp_login = ftp_login($conn, $userName, $password);
                        if ($ftp_login) {
                            $ftp_rawlist = ftp_rawlist($conn, $fileDir);
                            if ($ftp_rawlist) {
                                $qr['status'] = "true";
                            } else {
                                $qr['status'] = "false";
                            }
                        } else {
                            $qr['status'] = "false";
                        }
                    } else {
                        $qr['status'] = "false";
                    }
                } catch (Exception $e) {
                    $qr['status'] = "false";
                }
            } else {
                $qr['status'] = null;
            }

            array_push($items, $qr);
        }//end foreach

        $result['records'] = $items;
        return json_encode($result);

    }//end getTableData()


    /**
     * 更新FTP-Server信息
     *
     * @return mixed
     */
    public function updateDownload()
    {
        $downloadId      = Input::get("downloadId");
        $serverName      = Input::get("serverName");
        $city            = Input::get("citys");
        $type            = Input::get("type");
        $externalAddress = Input::get("externalAddress");
        $internalAddress = Input::get("internalAddress");
        $userName        = Input::get("userName");
        $password        = Input::get("password");
        $subNetwork      = Input::get("subNetwork");
        $fileDir         = Input::get("fileDir");

        if ($downloadId) {
            $res = FtpServerInfo::where('id', $downloadId)->update([
                                                'serverName'=>$serverName,
                                                'city'=>$city,
                                                'type'=>$type,
                                                'externalAddress'=>$externalAddress,
                                                'internalAddress'=>$internalAddress,
                                                'subNetwork'=>$subNetwork,
                                                'fileDir'=>$fileDir,
                                                'userName'=>$userName,
                                                'password'=>$password
                                            ]);
        } else {
            $newFtp = new FtpServerInfo;
            $newFtp->serverName = $serverName;
            $newFtp->city = $city;
            $newFtp->type = $type;
            $newFtp->externalAddress = $externalAddress;
            $newFtp->internalAddress = $internalAddress;
            $newFtp->subNetwork = $subNetwork;
            $newFtp->fileDir = $fileDir;
            $newFtp->userName = $userName;
            $newFtp->password = $password;
            $res = $newFtp->save();
        }
        return $res;

    }//end updateDownload()


    /**
     * 删除FTP连接信息
     *
     * @return void
     */
    public function deleteDownload()
    {
        $id  = Input::get("id");
        $res = FtpServerInfo::destroy($id);
        return $res;
    }//end deleteDownload()


    /**
     * 获得城市列表
     *
     * @return mixed
     */
    public function getCitys()
    {
        $row = Databaseconns::groupBy('cityChinese')->orderBY('connName', 'asc')->get()->toArray();
        $items = array();
        foreach ($row as $r) {
            array_push($items, array("label" => $r['cityChinese'], "value" => $r['connName']));
        }
        return json_encode($items);
    }//end getCitys()
}//end class
