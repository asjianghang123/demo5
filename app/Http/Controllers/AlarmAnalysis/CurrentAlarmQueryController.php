<?php

/**
 * CurrentAlarmQueryController.php
 *
 * @category AlarmAnalysis
 * @package  App\Http\Controllers\AlarmAnalysis
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
namespace App\Http\Controllers\AlarmAnalysis;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Utils\FileUtil;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\Models\Mongs\Databaseconns;
use App\Models\Alarm\FMA_alarm_list;

/**
 * 当前告警
 * Class CurrentAlarmQueryController
 *
 * @category AlarmAnalysis
 * @package  App\Http\Controllers\AlarmAnalysis
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class CurrentAlarmQueryController extends Controller
{

    /**
     * 获得城市列表
     *
     * @return void
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
        echo json_encode($items);

    }//end getCitys()


    /**
     * 获得当前告警
     *
     * @return mixed
     */
    public function getTableData()
    {
        $placeDim = Input::get("placeDim");
        $placeDimNameStr = Input::get("placeDimName");

        $phpCity = Input::get("city");
        $cities = explode(",", $phpCity);
        $limit = Input::get('limit', 50);

        $conn = FMA_alarm_list::whereIn('FMA_alarm_list.city', $cities);
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
        $result["total"] = $rows['total'];
        $result['records'] = $rows['data'];
        return json_encode($result);
    }//end getTableData()


    /**
     * 导出全量当前告警
     *
     * @return mixed
     */
    public function getAllTableData()
    {
        $placeDim = Input::get("placeDim");
        $placeDimNameStr = Input::get("placeDimName");
        $phpCity = Input::get("city");
        $cities = explode(",", $phpCity);
        $conn = FMA_alarm_list::selectRaw('Event_time,FMA_alarm_list.city,FMA_alarm_list.subNetwork,meContext,eutranCell,SP_text,Problem_text,Alarm_id,cluster,siteType,siteNameChinese,alarmNameC,levelC')
            ->whereIn('FMA_alarm_list.city', $cities);
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

        $result["text"] = "Event_time,city,subNetwork,meContext,eutranCell,SP_text,Problem_text,Alarm_id,cluster,siteType,siteNameChinese,alarmNameC,levelC";
        $result['total'] = count($items);
        $result['result'] = 'true';

        $filename = "common/files/FMA_alarm_list_" . date('YmdHis') . ".csv";
        $fileUtil = new FileUtil();
        $fileUtil->resultToCSV2($result["text"], $items, $filename);
        $result['filename'] = $filename;

        return json_encode($result);

    }//end getAllTableData()
    
}//end class
