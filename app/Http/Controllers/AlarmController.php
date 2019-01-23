<?php

/**
 * AlarmController.php
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
use Illuminate\Support\Facades\Input;
use App\Models\Alarm\FMA_alarm_list;
use App\Models\Alarm\FMA_alarm_log_group_by_city_date;
use App\Models\Alarm\FMA_alarm_log;

/**
 * 告警处理
 * Class AlarmController
 *
 * @category Controllers
 * @package  App\Http\Controllers
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class AlarmController extends Controller
{


    /**
     * 获得当前告警
     *
     * @return string
     */
    public function getCurrentAlarm()
    {
        $conn         = DB::connection('alarm');
        $rs           = FMA_alarm_list::selectRaw('DISTINCT city as category')
                            ->whereNotNull('city')
                            ->where('city', '!=', '')
                            ->orderBy('city')
                            ->get();
        $categories   = $this->getHighChartCategory($rs);
        $rs           = FMA_alarm_list::groupBy('Perceived_severity')->orderBy('Perceived_severity', 'asc')->get();
        $series       = array();
        foreach ($rs as $item) {
            $type   = $item->Perceived_severity;
            $rs     = FMA_alarm_list::selectRaw(
                                                "city as category,
                                                case Perceived_severity
                                                when 0 then 'Indeterminate(事件)'
                                                when 1 then 'Critical'
                                                when 2 then 'Major'
                                                when 3 then 'Minor'
                                                when 4 then 'Warning'
                                                when 5 then 'Cleared'
                                                end as type,
                                                count(*) as num")
                                        ->where('Perceived_severity', $type)
                                        ->whereNotNull('city')
                                        ->where('city', '!=', '')
                                        ->groupBy(['city','Perceived_severity'])
                                        ->orderBy('city', 'asc')
                                        ->get();
            $series = $this->getHighChartSeries($rs, $series, $categories, $type);
        }

        $data['category'] = $categories;
        $data['series']   = array();
        foreach ($series as $key => $value) {
            $data['series'][] = [
                                 'name' => $key,
                                 'data' => $value,
                                ];
        }

        return json_encode($data);

    }//end getCurrentAlarm()


    /**
     * 获得categories
     *
     * @param array $rs 查询结果
     *
     * @return array
     */
    public function getHighChartCategory($rs)
    {
        $categories = array();
        foreach ($rs as $item) {
            $category = $item->category;
            if (array_search($category, $categories) === false) {
                $categories[] = $category;
            }
        }

        return $categories;

    }//end getHighChartCategory()


    /**
     * 获取chart图形的 series数据
     *
     * @param array  $rs         查询结果
     * @param array  $series     时序数据
     * @param array  $categories categories
     * @param string $type       告警类型
     *
     * @return mixed
     */
    public function getHighChartSeries($rs, $series, $categories, $type)
    {
        if ($rs) {
            $l = 0;
            $k = 0;
            foreach ($rs as $item) {
                $category  = $item->category;
                $num       = $item->num;
                $seriesKey = $item->type;
                for ($i = $k; $i < count($categories); $i++) {
                    if ($category == $categories[$i]) {
                        if (!array_key_exists($seriesKey, $series)) {
                            $series[$seriesKey] = array();
                        }

                        $series[$seriesKey][] = floatval($num);
                        $k = ($i + 1);
                        $l++;
                        break;
                    } else {
                        if (!array_key_exists($seriesKey, $series)) {
                            $series[$seriesKey] = array();
                        }

                        $series[$seriesKey][] = floatval(0);
                        $l++;
                    }
                }
            }//end foreach

            if ($l < count($categories)) {
                for ($i = $l; $i < count($categories); $i++) {
                    if (!array_key_exists($seriesKey, $series)) {
                        $series[$seriesKey] = array();
                    }

                    $series[$seriesKey][] = floatval(0);
                }
            }
        } else {
            $tempType = '';
            switch ($type) {
            case 0:
                $tempType = 'Indeterminate(事件)';
                break;
            case 1:
                $tempType = 'Critical';
                break;
            case 2:
                $tempType = 'Major';
                break;
            case 3:
                $tempType = 'Minor';
                break;
            case 4:
                $tempType = 'Warning';
                break;
            case 5:
                $tempType = 'Cleared';
                break;
            }

            for ($i = 0; $i < count($categories); $i++) {
                if (!array_key_exists($tempType, $series)) {
                    $series[$tempType] = array();
                }

                $series[$tempType][] = floatval(0);
            }
        }//end if
        return $series;

    }//end getHighChartSeries()


    /**
     * 获得最近7天的历史告警信息
     *
     * @return string 历史告警
     */
    public function getHistoryAlarm()
    {
        $startDate = new DateTime();
        $startDate->sub(new DateInterval('P2M'));
        $endDate = new DateTime();
        $endDate->sub(new DateInterval('P1D'));
        $startDateId = $startDate->format('Y-m-d');
        $endDateId   = $endDate->format('Y-m-d');
        $result      = FMA_alarm_log_group_by_city_date::selectRaw('date as time,city,alarm_num as num')
                                                        ->where('city', '!=', '')
                                                        ->whereBetween('date', [$startDateId,$endDateId])
                                                        ->get();
        return $this->getHighChartData($result);

    }//end getHistoryAlarm()


    /**
     * 获得HighChart图表数据
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
            $city = $item->city;
            $num  = $item->num;
            $time = strtotime($item->time);
            $arr  = array();
            if (array_search($city, $category) === false) {
                $category[] = $city;
            }

            if (!array_key_exists($city, $series)) {
                $series[$city] = array();
            }

            array_push($arr, (floatval($time) * 1000));
            array_push($arr, floatval($num));
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


    /**
     * 获取一天各地市的详细信息
     *
     * @return string
     */
    public function getHistoryAlarmDateData()
    {
        $time = Input::get("time");
        $time = (floatval($time) / 1000);
        $date = date("Y-m-d", $time);
        $rs   = FMA_alarm_log::selectRaw(
                                        "select city ,
                                        case Perceived_severity
                                        when 0 then 'Indeterminate(事件)'
                                        when 1 then 'Critical'
                                        when 2 then 'Major'
                                        when 3 then 'Minor'
                                        when 4 then 'Warning'
                                        when 5 then 'Cleared'
                                        end as type,
                                        count(*) as num")
                                ->where('city', '!=', '')
                                ->where('Event_time', 'like', $date.'%')
                                ->groupBy(['Perceived_severity','city'])
                                ->get();
        return $this->getHistoryHighChartData($rs);

    }//end getHistoryAlarmDateData()


    /**
     * 获取一天各地市的详细信息
     *
     * @param array $result 查询结果
     *
     * @return string
     */
    public function getHistoryHighChartData($result)
    {
        $series   = array();
        $category = array();
        foreach ($result as $item) {
            $city = $item->city;
            $num  = $item->num;
            $type = $item->type;
            if (array_search($city, $category) === false) {
                $category[] = $city;
            }

            if (!array_key_exists($type, $series)) {
                $series[$type] = array();
            }

            $series[$type][] = floatval($num);
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

    }//end getHistoryHighChartData()


    /**
     * 当前告警柱状图 drilldown数据
     *
     * @return string
     */
    function getDrillDownDonutPie()
    {
        $city = Input::get('city');
        $rs   = FMA_alarm_list::selectRaw(
                                        "Perceived_severity as tempType,
                                        case Perceived_severity
                                        when 0 then 'Indeterminate(事件)'
                                        when 1 then 'Critical'
                                        when 2 then 'Major'
                                        when 3 then 'Minor'
                                        when 4 then 'Warning'
                                        when 5 then 'Cleared'
                                        end as type,
                                        count(*) as num")
                                ->where('city', $city)
                                ->groupBy('Perceived_severity')
                                ->orderBy('tempType', 'asc')
                                ->get();
        $data = array();
        $perceived_severity_Data = array();
        $sp_text_data            = array();
        foreach ($rs as $item) {
            $arr        = array();
            $tempType   = $item->tempType;
            $type       = $item->type;
            $num        = $item->num;
            $arr[$type] = floatval($num);
            foreach ($arr as $key => $value) {
                $perceived_severity_Data[] = ['name' => $key,
                                              'y'    => $value,];
            }
            $rs_sp_text  = FMA_alarm_list::selectRaw(
                                                    "Perceived_severity as tempType,
                                                    case Perceived_severity
                                                    when 0 then 'Indeterminate(事件)'
                                                    when 1 then 'Critical'
                                                    when 2 then 'Major'
                                                    when 3 then 'Minor'
                                                    when 4 then 'Warning'
                                                    when 5 then 'Cleared'
                                                    end as type,SP_text,
                                                    count(*) as num")
                                            ->where('city', $city)
                                            ->where('Perceived_severity', $tempType)
                                            ->groupBy('SP_text')
                                            ->get();
            foreach ($rs_sp_text as $item_sp) {
                $arr_sp  = array();
                $type    = $item_sp->type;
                $SP_text = $item_sp->SP_text;
                $num_sp  = $item_sp->num;
                $arr_sp[$SP_text] = floatval($num_sp);
                foreach ($arr_sp as $key => $value) {
                    $sp_text_data[$type][] = ['name' => $key,
                                              'y'    => $value,];
                }
            }
        }//end foreach

        $data['perceived_severity'] = $perceived_severity_Data;
        $data['sp_text']            = $sp_text_data;
        return json_encode($data);

    }//end getDrillDownDonutPie()


    /**
     * 历史告警柱状图 drilldown数据
     *
     * @return string
     */
    function getHistoryDrillDownDonutPie()
    {
        $time = Input::get("time");
        $time = (floatval($time) / 1000);
        $date = date("Y-m-d", $time);
        $city = Input::get('city');
        $rs   = FMA_alarm_log::selectRaw(
                                        "Perceived_severity as tempType,
                                        case Perceived_severity
                                        when 0 then 'Indeterminate(事件)'
                                        when 1 then 'Critical'
                                        when 2 then 'Major'
                                        when 3 then 'Minor'
                                        when 4 then 'Warning'
                                        when 5 then 'Cleared'
                                        end as type,
                                        count(*) as num")
                                ->where('city', $city)
                                ->where('Event_time', 'like', $date.'%')
                                ->groupBy('Perceived_severity')
                                ->get();
        $data = array();
        $perceived_severity_Data = array();
        $sp_text_data            = array();
        foreach ($rs as $item) {
            $arr        = array();
            $type       = $item->type;
            $num        = $item->num;
            $arr[$type] = floatval($num);
            foreach ($arr as $key => $value) {
                $perceived_severity_Data[] = [
                                              'name' => $key,
                                              'y'    => $value,
                                              ];
            }
        }

        $rs_sp_text  = FMA_alarm_log::selectRaw(
                                                "Perceived_severity as tempType,
                                                case Perceived_severity
                                                when 0 then 'Indeterminate(事件)'
                                                when 1 then 'Critical'
                                                when 2 then 'Major'
                                                when 3 then 'Minor'
                                                when 4 then 'Warning'
                                                when 5 then 'Cleared'
                                                end as type,SP_text,
                                                count(*) as num")
                                        ->where('city', $city)
                                        ->where('Event_time', 'like', $date.'%')
                                        ->groupBy(['Perceived_severity','SP_text'])
                                        ->get();

        foreach ($rs_sp_text as $item_sp) {
            $arr_sp  = array();
            $type    = $item_sp->type;
            $SP_text = $item_sp->SP_text;
            $num_sp  = $item_sp->num;
            $arr_sp[$SP_text] = floatval($num_sp);
            foreach ($arr_sp as $key => $value) {
                $sp_text_data[$type][] = ['name' => $key,
                                          'y'    => $value,];
            }
        }

        $data['perceived_severity'] = $perceived_severity_Data;
        $data['sp_text']            = $sp_text_data;
        return json_encode($data);

    }//end getHistoryDrillDownDonutPie()


}//end class
