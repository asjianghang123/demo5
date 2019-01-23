<?php

/**
 * NetworkChartsController.php
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
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Utils\LocalizationUtil;
use App\Models\NBM\EutranCellTdd_city_Hour;
use App\Models\AutoKPI\SysCoreTemp_city_hour;

/**
 * 指标概览
 * Class NetworkChartsController
 *
 * @category Controllers
 * @package  App\Http\Controllers
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class NetworkChartsController extends Controller
{
    /**
     * 日期字串
     * 
     * @var string $datetime_id 日期函数字串
     */
    protected $datetime_id = 'concat(day_id,\' \', concat(if(hour_id>=10,hour_id,concat(0,hour_id)),\':00:00\'))';


    /**
     * 生成Video仪表盘数据
     *
     * @return array
     */
    public function getVideoGauge()
    {
        $cityChinese = Input::get('city');
        if ($cityChinese == 'province') {
            $row   = EutranCellTdd_city_Hour::selectRaw('100 * (SUM(ERAB_NbrSuccEstab_2) / SUM(ERAB_NbrAttEstab_2) * SUM(RRC_SuccConnEstab) / SUM(RRC_AttConnEstab)) as kpi')
                        ->groupBy(['date_id','hour_id'])
                        ->orderBy('date_id', 'desc')
                        ->orderBy('hour_id', 'desc')
                        ->first()
                        ->toArray();
            $items = array();
            array_push($items, round(floatval($row['kpi']), 2));

            $row   = EutranCellTdd_city_Hour::selectRaw('100 * (SUM(ERAB_NbrReqRelEnb_2) - SUM(ERAB_NbrReqRelEnb_Normal_2) + SUM(ERAB_HoFail_2)) / SUM(ERAB_NbrSuccEstab_2) as kpi')
                        ->groupBy(['date_id','hour_id'])
                        ->orderBy('date_id', 'desc')
                        ->orderBy('hour_id', 'desc')
                        ->first()
                        ->toArray();
            array_push($items, round(floatval($row['kpi']), 2));

            $row   = EutranCellTdd_city_Hour::selectRaw('100 * SUM(IRATHO_SuccOutGeran) / SUM(IRATHO_AttOutGeran) as kpi')
                        ->groupBy(['date_id','hour_id'])
                        ->orderBy('date_id', 'desc')
                        ->orderBy('hour_id', 'desc')
                        ->first()
                        ->toArray();
            array_push($items, round(floatval($row['kpi']), 2));
            return $items;
        } else {
            $dbc  = new DataBaseConnection();
            $city = $dbc->getNbiOptions($cityChinese);

            $city  = trim($city);
            $row   = EutranCellTdd_city_Hour::selectRaw('100 * (SUM(ERAB_NbrSuccEstab_2) / SUM(ERAB_NbrAttEstab_2) * SUM(RRC_SuccConnEstab) / SUM(RRC_AttConnEstab)) as kpi')
                        ->where('city', $city)
                        ->groupBy(['date_id','hour_id'])
                        ->orderBy('date_id', 'desc')
                        ->orderBy('hour_id', 'desc')
                        ->first()
                        ->toArray();
            $items = array();
            array_push($items, round(floatval($row['kpi']), 2));

            $row   = EutranCellTdd_city_Hour::selectRaw('100 * (SUM(ERAB_NbrReqRelEnb_2) - SUM(ERAB_NbrReqRelEnb_Normal_2) + SUM(ERAB_HoFail_2)) / SUM(ERAB_NbrSuccEstab_2) as kpi')
                        ->where('city', $city)
                        ->groupBy(['date_id','hour_id'])
                        ->orderBy('date_id', 'desc')
                        ->orderBy('hour_id', 'desc')
                        ->first()
                        ->toArray();
            array_push($items, round(floatval($row['kpi']), 2));

            $row   = EutranCellTdd_city_Hour::selectRaw('100 * SUM(IRATHO_SuccOutGeran) / SUM(IRATHO_AttOutGeran) as kpi')
                        ->where('city', $city)
                        ->groupBy(['date_id' , 'hour_id'])
                        ->orderBy('date_id', 'desc')
                        ->orderBy('hour_id', 'desc')
                        ->first()
                        ->toArray();
            array_push($items, round(floatval($row['kpi']), 2));
            return $items;
        }//end if

    }//end getVideoGauge()


    /**
     * 生成Volte仪表盘数据
     *
     * @return array
     */
    public function getVolteGauge()
    {
        $cityChinese = Input::get('city');
        if ($cityChinese == 'province') {
            $row   = EutranCellTdd_city_Hour::selectRaw('100*(SUM(ERAB_NbrSuccEstab_1)/SUM(ERAB_NbrAttEstab_1)*SUM(RRC_SuccConnEstab)/SUM(RRC_AttConnEstab)) as kpi')
                        ->groupBy(['date_id','hour_id'])
                        ->orderBy('date_id', 'desc')
                        ->orderBy('hour_id', 'desc')
                        ->first()
                        ->toArray();
            $items = array();
            array_push($items, round(floatval($row['kpi']), 2));

            $row   = EutranCellTdd_city_Hour::selectRaw('100 * (SUM(ERAB_NbrReqRelEnb_1) - SUM(ERAB_NbrReqRelEnb_Normal_1) + SUM(ERAB_HoFail_1)) / SUM(ERAB_NbrSuccEstab_1) as kpi')
                        ->groupBy(['date_id','hour_id'])
                        ->orderBy('date_id', 'desc')
                        ->orderBy('hour_id', 'desc')
                        ->first()
                        ->toArray();
            array_push($items, round(floatval($row['kpi']), 2));

            $row   = EutranCellTdd_city_Hour::selectRaw('100*(SUM(HO_SuccOutInterEnbS1_1)+SUM(HO_SuccOutInterEnbX2_1)+SUM(HO_SuccOutIntraEnb_1))/(SUM(HO_AttOutInterEnbS1_1)+SUM(HO_AttOutInterEnbX2_1)+SUM(HO_AttOutIntraEnb_1)) as kpi')
                        ->groupBy(['date_id','hour_id'])
                        ->orderBy('date_id', 'desc')
                        ->orderBy('hour_id', 'desc')
                        ->first()
                        ->toArray();
            array_push($items, round(floatval($row['kpi']), 2));
            
            $row  = EutranCellTdd_city_Hour::selectRaw('100*IRATHO_SuccOutGeran/IRATHO_AttOutGeran as kpi')
                        ->groupBy(['date_id','hour_id'])
                        ->orderBy('date_id', 'desc')
                        ->orderBy('hour_id', 'desc')
                        ->first()
                        ->toArray();
            array_push($items, round(floatval($row['kpi']), 2));
            return $items;
        } else {
            $dbc  = new DataBaseConnection();
            $city = $dbc->getNbiOptions($cityChinese);

            $city  = trim($city);
            $row   = EutranCellTdd_city_Hour::selectRaw('100*(SUM(ERAB_NbrSuccEstab_1)/SUM(ERAB_NbrAttEstab_1)*SUM(RRC_SuccConnEstab)/SUM(RRC_AttConnEstab)) as kpi')
                        ->where('city', $city)
                        ->groupBy(['date_id','hour_id'])
                        ->orderBy('date_id', 'desc')
                        ->orderBy('hour_id', 'desc')
                        ->first()
                        ->toArray();
            $items = array();
            array_push($items, round(floatval($row['kpi']), 2));

            $row   = EutranCellTdd_city_Hour::selectRaw('100 * (SUM(ERAB_NbrReqRelEnb_1) - SUM(ERAB_NbrReqRelEnb_Normal_1) + SUM(ERAB_HoFail_1)) / SUM(ERAB_NbrSuccEstab_1) as kpi')
                        ->where('city', $city)
                        ->groupBy(['date_id','hour_id'])
                        ->orderBy('date_id', 'desc')
                        ->orderBy('hour_id', 'desc')
                        ->first()
                        ->toArray();
            array_push($items, round(floatval($row['kpi']), 2));

            $row = EutranCellTdd_city_Hour::selectRaw('100*(SUM(HO_SuccOutInterEnbS1_1)+SUM(HO_SuccOutInterEnbX2_1)+SUM(HO_SuccOutIntraEnb_1))/(SUM(HO_AttOutInterEnbS1_1)+SUM(HO_AttOutInterEnbX2_1)+SUM(HO_AttOutIntraEnb_1)) as kpi')
                        ->where('city', $city)
                        ->groupBy(['date_id' , 'hour_id'])
                        ->orderBy('date_id', 'desc')
                        ->orderBy('hour_id', 'desc')
                        ->first()
                        ->toArray();
            array_push($items, round(floatval($row['kpi']), 2));

            $row = EutranCellTdd_city_Hour::selectRaw('100*IRATHO_SuccOutGeran/IRATHO_AttOutGeran as kpi')
                        ->where('city', $city)
                        ->groupBy(['date_id','hour_id'])
                        ->orderBy('date_id', 'desc')
                        ->orderBy('hour_id', 'desc')
                        ->first()
                        ->toArray();
            array_push($items, round(floatval($row['kpi']), 2));
            return $items;
        }//end if

    }//end getVolteGauge()


    /**
     * 生成关键三项仪表盘数据
     *
     * @return array
     */
    public function getThreeKeysGauge()
    {
        $cityChinese = Input::get('city');
        if ($cityChinese == 'province') {
            $row   = SysCoreTemp_city_hour::orderBy('id', 'desc')->first()->toArray();
            $items = array();
            array_push($items, round(floatval($row['无线接通率']), 2), round(floatval($row['无线掉线率']), 2), round(floatval($row['切换成功率']), 2));
            $result         = array();
            $result['data'] = $items;
            return $result;
        } else {
            $dbc  = new DataBaseConnection();
            $city = $dbc->getENCity($cityChinese);

            $city  = trim($city);
            $row   = SysCoreTemp_city_hour::where('city', $city)->orderBy('id', 'desc')->first()->toArray();
            $items = array();
            array_push($items, round(floatval($row['无线接通率']), 2), round(floatval($row['无线掉线率']), 2), round(floatval($row['切换成功率']), 2));
            $result         = array();
            $result['data'] = $items;
            return $result;
        }//end if

    }//end getThreeKeysGauge()


    /**
     * 生成低接入趋势图
     *
     * @return string
     */
    public function getLowAccess()
    {
        $endTime   = time();
        $startTime = strtotime('-60 days', $endTime);
        $startTime = date('Y-m-d', $startTime);
        $strtotime = 'UNIX_TIMESTAMP(day_id)*1000 ';
        $days      = 60;
        $arr       = array();
        for ($i = 0; $i < $days; $i++) {
            $arr[] = ((strtotime($startTime) + $i * 86400 - 28800) * 1000);
        }

        $strTime = implode('\',\'', $arr);
        $result  = SysCoreTemp_city_hour::selectRaw('city,'.$strtotime.' as datetime_id,无线接通率 as kpi')
                        ->whereRaw($strtotime.'in (\''.$strTime.'\')')
                        ->groupBy(['city','day_id'])
                        ->get();
        return $this->getStrToTimeTest($result);

    }//end getLowAccess()


    /**
     * 生成切换差趋势图
     *
     * @param string $result 查询结果
     *
     * @return string
     */
    public function getStrToTimeTest($result)
    {
        $localUtil = new LocalizationUtil();
        $result = $localUtil->localization($result);
        $series   = array();
        $category = array();
        $array    = array();
        foreach ($result as $item) {
            $city     = $item->city;
            $datetime = $item->datetime_id;
            $kpi      = $item->kpi;
            $arr      = array();
            if (array_search($city, $category) === false) {
                $category[] = $city;
            }

            if (!array_key_exists($city, $series)) {
                $series[$city] = array();
            }

            array_push($arr, floatval($datetime));
            array_push($arr, round(floatval($kpi), 2));
            $series[$city][] = $arr;
            array_push($array, floatval($kpi));
        }

        $maxPos         = array_search(max($array), $array);
        $max            = $array[$maxPos];
        $minPos         = array_search(min($array), $array);
        $min            = $array[$minPos];
        $yAxis          = $this->getYAxis($max, $min);
        $data['yAxis']  = $yAxis;
        $data['series'] = array();
        foreach ($series as $key => $value) {
            $data['series'][] = [
                                 'name' => $key,
                                 'data' => $value,
                                ];
        }

        return json_encode($data);

    }//end getStrToTimeTest()


    /**
     * 生成YAxis
     *
     * @param float $max 最大值
     * @param float $min 最小值
     *
     * @return array
     */
    protected function getYAxis($max, $min)
    {
        $yAxis  = array();
        $max    = ceil($max);
        $min    = floor($min);
        $yAxis0 = $min;
        $yAxis2 = round((($min + $max) / 2), 2);
        $yAxis1 = round((($min + $yAxis2) / 2), 2);
        $yAxis4 = $max;
        $yAxis3 = round((($max + $yAxis2) / 2), 2);
        $yAxis5 = ($yAxis4 + 0.0001);
        array_push($yAxis, $yAxis0, $yAxis1, $yAxis2, $yAxis3, $yAxis4, $yAxis5);
        return $yAxis;

    }//end getYAxis()


    /**
     * 获得低接入趋势数据
     *
     * @param mixed $request HTTP请求
     *
     * @return string
     */
    public function getLowAccessDefine(Request $request)
    {
        $dataFrom  = $request['dataFrom'];
        $dataTo    = $request['dataTo'];
        $endTime   = time();
        $startTime = strtotime('-60 days', $endTime);
        $startTime = date('Y-m-d', $startTime);

        $strtotime = 'UNIX_TIMESTAMP(day_id)*1000 ';
        $days      = 60;
        $arr       = array();
        for ($i = 0; $i < $days; $i++) {
            $arr[] = ((strtotime($startTime) + $i * 86400 - 28800) * 1000);
        }

        $strTime = implode('\',\'', $arr);
        $result  = SysCoreTemp_city_hour::selectRaw('city,'.$strtotime.' as datetime_id,无线接通率 as kpi')
                        ->whereRaw($strtotime.'in (\''.$strTime.'\')')
                        ->groupBy(['city','day_id'])
                        ->get();
        return $this->getStrToTime($result, $dataFrom, $dataTo);

    }//end getLowAccessDefine()


    /**
     * 获得切换差趋势数据
     *
     * @param array  $result   查询结果
     * @param string $dataFrom 开始时间
     * @param string $dataTo   结束时间
     *
     * @return string
     */
    public function getStrToTime($result, $dataFrom, $dataTo)
    {
        $series   = array();
        $category = array();
        $array    = array();
        foreach ($result as $item) {
            $city     = $item->city;
            $datetime = $item->datetime_id;
            $kpi      = $item->kpi;
            $arr      = array();
            if (array_search($city, $category) === false) {
                $category[] = $city;
            }

            if (!array_key_exists($city, $series)) {
                $series[$city] = array();
            }

            array_push($arr, floatval($datetime));
            array_push($arr, round(floatval($kpi), 2));
            $series[$city][] = $arr;
            array_push($array, floatval($kpi));
        }

        $max            = $dataTo;
        $min            = $dataFrom;
        $yAxis          = $this->getYAxis($max, $min);
        $data['yAxis']  = $yAxis;
        $data['series'] = array();
        foreach ($series as $key => $value) {
            $data['series'][] = [
                                 'name' => $key,
                                 'data' => $value,
                                ];
        }

        return json_encode($data);

    }//end getStrToTime()


    /**
     * 获得高掉线趋势数据
     *
     * @param mixed $request HTTP请求
     *
     * @return string
     */
    public function getHighLostDefine(Request $request)
    {
        $dataFrom  = $request['dataFrom'];
        $dataTo    = $request['dataTo'];
        $endTime   = time();
        $startTime = strtotime('-60 days', $endTime);
        $startTime = date('Y-m-d', $startTime);
        $strtotime = 'UNIX_TIMESTAMP(day_id)*1000 ';
        $days      = 60;
        $arr       = array();
        for ($i = 0; $i < $days; $i++) {
            $arr[] = ((strtotime($startTime) + $i * 86400 - 28800) * 1000);
        }

        $strTime = implode('\',\'', $arr);
        $result  = SysCoreTemp_city_hour::selectRaw('city,'.$strtotime.' as datetime_id,无线掉线率 as kpi')
                        ->whereRaw($strtotime.'in (\''.$strTime.'\')')
                        ->groupBy(['city','day_id'])
                        ->get();
        return $this->getStrToTime($result, $dataFrom, $dataTo);

    }//end getHighLostDefine()


    /**
     * 获得切换差趋势数据
     *
     * @param mixed $request HTTP请求
     *
     * @return string
     */
    public function getBadHandoverDefine(Request $request)
    {
        $dataFrom  = $request['dataFrom'];
        $dataTo    = $request['dataTo'];
        $endTime   = time();
        $startTime = strtotime('-60 days', $endTime);
        $startTime = date('Y-m-d', $startTime);
        $strtotime = 'UNIX_TIMESTAMP(day_id)*1000 ';
        $days      = 60;
        $arr       = array();
        for ($i = 0; $i < $days; $i++) {
            $arr[] = ((strtotime($startTime) + $i * 86400 - 28800) * 1000);
        }

        $strTime = implode('\',\'', $arr);
        $result  = SysCoreTemp_city_hour::selectRaw('city,'.$strtotime.' as datetime_id,切换成功率 as kpi')
                        ->whereRaw($strtotime.'in (\''.$strTime.'\')')
                        ->groupBy(['city','day_id'])
                        ->get();
        return $this->getStrToTime($result, $dataFrom, $dataTo);

    }//end getBadHandoverDefine()


    /**
     * 生成低接入自定义Y轴数据
     *
     * @param mixed $request HTTP请求
     *
     * @return string
     */
    public function getLowAccessTrendMore(Request $request)
    {
        $dataFrom  = $request['dataFrom'];
        $dataTo    = $request['dataTo'];
        $strtotime = 'UNIX_TIMESTAMP(concat(day_id,\' \', concat(if(hour_id>=10,hour_id,concat(0,hour_id)),\':00:00\')))*1000 ';
        $startTime = date('Y-m-d H:00:00', strtotime('-60 days'));
        $days      = (60 * 24);
        $arr       = array();
        for ($i = 0; $i < $days; $i++) {
            $arr[] = ((strtotime($startTime) + $i * 3600) * 1000);
        }

        $strTime = implode('\',\'', $arr);
        $result  = SysCoreTemp_city_hour::selectRaw('city,'.$strtotime.' as datetime_id,无线接通率 as kpi')
                        ->whereRaw($strtotime.'in (\''.$strTime.'\')')
                        ->groupBy(['city','day_id'])
                        ->get();
        return $this->getStrToTime($result, $dataFrom, $dataTo);

    }//end getLowAccessTrendMore()


    /**
     * 获得高掉线自定义Y轴数据
     *
     * @param mixed $request HTTP请求
     *
     * @return string
     */
    public function getHighLostTrendMore(Request $request)
    {
        $dataFrom  = $request['dataFrom'];
        $dataTo    = $request['dataTo'];
        $strtotime = 'UNIX_TIMESTAMP(concat(day_id,\' \', concat(if(hour_id>=10,hour_id,concat(0,hour_id)),\':00:00\')))*1000 ';
        $startTime = date('Y-m-d H:00:00', strtotime('-60 days'));
        $days      = (60 * 24);
        $arr       = array();
        for ($i = 0; $i < $days; $i++) {
            $arr[] = ((strtotime($startTime) + $i * 3600) * 1000);
        }

        $strTime = implode('\',\'', $arr);
        $result  = SysCoreTemp_city_hour::selectRaw('city,'.$strtotime.' as datetime_id,无线掉线率 as kpi')
                        ->whereRaw($strtotime.'in (\''.$strTime.'\')')
                        ->groupBy(['city','day_id'])
                        ->get();
        return $this->getStrToTime($result, $dataFrom, $dataTo);

    }//end getHighLostTrendMore()


    /**
     * 获得低接入趋势
     *
     * @return string
     */
    public function getLowAccessTrend()
    {
        $strtotime = '(UNIX_TIMESTAMP(concat(day_id,\' \', concat(if(hour_id>=10,hour_id,concat(0,hour_id)),\':00:00\')))+8*3600)*1000 ';
        $startTime = date('Y-m-d H:00:00', strtotime('-60 days'));
        $days      = (60 * 24);
        $arr       = array();
        for ($i = 0; $i < $days; $i++) {
            $arr[] = ((strtotime($startTime) + $i * 3600 + 8 * 3600) * 1000);
        }

        $strTime = implode('\',\'', $arr);

        $sql    = 'select city,'.$strtotime.'as datetime_id'.',CASE city WHEN "changzhou" THEN IF(concat(day_id," ", concat(if(hour_id>=10,hour_id,concat(0,hour_id)),":00:00"
))<"2016-11-24 15:00:00",SUM(无线接通率),SUM(无线接通率)/2) WHEN "nantong" THEN IF(concat(day_id," ", concat(if(hour_id>=10,hour_id,concat(0,hour_id)),":00:00"
))<"2016-12-05 17:00:00",SUM(无线接通率),SUM(无线接通率)/2) ELSE SUM(无线接通率) END as kpi from SysCoreTemp_city_hour where '.$strtotime.'in (\''.$strTime.'\') GROUP BY city,day_id,hour_id';
        $result = DB::connection('autokpi')->select($sql);
        return $this->getStrToTimeTest($result);

    }//end getLowAccessTrend()


    /**
     * 获得切换差趋势数据(MORE?)
     *
     * @param Request $request HTTP请求
     *
     * @return string
     */
    public function getBadHandoverTrendMore(Request $request)
    {
        $dataFrom  = $request['dataFrom'];
        $dataTo    = $request['dataTo'];
        $strtotime = 'UNIX_TIMESTAMP(concat(day_id,\' \', concat(if(hour_id>=10,hour_id,concat(0,hour_id)),\':00:00\')))*1000 ';
        $startTime = date('Y-m-d H:00:00', strtotime('-60 days'));
        $days      = (60 * 24);
        $arr       = array();
        for ($i = 0; $i < $days; $i++) {
            $arr[] = ((strtotime($startTime) + $i * 3600) * 1000);
        }

        $strTime = implode('\',\'', $arr);
        $result  = SysCoreTemp_city_hour::selectRaw('city,'.$strtotime.' as datetime_id,切换成功率 as kpi')
                        ->whereRaw($strtotime.'in (\''.$strTime.'\')')
                        ->groupBy(['city','day_id'])
                        ->get();
        return $this->getStrToTime($result, $dataFrom, $dataTo);

    }//end getBadHandoverTrendMore()


    /**
     * 生成HighChart图表
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
            $city     = $item->city;
            $datetime = $item->datetime_id;
            $kpi      = $item->kpi;
            if (array_search($datetime, $category) === false) {
                $category[] = $datetime;
            }

            if (!array_key_exists($city, $series)) {
                $series[$city] = array();
            }

            $series[$city][] = floatval($kpi);
        }

        $data['category'] = $category;
        $data['series']   = array();
        foreach ($series as $key => $value) {
            $data['series'][] = [
                                 'name' => $key,
                                 'data' => $value,
                                ];
        }

        return json_encode($data);

    }//end getHighChartData()


    /**
     * 获得高掉线趋势
     *
     * @param mixed $request HTTP请求
     *
     * @return string
     */
    public function getHighLostTrend(Request $request)
    {
        $strtotime = '(UNIX_TIMESTAMP(concat(day_id,\' \', concat(if(hour_id>=10,hour_id,concat(0,hour_id)),\':00:00\')))+8*3600)*1000 ';
        $startTime = date('Y-m-d H:00:00', strtotime('-60 days'));
        $days      = (60 * 24);
        $arr       = array();
        for ($i = 0; $i < $days; $i++) {
            $arr[] = ((strtotime($startTime) + $i * 3600 + 8 * 3600) * 1000);
        }

        $strTime = implode('\',\'', $arr);

        $sql    = 'select city,'.$strtotime.'as datetime_id'.', CASE city WHEN "changzhou" THEN IF(concat(day_id," ", concat(if(hour_id>=10,hour_id,concat(0,hour_id)),":00:00"
))<"2016-11-24 15:00:00",SUM(无线掉线率),SUM(无线掉线率)/2) WHEN "nantong" THEN IF(concat(day_id," ", concat(if(hour_id>=10,hour_id,concat(0,hour_id)),":00:00"
))<"2016-12-05 17:00:00",SUM(无线掉线率),SUM(无线掉线率)/2) ELSE SUM(无线掉线率) END as kpi from SysCoreTemp_city_hour where '.$strtotime.'in (\''.$strTime.'\') GROUP BY city,day_id,hour_id';
        $result = DB::connection('autokpi')->select($sql);
        return $this->getStrToTimeTest($result);

    }//end getHighLostTrend()


    /**
     * 获得切换差趋势
     *
     * @return string
     */
    public function getBadHandoverTrend()
    {
        $strtotime = '(UNIX_TIMESTAMP(concat(day_id,\' \', concat(if(hour_id>=10,hour_id,concat(0,hour_id)),\':00:00\')))+8*3600)*1000 ';
        $startTime = date('Y-m-d H:00:00', strtotime('-60 days'));
        $days      = (60 * 24);
        $arr       = array();
        for ($i = 0; $i < $days; $i++) {
            $arr[] = ((strtotime($startTime) + $i * 3600 + 8 * 3600) * 1000);
        }

        $strTime = implode('\',\'', $arr);

        $sql    = 'select city,'.$strtotime.'as datetime_id'.', CASE city WHEN "changzhou" THEN IF(concat(day_id," ", concat(if(hour_id>=10,hour_id,concat(0,hour_id)),":00:00"
))<"2016-11-24 15:00:00",SUM(切换成功率),SUM(切换成功率)/2) WHEN "nantong" THEN IF(concat(day_id," ", concat(if(hour_id>=10,hour_id,concat(0,hour_id)),":00:00"
))<"2016-12-05 17:00:00",SUM(切换成功率),SUM(切换成功率)/2) ELSE SUM(切换成功率) END as kpi from SysCoreTemp_city_hour where '.$strtotime.'in (\''.$strTime.'\') group by city,day_id,hour_id';
        $result = DB::connection('autokpi')->select($sql);
        return $this->getStrToTimeTest($result);

    }//end getBadHandoverTrend()


    /**
     * 获得高掉线数据
     *
     * @return string
     */
    public function getHighLost()
    {
        $endTime   = time();
        $startTime = strtotime('-60 days', $endTime);
        $startTime = date('Y-m-d', $startTime);
        $strtotime = 'UNIX_TIMESTAMP(day_id)*1000 ';
        $days      = 60;
        $arr       = array();
        for ($i = 0; $i < $days; $i++) {
            $arr[] = ((strtotime($startTime) + $i * 86400 - 28800) * 1000);
        }

        $strTime = implode('\',\'', $arr);
        $result  = SysCoreTemp_city_hour::selectRaw('city,'.$strtotime.' as datetime_id,无线掉线率 as kpi')
                        ->whereRaw($strtotime.'in (\''.$strTime.'\')')
                        ->groupBy(['city','day_id'])
                        ->get();
        return $this->getStrToTimeTest($result);

    }//end getHighLost()


    /**
     * 获得切换差数据
     *
     * @return string
     */
    public function getBadHandover()
    {
        $endTime   = time();
        $startTime = strtotime('-60 days', $endTime);
        $startTime = date('Y-m-d', $startTime);
        $strtotime = 'UNIX_TIMESTAMP(day_id)*1000 ';
        $days      = 60;
        $arr       = array();
        for ($i = 0; $i < $days; $i++) {
            $arr[] = ((strtotime($startTime) + $i * 86400 - 28800) * 1000);
        }

        $strTime = implode('\',\'', $arr);
        $result  = SysCoreTemp_city_hour::selectRaw('city,'.$strtotime.' as datetime_id,切换成功率 as kpi')
                        ->whereRaw($strtotime.'in (\''.$strTime.'\')')
                        ->groupBy(['city','day_id'])
                        ->get();
        return $this->getStrToTimeTest($result);

    }//end getBadHandover()


    /**
     * 获得Erab切换成功率数据
     *
     * @return string
     */
    public function getErabSuccessHandover()
    {
        $endTime   = time();
        $startTime = strtotime('-60 days', $endTime);
        $startTime = date('Y-m-d', $startTime);
        $strtotime = 'UNIX_TIMESTAMP(date_id)*1000 ';
        $days      = 60;
        $arr       = array();
        for ($i = 0; $i < $days; $i++) {
            $arr[] = ((strtotime($startTime) + $i * 86400 - 28800) * 1000);
        }

        $strTime = implode('\',\'', $arr);
        $result  = EutranCellTdd_city_Hour::selectRaw('city,'.$strtotime.' as datetime_id,100*(SUM(ERAB_NbrSuccEstab_1)/SUM(ERAB_NbrAttEstab_1)*SUM(RRC_SuccConnEstab)/SUM(RRC_AttConnEstab)) as kpi')
                        ->whereRaw($strtotime.'in (\''.$strTime.'\')')
                        ->groupBy(['city','date_id'])
                        ->get();
        return $this->getStrToTimeTest($result);

    }//end getErabSuccessHandover()


    /**
     * 获得Erab切换成功率趋势
     *
     * @return string
     */
    public function getErabSuccessHandoverTrend()
    {
        $strtotime = '(UNIX_TIMESTAMP(concat(date_id,\' \', concat(if(hour_id>=10,hour_id,concat(0,hour_id)),\':00:00\')))+8*3600)*1000 ';
        $startTime = date('Y-m-d H:00:00', strtotime('-60 days'));
        $days      = (60 * 24);
        $arr       = array();
        for ($i = 0; $i < $days; $i++) {
            $arr[] = ((strtotime($startTime) + $i * 3600 + 8 * 3600) * 1000);
        }

        $strTime = implode('\',\'', $arr);
        $result  = EutranCellTdd_city_Hour::selectRaw('city,'.$strtotime.' as datetime_id,100*(SUM(ERAB_NbrSuccEstab_1)/SUM(ERAB_NbrAttEstab_1)*SUM(RRC_SuccConnEstab)/SUM(RRC_AttConnEstab)) as kpi')
                        ->whereRaw($strtotime.'in (\''.$strTime.'\')')
                        ->groupBy(['datetime_id','hour_id','city'])
                        ->get();
        return $this->getStrToTimeTest($result);

    }//end getErabSuccessHandoverTrend()


    /**
     * 获得Erab掉线率趋势
     *
     * @return string
     */
    public function getErabsLostTrend()
    {
        $strtotime = '(UNIX_TIMESTAMP(concat(date_id,\' \', concat(if(hour_id>=10,hour_id,concat(0,hour_id)),\':00:00\')))+8*3600)*1000 ';
        $startTime = date('Y-m-d H:00:00', strtotime('-60 days'));
        $days      = (60 * 24);
        $arr       = array();
        for ($i = 0; $i < $days; $i++) {
            $arr[] = ((strtotime($startTime) + $i * 3600 + 8 * 3600) * 1000);
        }

        $strTime = implode('\',\'', $arr);
        $result  = EutranCellTdd_city_Hour::selectRaw('city,'.$strtotime.' as datetime_id,100 * (SUM(ERAB_NbrReqRelEnb_1) - SUM(ERAB_NbrReqRelEnb_Normal_1) + SUM(ERAB_HoFail_1)) / SUM(ERAB_NbrSuccEstab_1) as kpi')
                        ->whereRaw($strtotime.'in (\''.$strTime.'\')')
                        ->groupBy(['datetime_id','hour_id','city'])
                        ->get();
        return $this->getStrToTimeTest($result);

    }//end getErabsLostTrend()


    /**
     * 获得无限成功率趋势
     *
     * @return string
     */
    public function getWirelessSuccTrend()
    {
        $strtotime = '(UNIX_TIMESTAMP(concat(date_id,\' \', concat(if(hour_id>=10,hour_id,concat(0,hour_id)),\':00:00\')))+8*3600)*1000 ';
        $startTime = date('Y-m-d H:00:00', strtotime('-60 days'));
        $days      = (60 * 24);
        $arr       = array();
        for ($i = 0; $i < $days; $i++) {
            $arr[] = ((strtotime($startTime) + $i * 3600 + 8 * 3600) * 1000);
        }

        $strTime = implode('\',\'', $arr);
        $result  = EutranCellTdd_city_Hour::selectRaw('city,'.$strtotime.' as datetime_id,100 * (SUM(HO_SuccOutInterEnbS1_1) + SUM(HO_SuccOutInterEnbX2_1) + SUM(HO_SuccOutIntraEnb_1)) / (SUM(HO_AttOutInterEnbS1_1) + SUM(HO_AttOutInterEnbX2_1) + SUM(HO_AttOutIntraEnb_1)) as kpi')
                        ->whereRaw($strtotime.'in (\''.$strTime.'\')')
                        ->groupBy(['datetime_id','hour_id','city'])
                        ->get();
        return $this->getStrToTimeTest($result);

    }//end getWirelessSuccTrend()


    /**
     * 获得Vlote切换成功率趋势
     *
     * @return string
     */
    public function getVolteHandoverTrend()
    {
        $strtotime = '(UNIX_TIMESTAMP(concat(date_id,\' \', concat(if(hour_id>=10,hour_id,concat(0,hour_id)),\':00:00\')))+8*3600)*1000 ';
        $startTime = date('Y-m-d H:00:00', strtotime('-60 days'));
        $days      = (60 * 24);
        $arr       = array();
        for ($i = 0; $i < $days; $i++) {
            $arr[] = ((strtotime($startTime) + $i * 3600 + 8 * 3600) * 1000);
        }

        $strTime = implode('\',\'', $arr);
        $result  = EutranCellTdd_city_Hour::selectRaw('city,'.$strtotime.' as datetime_id,100 * SUM(IRATHO_SuccOutGeran) / SUM(IRATHO_AttOutGeran) as kpi')
                        ->whereRaw($strtotime.'in (\''.$strTime.'\')')
                        ->groupBy(['datetime_id','hour_id','city'])
                        ->get();
        return $this->getStrToTimeTest($result);

    }//end getVolteHandoverTrend()


    /**
     * 获得ErabLost数据
     *
     * @return string
     */
    public function getErabsLost()
    {
        $endTime   = time();
        $startTime = strtotime('-60 days', $endTime);
        $startTime = date('Y-m-d', $startTime);
        $strtotime = 'UNIX_TIMESTAMP(date_id)*1000 ';
        $days      = 60;
        $arr       = array();
        for ($i = 0; $i < $days; $i++) {
            $arr[] = ((strtotime($startTime) + $i * 86400 - 28800) * 1000);
        }

        $strTime = implode('\',\'', $arr);
        $result  = EutranCellTdd_city_Hour::selectRaw('city,'.$strtotime.' as datetime_id,100 * (SUM(ERAB_NbrReqRelEnb_1) - SUM(ERAB_NbrReqRelEnb_Normal_1) + SUM(ERAB_HoFail_1)) / SUM(ERAB_NbrSuccEstab_1) as kpi')
                        ->whereRaw($strtotime.'in (\''.$strTime.'\')')
                        ->groupBy(['city','datetime_id'])
                        ->get();
        return $this->getStrToTimeTest($result);

    }//end getErabsLost()


    /**
     * 获得无线接入成功率数据
     *
     * @return string
     */
    public function getWirelessSucc()
    {
        $endTime   = time();
        $startTime = strtotime('-60 days', $endTime);
        $startTime = date('Y-m-d', $startTime);

        $strtotime = 'UNIX_TIMESTAMP(date_id)*1000 ';
        $days      = 60;
        $arr       = array();
        for ($i = 0; $i < $days; $i++) {
            $arr[] = ((strtotime($startTime) + $i * 86400 - 28800) * 1000);
        }

        $strTime = implode('\',\'', $arr);
        $result  = EutranCellTdd_city_Hour::selectRaw('city,'.$strtotime.' as datetime_id,100 * (SUM(HO_SuccOutInterEnbS1_1) + SUM(HO_SuccOutInterEnbX2_1) + SUM(HO_SuccOutIntraEnb_1)) / (SUM(HO_AttOutInterEnbS1_1) + SUM(HO_AttOutInterEnbX2_1) + SUM(HO_AttOutIntraEnb_1)) as kpi')
                        ->whereRaw($strtotime.'in (\''.$strTime.'\')')
                        ->groupBy(['city','datetime_id'])
                        ->get();
        return $this->getStrToTimeTest($result);

    }//end getWirelessSucc()


    /**
     * 获得Volte切换成功率数据
     *
     * @return string
     */
    public function getVolteHandover()
    {
        // day
        $endTime   = time();
        $startTime = strtotime('-60 days', $endTime);
        $startTime = date('Y-m-d', $startTime);

        $strtotime = 'UNIX_TIMESTAMP(date_id)*1000 ';
        $days      = 60;
        $arr       = array();
        for ($i = 0; $i < $days; $i++) {
            $arr[] = ((strtotime($startTime) + $i * 86400 - 28800) * 1000);
        }

        $strTime = implode('\',\'', $arr);
        $result  = EutranCellTdd_city_Hour::selectRaw('city,'.$strtotime.' as datetime_id,100 * SUM(IRATHO_SuccOutGeran) / SUM(IRATHO_AttOutGeran) as kpi')
                        ->whereRaw($strtotime.'in (\''.$strTime.'\')')
                        ->groupBy(['city','datetime_id'])
                        ->get();
        return $this->getStrToTimeTest($result);

    }//end getVolteHandover()


    /**
     * 生成无线成功率图表
     *
     * @return string
     */
    public function getChart1WireSucc()
    {
        $endTime   = time();
        $startTime = strtotime('-60 days', $endTime);
        $startTime = date('Y-m-d', $startTime);
        $strtotime = 'UNIX_TIMESTAMP(date_id)*1000 ';
        $days      = 60;
        $arr       = array();
        for ($i = 0; $i < $days; $i++) {
            $arr[] = ((strtotime($startTime) + $i * 86400 - 28800) * 1000);
        }

        $strTime = implode('\',\'', $arr);
        $result  = EutranCellTdd_city_Hour::selectRaw('city,'.$strtotime.' as datetime_id,100 * (SUM(ERAB_NbrSuccEstab_2) / SUM(ERAB_NbrAttEstab_2) * SUM(RRC_SuccConnEstab) / SUM(RRC_AttConnEstab)) as kpi')
                        ->whereRaw($strtotime.'in (\''.$strTime.'\')')
                        ->groupBy(['city','datetime_id'])
                        ->get();
        return $this->getStrToTimeTest($result);

    }//end getChart1WireSucc()


    /**
     * 生成ErbLost图表
     *
     * @return string
     */
    public function getChart1ErbLost()
    {
        $endTime   = time();
        $startTime = strtotime('-60 days', $endTime);
        $startTime = date('Y-m-d', $startTime);

        $strtotime = 'UNIX_TIMESTAMP(date_id)*1000 ';
        $days      = 60;
        $arr       = array();
        for ($i = 0; $i < $days; $i++) {
            $arr[] = ((strtotime($startTime) + $i * 86400 - 28800) * 1000);
        }

        $strTime = implode('\',\'', $arr);
        $result  = EutranCellTdd_city_Hour::selectRaw('city,'.$strtotime.' as datetime_id,100 * (SUM(ERAB_NbrReqRelEnb_2) - SUM(ERAB_NbrReqRelEnb_Normal_2) + SUM(ERAB_HoFail_2)) / SUM(ERAB_NbrSuccEstab_2) as kpi')
                        ->whereRaw($strtotime.'in (\''.$strTime.'\')')
                        ->groupBy(['city','datetime_id'])
                        ->get();
        return $this->getStrToTimeTest($result);

    }//end getChart1ErbLost()


    /**
     * 生成Video成功率图表
     *
     * @return string
     */
    public function getChart1VideoSucc()
    {
        $endTime   = time();
        $startTime = strtotime('-60 days', $endTime);
        $startTime = date('Y-m-d', $startTime);

        $strtotime = 'UNIX_TIMESTAMP(date_id)*1000 ';
        $days      = 60;
        $arr       = array();
        for ($i = 0; $i < $days; $i++) {
            $arr[] = ((strtotime($startTime) + $i * 86400 - 28800) * 1000);
        }

        $strTime = implode('\',\'', $arr);
        $result  = EutranCellTdd_city_Hour::selectRaw('city,'.$strtotime.' as datetime_id,100 * SUM(IRATHO_SuccOutGeran) / SUM(IRATHO_AttOutGeran) as kpi')
                        ->whereRaw($strtotime.'in (\''.$strTime.'\')')
                        ->groupBy(['city','datetime_id'])
                        ->get();
        return $this->getStrToTimeTest($result);

    }//end getChart1VideoSucc()


    /**
     * 生成Esrvcc切换指标图表
     *
     * @return string
     */
    public function getChart1EsrvccHander()
    {
        // day
        $endTime   = time();
        $startTime = strtotime('-60 days', $endTime);
        $startTime = date('Y-m-d', $startTime);

        $strtotime = 'UNIX_TIMESTAMP(date_id)*1000 ';
        $days      = 60;
        $arr       = array();
        for ($i = 0; $i < $days; $i++) {
            $arr[] = ((strtotime($startTime) + $i * 86400 - 28800) * 1000);
        }

        $strTime = implode('\',\'', $arr);
        $result  = EutranCellTdd_city_Hour::selectRaw('city,'.$strtotime.' as datetime_id,100 * (SUM(HO_SuccOutInterEnbS1_2) + SUM(HO_SuccOutInterEnbX2_2) + SUM(HO_SuccOutIntraEnb_2)) / (SUM(HO_AttOutInterEnbS1_2) + SUM(HO_AttOutInterEnbX2_2) + SUM(HO_AttOutIntraEnb_2))as kpi')
                        ->whereRaw($strtotime.'in (\''.$strTime.'\')')
                        ->groupBy(['city','datetime_id'])
                        ->get();
        return $this->getStrToTimeTest($result);

    }//end getChart1EsrvccHander()


    /**
     * 获得无线成功率趋势图表
     *
     * @return string
     */
    public function getChart1WireSuccTrend()
    {
        $strtotime = '(UNIX_TIMESTAMP(concat(date_id,\' \', concat(if(hour_id>=10,hour_id,concat(0,hour_id)),\':00:00\')))+8*3600)*1000 ';
        $startTime = date('Y-m-d H:00:00', strtotime('-60 days'));
        $days      = (60 * 24);
        $arr       = array();
        for ($i = 0; $i < $days; $i++) {
            $arr[] = ((strtotime($startTime) + $i * 3600 + 8 * 3600) * 1000);
        }

        $strTime = implode('\',\'', $arr);
        $result  = EutranCellTdd_city_Hour::selectRaw('city,'.$strtotime.' as datetime_id,100 * (SUM(ERAB_NbrSuccEstab_2) / SUM(ERAB_NbrAttEstab_2) * SUM(RRC_SuccConnEstab) / SUM(RRC_AttConnEstab)) as kpi')
                        ->whereRaw($strtotime.'in (\''.$strTime.'\')')
                        ->groupBy(['datetime_id','hour_id','city'])
                        ->get();
        return $this->getStrToTimeTest($result);

    }//end getChart1WireSuccTrend()


    /**
     * 获得Erb掉线率趋势图表
     *
     * @return string
     */
    public function getChart1ErbLostTrend()
    {
        $strtotime = '(UNIX_TIMESTAMP(concat(date_id,\' \', concat(if(hour_id>=10,hour_id,concat(0,hour_id)),\':00:00\')))+8*3600)*1000 ';
        $startTime = date('Y-m-d H:00:00', strtotime('-60 days'));
        $days      = (60 * 24);
        $arr       = array();
        for ($i = 0; $i < $days; $i++) {
            $arr[] = ((strtotime($startTime) + $i * 3600 + 8 * 3600) * 1000);
        }

        $strTime = implode('\',\'', $arr);
        $result  = EutranCellTdd_city_Hour::selectRaw('city,'.$strtotime.' as datetime_id,100 * (SUM(ERAB_NbrReqRelEnb_2) - SUM(ERAB_NbrReqRelEnb_Normal_2) + SUM(ERAB_HoFail_2)) / SUM(ERAB_NbrSuccEstab_2) as kpi')
                        ->whereRaw($strtotime.'in (\''.$strTime.'\')')
                        ->groupBy(['datetime_id','hour_id','city'])
                        ->get();
        return $this->getStrToTimeTest($result);

    }//end getChart1ErbLostTrend()


    /**
     * 生成Video接入成功率趋势图表
     *
     * @return string
     */
    public function getChart1VideoSuccTrend()
    {
        $strtotime = '(UNIX_TIMESTAMP(concat(date_id,\' \', concat(if(hour_id>=10,hour_id,concat(0,hour_id)),\':00:00\')))+8*3600)*1000 ';
        $startTime = date('Y-m-d H:00:00', strtotime('-60 days'));
        $days      = (60 * 24);
        $arr       = array();
        for ($i = 0; $i < $days; $i++) {
            $arr[] = ((strtotime($startTime) + $i * 3600 + 8 * 3600) * 1000);
        }

        $strTime = implode('\',\'', $arr);
        $result  = EutranCellTdd_city_Hour::selectRaw('city,'.$strtotime.' as datetime_id,100 * SUM(IRATHO_SuccOutGeran) / SUM(IRATHO_AttOutGeran) as kpi')
                        ->whereRaw($strtotime.'in (\''.$strTime.'\')')
                        ->groupBy(['datetime_id','hour_id','city'])
                        ->get();
        return $this->getStrToTimeTest($result);

    }//end getChart1VideoSuccTrend()


    /**
     * 生成Esrvcc切换成功率趋势图表
     *
     * @return string
     */
    public function getChart1EsrvccHanderTrend()
    {
        $strtotime = '(UNIX_TIMESTAMP(concat(date_id,\' \', concat(if(hour_id>=10,hour_id,concat(0,hour_id)),\':00:00\')))+8*3600)*1000 ';
        $startTime = date('Y-m-d H:00:00', strtotime('-60 days'));
        $days      = (60 * 24);
        $arr       = array();
        for ($i = 0; $i < $days; $i++) {
            $arr[] = ((strtotime($startTime) + $i * 3600 + 8 * 3600) * 1000);
        }

        $strTime = implode('\',\'', $arr);
        $result  = EutranCellTdd_city_Hour::selectRaw('city,'.$strtotime.' as datetime_id,100*SUM(IRATHO_SuccOutGeran)/SUM(IRATHO_AttOutGeran)as kpi')
                        ->whereRaw($strtotime.'in (\''.$strTime.'\')')
                        ->groupBy(['datetime_id','hour_id','city'])
                        ->get();
        return $this->getStrToTimeTest($result);

    }//end getChart1EsrvccHanderTrend()


    /**
     * 获得NBI英文城市列表
     *
     * @return array
     */
    protected function loadNbiEngCity()
    {
        $result = array();
        $lines  = file("common/txt/mapCitysNbi.txt");
        foreach ($lines as $line) {
            $pair = explode("=", $line);
            $result[$pair[0]] = $pair[1];
        }

        return $result;

    }//end loadNbiEngCity()


    /**
     * 获得英文城市列表
     *
     * @return array
     */
    protected function loadEngCity()
    {
        $result = array();
        $lines  = file("common/txt/mapCitys.txt");
        foreach ($lines as $line) {
            $pair = explode("=", $line);
            $result[$pair[0]] = $pair[1];
        }

        return $result;

    }//end LoadEngCity()


}//end class
