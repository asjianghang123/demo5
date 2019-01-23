<?php

/**
 * TrailQueryManualController.php
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
use App\Models\Mongs\TrailQuery;
use App\Models\Mongs\SiteLte;

/**
 * 轨迹查询
 * Class TrailQueryManualController
 *
 * @category UserAnalysis
 * @package  App\Http\Controllers\UserAnalysis
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class TrailQueryManualController extends MyRedis
{


    /**
     * 获得城市列表
     *
     * @return void
     */
    public function getCitys()
    {
        $items = array();
        $row   = TrailQuery::distinct()->get(["city"])->toArray();
        foreach ($row as $r) {
            array_push($items, array("label"=>$r['city'], "value"=>$r['city']));
        }
        echo json_encode($items);

    }//end getCitys()

    /**
     * 获得日期列表
     *
     * @return void
     */
    public function getDataGroupByDate()
    {
        // $dbname = input::get("dataBase");
        $dbc    = new DataBaseConnection();
        // $db     = $dbc->getDB('CDR', $dbname);
        $db     = $dbc->getDB('mongs', 'mongs');
        $table  = 'trailQuery';
        $sql    = "select distinct date_id from $table";
        $this->type = 'mongs:trailQuery_manual';
        return json_encode($this->getValue($db, $sql));

    }//end getDataGroupByDate()


    public function getTrailData()
    {
        $city = input::get("city");
        $startDate = input::get("startDate");
        $endDate = input::get("endDate");
        $user = input::get("user");

        $result = array();

        $row  = TrailQuery::where('city', $city)->where('imsi', $user)->whereBetween('date_id', array($startDate, $endDate))->whereNotNull('longitude')->whereNotNull('latitude')->orderBy('eventTime', 'asc')->get()->toArray();
        foreach ($row as $r) {
            if (end($result)['cell'] == $r['cell'] || $r['cell'] == null) {
                continue;
            }
            array_push($result, $r);
        }

        echo json_encode($result);
    }

    public function getAllSite()
    {
        $result = SiteLte::query()->selectRaw('cellName,longitudeBD as longitude,latitudeBD as latitude,dir,band')->get();
        return json_encode($result);
    }


}//end class
