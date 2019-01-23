<?php

/**
 * InterfereController.php
 *
 * @category Controllers
 * @package  App\Http\Controllers
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
namespace App\Http\Controllers;

use App\Http\Requests;
use DateInterval;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Models\AutoKPI\InterfereRate_city_day;

/**
 * 干扰分析
 * Class InterfereController
 *
 * @category Controllers
 * @package  App\Http\Controllers
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class InterfereController extends Controller
{


    /**
     * 获得干扰数据
     *
     * @return string
     */
    public function getInterfereData()
    {
        $startDate = new DateTime();
        $startDate->sub(new DateInterval('P2M'));
        $endDate = new DateTime();
        $endDate->sub(new DateInterval('P1D'));
        $startDateId = $startDate->format('Y-m-d');
        $endDateId   = $endDate->format('Y-m-d');
        $result      = InterfereRate_city_day::selectRaw('day_id as time,city,高干扰小区占比 as ratio')
                            ->whereBetween('day_id', [$startDateId,$endDateId])
                            ->get();
        return $this->getHighChartData($result);

    }//end getInterfereData()


    /**
     * 生成HighChart图表数据
     *
     * @param array $result 查询结果
     *
     * @return string
     */
    public function getHighChartData($result)
    {
        $series   = array();
        $category = array();
        foreach ($result as $item) {
            $city  = $item->city;
            $ratio = $item->ratio;
            $time  = strtotime($item->time);
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
