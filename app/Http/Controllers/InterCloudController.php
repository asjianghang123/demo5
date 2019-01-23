<?php

/**
 * IndexGeographicOverviewController.php
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
 * 干扰云图
 * Class InterCloudController
 *
 * @category Controllers
 * @package  App\Http\Controllers
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class InterCloudController extends MyRedis
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
                                                            PUSCH上行干扰电平,
                                                            PUCCH上行干扰电平,
                                                            prb100_avg,
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
                    ->whereNotNull('PUSCH上行干扰电平');           
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
    public function interfepointDate()
    {
        $dbc    = new DataBaseConnection();
        $db    = $dbc->getDB('mongs', 'AutoKPI');
        $table  = 'interfereCellQuarter_cell_quarter';
        $sql    = "select distinct day_id from $table";
        $this->type = 'AutoKPI:interCloud';
        return $this->getValue($db, $sql);

    }//end interfepointDate()


    /**
     * 获得弱覆盖小区列表
     *
     * @param Request $request HTTP请求
     *
     * @return void
     */
    function getweakCoverCells(Request $request)
    {
        $date     = $request['date'];
        $channel  = $request['channel'];
        $channelArr = explode(',', $channel);
        $city = Input::get('city');
        $dbc      = new DataBaseConnection();
        $dbname = $dbc->getMRDatabase($city);
        /*$rows = MroWeakCoverage_day::on($dbname)
                    // ->selectRaw('ratio110*100 as ratio110,cellName as cell,longitudeBD as longitude,latitudeBD as latitude,dir')
        ->selectRaw('avgRsrp,cellName as cell,longitudeBD as longitude,latitudeBD as latitude,dir')
                    ->where('dateId',$date)
                    ->whereIn('mroWeakCoverage_day.band',$channelArr)
                    ->leftJoin('GLOBAL.siteLte','siteLte.ecgi','=','mroWeakCoverage_day.ecgi')
                    ->get()
                    ->toArray();*/

        $channelStr = "'".implode("','", $channelArr)."'";
        $pdo = $dbc->getDB('MR', $dbname);
        $st = $pdo->prepare("/*!mycat: sql=select 1 from MRS */ select avgRsrp,cellName as cell,longitudeBD as longitude,latitudeBD as latitude,dir from `mroWeakCoverage_day` left join `siteLte` on `siteLte`.`ecgi` = `mroWeakCoverage_day`.`ecgi` where `dateId` = ? and `mroWeakCoverage_day`.`band` in ($channelStr);");
        $st->bindParam(1, $date);
        $st->execute();
        $rows = $st->fetchAll(PDO::FETCH_ASSOC);            

        echo json_encode($rows);

    }//end getweakCoverCells()


    /**
     * 弱覆盖云图获取有数据的日期集合
     *
     *@return void 
     */
    function getDateWithData()
    {
        $city       = Input::get('city');
        $city       = $this->check_input($city);
        $dbc        = new DataBaseConnection();
        $dbname     = $dbc->getMRDatabase($city);
        $db         = $dbc->getDB('MR', $dbname);
        $table      = 'mroWeakCoverage_day';
        $sql        = "select DISTINCT dateId from $table";
        $this->type = $dbname.':weakCoverCloud';
        return $this->getValue($db, $sql);
    }//end getDateWithData()


    /**
     * 检查数据
     *
     * @param array $value 数据
     *
     * @return array 
     */
    function check_input($value)
    {
        //去除斜杠
        if (get_magic_quotes_gpc()) {
            $value=stripslashes($value);
        }
        return $value;
    }


    /**
     * 获得弱覆盖小区
     *
     * @param Request $request HTTP请求
     *
     * @return void
     */
    function getweakCoverCellsBak(Request $request)
    {
        $date     = $request['date'];
        $minute   = $request['minute'];
        $channel  = $request['channel'];
        $city     = $request['citys'];
        $citysArr = [];
        $dbc      = new DataBaseConnection();
        $db       = $dbc->getMRDatabase($city);
        array_push($citysArr, $db);

        if ($minute == '0') {
            $date = $date.' 08:00:00';
        } else {
            $date = $date.' 18:00:00';
        }

        $result = [];
        foreach ($citysArr as $dataBase) {
            $db      = $dbc->getDB('MR', $dataBase);
            $channel = "'".$channel."'";
            $channel = str_replace(',', "','", $channel);
            $sql     = "SELECT ratio110*100 as ratio110,s.cellName as cell,s.longitudeBD as longitude ,s.latitudeBD as latitude,s.dir FROM mroWeakCoverage t LEFT JOIN GLOBAL.siteLte s ON t.ecgi = s.ecgi WHERE t.datetime_id = '".$date."'AND t.band IN ($channel)";
            // AND t.band IN ($channel)
            $rs = $db->query($sql, PDO::FETCH_ASSOC);
            $rs = $rs->fetchAll();
            foreach ($rs as $rss) {
                array_push($result, $rss);
            }
        }

        echo json_encode($result);

    }//end getweakCoverCellsBak()

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
                                                            PUSCH上行干扰电平,
                                                            PUCCH上行干扰电平,
                                                            prb100_avg,
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
