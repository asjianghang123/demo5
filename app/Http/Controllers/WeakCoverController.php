<?php

/**
 * WeakCoverController.php
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
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Common\MyRedis;
use PDO;
use App\Models\MR\MroWeakCoverage_day;
use App\Models\MR\MroOverCoverage_day;

/**
 * 弱覆盖分析
 * Class WeakCoverController
 *
 * @category Controllers
 * @package  App\Http\Controllers
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class WeakCoverController extends MyRedis
{


    /**
     * 生成重叠覆盖点图数据
     *
     * @param mixed $request HTTP请求
     *
     * @return mixed
     */
    public function getOverlapCoverPointCells(Request $request)
    {
        $date       = $request['date'];
        $city       = $request['city'];
        $dbc  = new DataBaseConnection();
        $dbname = $dbc->getMRDatabase($city);
        $channel    = $request['channel'];
        $channelArr = explode(',', $channel);
        $rows = MroOverCoverage_day::on($dbname)
                    ->selectRaw('mroOverCoverage_day.ecgi,rate,longitudeBD as longitude,latitudeBD as latitude,dir,band')
                    ->where('dateId', $date)
                    ->leftJoin('siteLte', 'siteLte.ecgi', '=', 'mroOverCoverage_day.ecgi')
                    ->whereIn('band', $channelArr)
                    ->get()
                    ->toArray();
        return response()->json($rows);

    }//end getOverlapCoverPointCells()


    /**
     * 获得小区信息
     *
     * @param Request $request HTTP请求
     *
     * @return mixed
     */
    public function getCells(Request $request)
    {
        $date       = $request['date'];
        $city       = $request['city'];
        $dbc  = new DataBaseConnection();
        $citySim = $dbc->getMRDatabase($city);
        $channel    = $request['channel'];
        $channelArr = explode(',', $channel);

        $rs = MroWeakCoverage_day::on($citySim)
                // ->selectRaw('mroWeakCoverage_day.ecgi,ratio110,longitudeBD as longitude, latitudeBD as latitude,dir,mroWeakCoverage_day.band')
                ->selectRaw('mroWeakCoverage_day.ecgi,avgRsrp,longitudeBD as longitude, latitudeBD as latitude,dir,mroWeakCoverage_day.band')
                ->where('dateId', $date)
                ->whereIn('mroWeakCoverage_day.band', $channelArr)
                ->leftJoin('siteLte', 'siteLte.ecgi', '=', 'mroWeakCoverage_day.ecgi')
                ->get()
                ->toArray();
        return response()->json($rs);

    }//end getCells()


    /**
     * 生成图表数据
     *
     * @param Request $request HTTP请求
     *
     * @return mixed
     */
    public function getCharts(Request $request)
    {
        $cells = $request['cells'];
        $cellArr = explode(',', $cells);
        $date     = $request['date'];
        $city     = $request['city'];
        $dbc  = new DataBaseConnection();
        $citySim = $dbc->getMRDatabase($city);
        $rs = MroWeakCoverage_day::on($citySim)
                ->where('dateId', $date)
                ->whereIn('ecgi', $cellArr)
                ->get()
                ->toArray();
        $items = array();
        foreach ($rs as $result) {
            $series         = array();
            $series['name'] = "A".$result['ecgi']."A";
            $series['data'] = array(
                               $result['numLess80'],
                               $result['numLess80_90'],
                               $result['numLess90_100'],
                               $result['numLess100_110'],
                               $result['numLess110'],
                              );
            array_push($items, $series);
        }

        return response()->json($items);

    }//end getCharts()


    /**
     * 获得弱覆盖日期列表
     * 
     * @param Request $request HTTP请求
     *
     * @return array
     */
    public function weakCoverDatee(Request $request)
    {
        $dbc    = new DataBaseConnection();
        $city = $request['city'];
        $dbname = $dbc->getMRDatabase($city);
        $db     = $dbc->getDB('MR', $dbname);
        $table  = 'mroWeakCoverage_day';
        $sql    = "select distinct dateId from $table";
        $this->type = $dbname.':weakCover';
        return $this->getValue($db, $sql);

    }//end weakCoverDatee()


    /**
     * 获得重叠覆盖日期列表
     * 
     * @param Request $request HTTP请求
     *
     * @return array
     */
    public function overlapCoverPointDate(Request $request)
    {
        $dbc    = new DataBaseConnection();
        $city = $request['city'];
        $dbname = $dbc->getMRDatabase($city);
        $db     = $dbc->getDB('MR', $dbname);
        $table  = 'mroOverCoverage_day';
        $sql    = "select distinct dateId from $table";
        $this->type = $dbname.':overlapCoverPoint';
        return $this->getValue($db, $sql);

    }//end overlapCoverPointDate()

    /**
     * 获得重叠覆盖小区信息
     *
     * @param Request $request HTTP请求
     *
     * @return void
     */
    public function getCell(Request $request)
    {
        $date       = $request['date'];
        $city       = $request['city'];
        $dbc  = new DataBaseConnection();
        $dbname = $dbc->getMRDatabase($city);
        $cell       = $request['cell'];
        $channel    = $request['channel'];
        $channelArr = explode(',', $channel);
        $row = MroOverCoverage_day::on($dbname)
                    ->selectRaw('mroOverCoverage_day.ecgi,rate,longitudeBD as longitude,latitudeBD as latitude,dir,band')
                    ->where('dateId', $date)
                    ->where('mroOverCoverage_day.ecgi', $cell)
                    ->leftJoin('siteLte', 'siteLte.ecgi', '=', 'mroOverCoverage_day.ecgi')
                    ->whereIn('band', $channelArr)
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
     * 获得弱覆盖小区信息
     *
     * @param Request $request HTTP请求
     *
     * @return mixed
     */
    public function getOneCell(Request $request)
    {
        $date       = $request['date'];
        $city       = $request['city'];
        $dbc  = new DataBaseConnection();
        $citySim = $dbc->getMRDatabase($city);
        $channel    = $request['channel'];
        $channelArr = explode(',', $channel);
        $cell       = $request['cell'];

        $row = MroWeakCoverage_day::on($citySim)
                // ->selectRaw('mroWeakCoverage_day.ecgi,ratio110,longitudeBD as longitude, latitudeBD as latitude,dir,mroWeakCoverage_day.band')
                ->selectRaw('mroWeakCoverage_day.ecgi,avgRsrp,longitudeBD as longitude, latitudeBD as latitude,dir,mroWeakCoverage_day.band')
                ->where('dateId', $date)
                ->whereIn('mroWeakCoverage_day.band', $channelArr)
                ->where('mroWeakCoverage_day.ecgi', $cell)
                ->leftJoin('siteLte', 'siteLte.ecgi', '=', 'mroWeakCoverage_day.ecgi')
                ->first()
                ->toArray();
        if ($row) {
            echo json_encode($row);
            return;
        } else {
            echo "false";
        }
    }//end getOneCell()
}//end class
