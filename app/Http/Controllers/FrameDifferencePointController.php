<?php

/**
 * FrameDifferencePointController.php
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
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Common\MyRedis;
use PDO;
use App\Models\Mongs\SiteLte;
use App\Models\MR\MroWeakCoverage_day;
use App\Models\Mongs\Databaseconns;
use App\Models\AutoKPI\InterfereCellQuarter_cell_quarter;

/**
 * 帧差异点图
 * Class FrameDifferencePointController
 *
 * @category Controllers
 * @package  App\Http\Controllers
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class FrameDifferencePointController extends MyRedis
{


    /**
     * 获得频段列表
     *
     * @return mixed
     */
    public function getChannels()
    {
        $results = SiteLte::orderBy('band', 'asc')->distinct('band')->get(['band']);
        $items   = array();
        foreach ($results as $result) {
            if ($result->band == null) {
                $result->band = 'null';
            }

            $channel = '{"text":"'.$result->band.'","value":"'.$result->band.'"}';
            array_push($items, $channel);
        }

        return response()->json($items);

    }//end getChannels()

    /**
     * 获得小区干扰信息
     *
     * @param Request $request HTTP请求
     *
     * @return void
     */
    public function getCells(Request $request)
    {
        $date    = $request['date'];
        $hour    = $request['hour'];
        $minute  = $request['minute'];
        $channel = $request['channel'];
        $channelArr = explode(',', $channel);
        $citys   = $request['citys'];

        $resultChannel = SiteLte::whereIn('band', $channelArr)->distinct('earfcn')->orderBy('earfcn', 'asc')->get(['earfcn'])->toArray();
        $conn = InterfereCellQuarter_cell_quarter::selectRaw(
                                                            "longitude,
                                                            latitude,
                                                            SF1上行干扰电平 as sf1,
                                                            SF2上行干扰电平 as sf2,
                                                            SF6上行干扰电平 as sf6,
                                                            SF7上行干扰电平 as sf7,
                                                            dir,
                                                            cell,
                                                            (
                                                                CASE
                                                                WHEN (
                                                                    channel >= 37750
                                                                    AND channel <= 38250
                                                                ) THEN
                                                                    'D'
                                                                WHEN (
                                                                    channel > 38250
                                                                    AND channel <= 38650
                                                                ) THEN
                                                                    'F'
                                                                WHEN (
                                                                    channel > 38650
                                                                    AND channel <= 39650
                                                                ) THEN
                                                                    'E'
                                                                WHEN (
                                                                    channel > 40440
                                                                    AND channel <= 41040
                                                                ) THEN
                                                                    'D'
                                                                END
                                                            ) AS band")
                    ->where('day_id', $date)
                    ->where('hour_id', $hour)
                    ->where('quarter_id', $minute)
                    ->whereIn('channel', $resultChannel)
                    ->whereNotNull('SF1上行干扰电平')
                    ->whereNotNull('SF2上行干扰电平')
                    ->whereNotNull('SF6上行干扰电平')
                    ->whereNotNull('SF7上行干扰电平');           
        if ($citys) {
            $cityArr = array();
            $rows = Databaseconns::whereIn('cityChinese', $citys)->get()->toArray();
            foreach ($rows as $row) {
                $city = $row['connName'];
                if (is_numeric(substr($city, -1))) {
                    continue;
                }
                array_push($cityArr, $city);
            }
            $conn = $conn->whereIn('city', $cityArr);
        }
        $rs = $conn->get()->toArray();
        echo json_encode($rs);

    }//end getCells()


    /**
     * 获得日期列表
     *
     * @return array
     */
    public function getDate()
    {
        $dbc    = new DataBaseConnection();
        $db    = $dbc->getDB('mongs', 'AutoKPI');
        $table  = 'interfereCellQuarter_cell_quarter';
        $sql    = "select distinct day_id from $table";
        $this->type = 'AutoKPI:interCloud';
        return $this->getValue($db, $sql);

    }//end interfepointDate()

    /**
     * 获得小区信息
     *
     * @param Request $request HTTP请求
     *
     * @return void
     */
    public function getCell(Request $request)
    {
        $date    = $request['date'];
        $hour    = $request['hour'];
        $minute  = $request['minute'];
        $channel = $request['channel'];
        $channelArr = explode(',', $channel);
        $citys   = $request['citys'];
        $cell    = $request['cell'];
        $resultChannel = SiteLte::whereIn('band', $channelArr)->distinct('earfcn')->orderBy('earfcn', 'asc')->get(['earfcn'])->toArray();

        $conn = InterfereCellQuarter_cell_quarter::selectRaw(
                                                            "longitude,
                                                            latitude,
                                                            SF1上行干扰电平 as sf1,
                                                            SF2上行干扰电平 as sf2,
                                                            SF6上行干扰电平 as sf6,
                                                            SF7上行干扰电平 as sf7,
                                                            dir,
                                                            cell,
                                                            (
                                                                CASE
                                                                WHEN (
                                                                    channel >= 37750
                                                                    AND channel <= 38250
                                                                ) THEN
                                                                    'D'
                                                                WHEN (
                                                                    channel > 38250
                                                                    AND channel <= 38650
                                                                ) THEN
                                                                    'F'
                                                                WHEN (
                                                                    channel > 38650
                                                                    AND channel <= 39650
                                                                ) THEN
                                                                    'E'
                                                                WHEN (
                                                                    channel > 40440
                                                                    AND channel <= 41040
                                                                ) THEN
                                                                    'D'
                                                                END
                                                            ) AS band")
                    ->where('day_id', $date)
                    ->where('hour_id', $hour)
                    ->where('quarter_id', $minute)
                    ->whereIn('channel', $resultChannel)
                    ->where('cell', $cell);
        if ($citys) {
            $cityArr = array();
            $rows = Databaseconns::whereIn('cityChinese', $citys)->get()->toArray();
            foreach ($rows as $row) {
                $city = $row['connName'];
                if (is_numeric(substr($city, -1))) {
                    continue;
                }
                array_push($cityArr, $city);
            }
            $conn = $conn->whereIn('city', $cityArr);
        }
        $row = $conn->first()->toArray();
        if ($row) {
            echo json_encode($row);
            return;
        } else {
            echo "false";
        }

    }//end getCell()


}//end class
