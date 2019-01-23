<?php

/**
 * AutoRoadSurveyController.php
 *
 * @category Controllers
 * @package  App\Http\Controllers
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
namespace App\Http\Controllers;

use App\Http\Controllers\Common\DataBaseConnection;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Common\MyRedis;
use PDO;
use App\Models\CTR\InternalPerUeMdtM1Report;
use App\Models\Mongs\SiteLte;
use App\Models\Mongs\Databaseconns;

/**
 * 自动路测
 * Class AutoRoadSurveyController
 *
 * @category Controllers
 * @package  App\Http\Controllers
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class AutoRoadSurveyController extends MyRedis
{
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

    /**
     * 获得日期列表
     *
     * @param Requset $request 
     *
     * @return array
     */
    public function getDate(Request $request)
    {
        $dbc    = new DataBaseConnection();
        $city = $request['city'];
        // $dbname = $dbc->getENCtrName($city);
        // $db     = $dbc->getDB('CTR', $dbname);
        $dbname = 'mdt_ctr';
        $db     = $dbc->getDB('CTR_test', $dbname);
        $table  = 'internalPerUeMdtM1Report';
        $sql    = "select distinct date_id from $table";
        $this->type = $dbname.':autoRoad';
        return $this->getValue($db, $sql);

    }//end getDate()

    /**
     * 获得地理化数据
     *
     * @return void
     */
    public function getData()
    {
        $dbc     = new DataBaseConnection();
        $city = input::get("city");
        // $dbname = $dbc->getENCtrName($city);
        $dbname = 'CTR_test';
        $date   = input::get("date");
        $hour   = input::get("hour");
        $row = InternalPerUeMdtM1Report::on($dbname)
            ->where('date_id', $date)
            ->where('hour_id', $hour)
            ->where('latitude', '!=', '')
            ->where('longtitude', '!=', '')
            ->groupBy('ecgi')
            ->get()
            ->toArray();
        echo json_encode($row);

    }//end getData()

    /**
     * 获得小区信息
     *
     * @return void
     */
    public function getCell()
    {
        $cityStr  = input::get("city");
        $citys    = explode(",", $cityStr);

        $date   = input::get("date");
        $cell   = input::get("cell");

        $row = InternalPerUeMdtM1Report::selectRaw('最大RRC连接用户数,空口下行业务量GB, cell, longitudeBD as longitude, latitudeBD as latitude,dir,band')
            ->where('day_id', $date)
            ->whereIn('SysCoreTemp_cell_day.city', $citys)
            ->where('cell', $cell)
            ->leftJoin('mongs.siteLte', 'cellName', '=', 'cell')
            ->first()
            ->toArray();

        if ($row) {
            echo json_encode($row);
            return;
        } else {
            echo "false";
        }
    }//end getCell()

    /**
     *获得一个小区信息
     * 
     * @return mixed
     */
    public function getOneCell()
    {
        $dbc  = new DataBaseConnection();
        $city = input::get("city");
        // $dbname = $dbc->getENCtrName($city);
        $dbname = 'CTR_test';
        $date   = input::get("date");
        $hour   = input::get("hour");
        $row = InternalPerUeMdtM1Report::on($dbname)
            ->where('date_id', $date)
            ->where('hour_id', $hour)
            ->where('latitude', '!=', '')
            ->where('longtitude', '!=', '')
            ->groupBy('ecgi')
            ->first()
            ->toArray();
        echo json_encode($row);
      
    }//end getOneCell()
}//end class
