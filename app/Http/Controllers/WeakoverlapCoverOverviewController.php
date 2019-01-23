<?php

/**
 * WeakoverlapCoverOverviewController.php
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
use DateInterval;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use PDO;

/**
 * 重叠覆盖概览
 * Class WeakoverlapCoverOverviewController
 *
 * @category Controllers
 * @package  App\Http\Controllers
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class WeakoverlapCoverOverviewController extends Controller
{


    /**
     * 获得重叠覆盖数据
     *
     * @return string
     */
    public function getOverlapCoverData()
    {
        $startDate = new DateTime();
        $startDate->sub(new DateInterval('P2M'));
        $endDate = new DateTime();
        $endDate->sub(new DateInterval('P1D'));
        $startDateId = $startDate->format('Y-m-d');
        $endDateId   = $endDate->format('Y-m-d');

        $dbc       = new DataBaseConnection();
        $dataBases = $dbc->getMRDatabases();

        $startDateId = $startDateId." 00:00:00";
        $endDateId   = $endDateId." 00:00:00";

        $test = [];
        foreach ($dataBases as $dataBase) {
            $dbc = new DataBaseConnection();
            $db  = $dbc->getDB('MR', $dataBase);
            /*$sql = "select datetime_id as time, city, SUM(case when rate>0.05 then 1 else 0 end)/COUNT(*) as ratea  FROM mroOverCoverage WHERE city is not null and datetime_id BETWEEN '".$startDateId."' and '".$endDateId."' GROUP BY datetime_id, city ORDER BY datetime_id";
*/
            $city = $dbc->getMRToCity($dataBase);
            $sql = "select HIGH_PRIORITY dateId as time, '$city' as city, SUM(case when rate>0.05 then 1 else 0 end)/COUNT(*) as ratio  FROM mroOverCoverage_day WHERE  dateId BETWEEN '".$startDateId."' and '".$endDateId."' GROUP BY dateId ORDER BY dateId";
            $result = $db->query($sql, PDO::FETCH_ASSOC)->fetchall();

            foreach ($result as $ar) {
                array_push($test, $ar);
            }
        }

        return $this->getHighChartData($test);

    }//end getOverlapCoverData()


    /**
     * 生成HighChart数据
     *
     * @param array $test 查询结果
     *
     * @return string
     */
    public function getHighChartData($test)
    {
        $series   = array();
        $category = array();
        foreach ($test as $item) {
            $city  = $item["city"];
            $ratio = $item["ratio"];
            $time  = strtotime($item["time"]);
            $arr   = array();
            if (array_search($city, $category) === false) {
                $category[] = $city;
            }

            if (!array_key_exists($city, $series)) {
                $series[$city] = array();
            }

            array_push($arr, (floatval($time) * 1000));
            array_push($arr, floatval($ratio));
            $series[$city][] = $arr;
        }

        $data['series'] = array();
        foreach ($series as $key => $value) {
            $data['series'][] = [
                                 'name' => $key,
                                 'data' => $value,
                                ];
        }

        return json_encode($data);

    }//end getHighChartData()


}//end class
