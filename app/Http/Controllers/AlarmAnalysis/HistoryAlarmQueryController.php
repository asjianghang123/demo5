<?php

/**
 * HistoryAlarmQueryController.php
 *
 * @category AlarmAnalysis
 * @package  App\Http\Controllers\AlarmAnalysis
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
namespace App\Http\Controllers\AlarmAnalysis;

use App\Http\Controllers\Common\DataBaseConnection;
use App\Http\Controllers\Utils\FileUtil;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Common\MyRedis;
use App\Models\Mongs\Databaseconns;
use App\Models\Alarm\FMA_alarm_log;

/**
 * 历史告警
 * Class HistoryAlarmQueryController
 *
 * @category AlarmAnalysis
 * @package  App\Http\Controllers\AlarmAnalysis
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class HistoryAlarmQueryController extends MyRedis
{


    /**
     * 获取城市列表
     *
     * @return array 城市列表
     */
    public function getCitys()
    {
        $row = Databaseconns::orderBy('cityChinese', 'asc')->get()->toArray();
        $items = array();
        foreach ($row as $qr) {
            if (is_numeric(substr($qr['connName'], -1))) {
                continue;
            }
            array_push($items, ["value" => $qr['connName'], "label" => $qr['cityChinese']]);
        }
        return json_encode($items);

    }//end getCitys()


    /**
     * 获取分页历史告警数据
     *
     * @return mixed 当前页历史告警数据
     */
    public function getTableData()
    {
        $placeDim = Input::get("placeDim");
        $placeDimNameStr = Input::get("placeDimName");
        $dateFrom = Input::get("dateFrom");
        $dateTo = Input::get("dateTo");
        $phpCity = Input::get("city");
        $cities = explode(",", $phpCity);
        $limit = Input::get('limit', 50);

        $conn = FMA_alarm_log::whereBetween('Event_time', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->whereIn('FMA_alarm_log.city', $cities);
        if ($placeDimNameStr) {
            $placeDimNameArr = explode(",", $placeDimNameStr);
            $conn = $conn->whereIn($placeDim, $placeDimNameArr);
        }
        $rows = $conn->where('Perceived_severity', '!=', 5)
            ->where('SP_text', '!=', '')
            ->leftJoin(DB::raw('(select * from mongs.siteLte group by siteName) as s'), 'siteName', '=', 'meContext')
            ->leftJoin('Alarm.AlarmInfo', 'alarmNameE', '=', 'SP_text')
            ->orderBy('Event_time', 'desc')
            ->paginate($limit)
            ->toArray();
        $result = array();
        $result["total"] = $rows['total'];
        $result['records'] = $rows['data'];
        return json_encode($result);

    }//end getTableData()


    /**
     * 获得所有历史告警数据
     *
     * @return mixed 所有历史告警数据
     */
    public function getAllTableData()
    {
        $placeDim = Input::get("placeDim");
        $placeDimNameStr = Input::get("placeDimName");
        $dateFrom = Input::get("dateFrom");
        $dateTo = Input::get("dateTo");
        $phpCity = Input::get("city");
        $cities = explode(",", $phpCity);

        $conn = FMA_alarm_log::selectRaw('Event_time,FMA_alarm_log.city,FMA_alarm_log.subNetwork,meContext,eutranCell,Cease_time,SP_text,Problem_text,Alarm_id,cluster,siteType,siteNameChinese,alarmNameC,levelC')
            ->whereBetween('Event_time', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->whereIn('FMA_alarm_log.city', $cities);
        if ($placeDimNameStr) {
            $placeDimNameArr = explode(",", $placeDimNameStr);
            $conn = $conn->whereIn($placeDim, $placeDimNameArr);
        }
        $items = $conn->where('Perceived_severity', '!=', 5)
            ->where('SP_text', '!=', '')
            ->leftJoin(DB::raw('(select * from mongs.siteLte group by siteName) as s'), 'siteName', '=', 'meContext')
            ->leftJoin('Alarm.AlarmInfo', 'alarmNameE', '=', 'SP_text')
            ->orderBy('Event_time', 'desc')
            ->get()
            ->toArray();

        $result = array();
        $result["text"] = "Event_time,city,subNetwork,meContext,eutranCell,Cease_time,SP_text,Problem_text,Alarm_id,cluster,siteType,siteNameChinese,alarmNameC,levelC";
        $result['total'] = count($items);
        $result['result'] = 'true';

        $filename = "common/files/FMA_alarm_log_" . date('YmdHis') . ".csv";
        $fileUtil = new FileUtil();
        $fileUtil->resultToCSV2($result["text"], $items, $filename);
        $result['filename'] = $filename;

        return json_encode($result);

    }//end getAllTableData()

    /**
     * 获得历史告警数据的时间(天)列表
     *
     * @return array 天列表
     */
    public function getHistoryAlarmTime()
    {
        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('mongs', 'Alarm');
        $table = 'FMA_alarm_log';
        $sql = "select DISTINCT SUBSTRING_INDEX(insert_time,' ',1) time from $table";
        $this->type = 'Alarm:historyAlarmQuery';
        return $this->getValue($db, $sql);

    }//end getHistoryAlarmTime()
}//end class
