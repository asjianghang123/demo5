<?php

/**
 * TerminalQueryController.php
 *
 * @category UserAnalysis
 * @package  App\Http\Controllers\UserAnalysis
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
namespace App\Http\Controllers\UserAnalysis;

use App\Http\Controllers\Common\DataBaseConnection;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use PDO;
use App\Models\CDR\UserInfo;

/**
 * 终端查询
 * Class TerminalQueryController
 *
 * @category UserAnalysis
 * @package  App\Http\Controllers\UserAnalysis
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class TerminalQueryController extends Controller
{


    /**
     * 获得城市列表
     *
     * @return void
     */
    public function getCitys()
    {
        $dbc   = new DataBaseConnection();
        $db    = $dbc->getDB('CDR');
        $sql   = "show dataBases";
        $res   = $db->query($sql);
        $items = array();
        $row   = $res->fetchAll(PDO::FETCH_ASSOC);
        foreach ($row as $r) {
            if ($r['DATABASE'] != 'Global') {
                $CHCity = $dbc->getCDRToCHName($r['DATABASE']);
                array_push($items, $CHCity."-".$r['DATABASE']);
            }
        }
        echo json_encode($items);

    }//end getCitys()


    /**
     * 获得用户信息表头
     *
     * @return void
     */
    public function getUserInfoHead()
    {
        $city = input::get("city");
        $userInfo = new UserInfo;
        $items = $userInfo->getVisible();
        $result         = array();
        $result['text'] = implode(",", $items);
        echo json_encode($result);

    }//end getUserInfoHead()


    /**
     * 获得用户信息
     *
     * @return void
     */
    public function getUserInfoData()
    {
        // $page   = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $rows   = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 50;
        $city = input::get("city");

        $user   = input::get("user");
        $result = array();
        $row = UserInfo::on($city)->where('imsi', $user)->orWhere('msisdn', 'like', '%'.$user)->paginate($rows)->toArray();
        $result["total"] = $row['total'];
        $result["records"] = $row['data'];
        echo json_encode($result);

    }//end getUserInfoData()


}//end class
