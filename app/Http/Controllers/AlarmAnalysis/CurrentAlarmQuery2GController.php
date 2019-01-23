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
use Illuminate\Support\Facades\Input;
use App\Models\Mongs\Databaseconn2G;
use App\Models\Alarm\FMA_alarm_list_2G;

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
class CurrentAlarmQuery2GController extends Controller
{

    /**
     * 获得城市列表
     *
     * @return string 城市列表
     */
    public function getCitys()
    {
        $row = Databaseconn2G::orderBy('cityChinese', 'asc')->get()->toArray();
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
     * 获得当前告警
     *
     * @return string 当前告警
     */
    public function getTableData()
    {
        $placeDim = Input::get("placeDim");
        // what's placeDimName?
        $placeDimNameStr = Input::get("placeDimName");
        $phpCity = Input::get("city");
        $cities = explode(",", $phpCity);

        $limit = Input::get('limit', 50);

        $conn = FMA_alarm_list_2G::whereIn('city', $cities);
        if ($placeDimNameStr) {
            $placeDimNameArr = explode(",", $placeDimNameStr);
            $conn = $conn->whereIn($placeDim, $placeDimNameArr);
        }
        $rows = $conn->orderBy('Event_time', 'desc')
            ->paginate($limit)
            ->toArray();

        $result = array();
        $result["total"] = $rows['total'];
        $result['records'] = $rows['data'];

        return json_encode($result);

    }//end getTableData()


    /**
     * 导出全量当前告警
     *
     * @return string 全量当前告警
     */
    public function getAllTableData()
    {
        $placeDim = Input::get("placeDim");
        $placeDimNameStr = Input::get("placeDimName");
        $phpCity = Input::get("city");
        $cities = explode(",", $phpCity);

        $text = "Event_time,city,subNetwork,ManagedElement,BtsSiteMgr,SP_text,Problem_text,Alarm_id";
        $conn = FMA_alarm_list_2G::selectRaw($text)
            ->whereIn('city', $cities);
        if ($placeDimNameStr) {
            $placeDimNameArr = explode(",", $placeDimNameStr);
            $conn = $conn->whereIn($placeDim, $placeDimNameArr);
        }
        $items = $conn->orderBy('Event_time', 'desc')
            ->get()
            ->toArray();

        $result = array();
        $result["text"] = $text;
        $result['total'] = count($items);
        $result['result'] = 'true';
        $filename = "common/files/FMA_alarm_list_2G_" . date('YmdHis') . ".csv";
        $fileUtil = new FileUtil();
        $fileUtil->resultToCSV2($result["text"], $items, $filename);
        $result['filename'] = $filename;

        return json_encode($result);

    }//end getAllTableData()

}//end class
