<?php

/**
 * TrailQueryController.php
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
use App\Http\Controllers\Common\MyRedis;
use PDO;
use App\Models\CDR\UserInfo;
use App\Models\CDR\L_HANDOVER;
use App\Models\Mongs\SiteLte;

/**
 * 轨迹查询
 * Class TrailQueryController
 *
 * @category UserAnalysis
 * @package  App\Http\Controllers\UserAnalysis
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class TrailQueryController extends MyRedis
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
     * 获得轨迹数据
     *
     * @return void
     */
    public function getTrailData()
    {
        $dbname = input::get("dataBase");

        $date = input::get("date");
        $user = input::get("user");
        if (substr($user, 0, 1) == "1") {
            $user = UserInfo::on($dbname)->where('msisdn', 'like', '%'.$user)->first();
        }

        $result = array();
        if (!$user) {
            echo json_encode($result);
            return;
        }
        $user = $user->imsi;

        $items = array();
        $row  = L_HANDOVER::on($dbname)->where('date_id', $date)->where('imsi', $user)->orderBy('eventTime', 'asc')->get()->toArray();
        foreach ($row as $r) {
            if (end($items) == $r['ecgi']) {
                continue;
            }
            array_push($items, $r['ecgi']);
        }

        if (count($items) > 0) {
            foreach ($items as $ecgi) {
                $ecgi = str_replace("0-0", "0", $ecgi);
                $row = SiteLte::selectRaw('cellName,cellNameChinese,longitudeBD as longitude,latitudeBD as latitude')->where('ecgi', $ecgi)->first()->toArray();
                if (count($row) > 0) {
                    array_push($result, $row);
                }
            }
        }

        echo json_encode($result);

    }//end getTrailData()


    /**
     * 获得日期列表
     *
     * @return void
     */
    public function getDataGroupByDate()
    {
        $dbname = input::get("dataBase");
        $dbc    = new DataBaseConnection();
        $db     = $dbc->getDB('CDR', $dbname);
        $table  = 'L_HANDOVER';
        $sql    = "select distinct date_id from $table";
        $this->type = $dbname.':trailQuery';
        return json_encode($this->getValue($db, $sql));

    }//end getDataGroupByDate()


}//end class
