<?php

/**
 * GSMAlarmQueryController.php
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
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Common\MyRedis;
use App\Models\Mongs\Databaseconn2G;
use App\Models\Alarm\FMA_alarm_log_2G;
use App\Models\Alarm\FMA_alarm_list_2G;

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
class GSMAlarmQueryController extends MyRedis
{


    /**
     * 获取城市列表
     *
     * @return array 城市列表
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
     * 获取GSM查询信息
     *
     * @return mixed 当前页GSM告警数据
     */

    public function getTableData()
    {
        $type = Input::get('type');

        if ($type == "GSMCurrent") {
            $this->getGSMCurrentData();
        } else if ($type == "GSMHistory") {
            $this->getGSMHistoryData();
        }

    }

    /**
     * 获得当前告警
     *
     * @return string
     */
    public function getGSMCurrentData()
    {
        $placeDim = Input::get("placeDim");
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
    }

    /**
     * 获取分页历史告警数据
     *
     * @return mixed 当前页历史告警数据
     */
    public function getGSMHistoryData()
    {
        $placeDim = Input::get("placeDim");
        $placeDimNameStr = Input::get("placeDimName");
        $dateFrom = Input::get("dateFrom");
        $dateTo = Input::get("dateTo");
        $phpCity = Input::get("city");
        $cities = explode(",", $phpCity);

        $limit = Input::get('limit', 50);

        $conn = FMA_alarm_log_2G::whereBetween('Event_time', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->whereIn('city', $cities);
        if ($placeDimNameStr) {
            $placeDimNameArr = explode(",", $placeDimNameStr);
            $conn = $conn->whereIn($placeDim, $placeDimNameArr);
        }
        $rows = $conn->where('Perceived_severity', '!=', 5)
            ->where('SP_text', '!=', '')
            ->orderBy('Event_time', 'desc')
            ->paginate($limit)
            ->toArray();

        $result = array();
        $result["total"] = $rows['total'];
        $result['records'] = $rows['data'];
        return json_encode($result);

    }//end getGSMHistoryData()

    /**
     * 获得所有GSM告警数据
     *
     * @return mixed 所有GSM告警数据
     */

    public function getAllTableData()
    {
        $type = Input::get('type');

        if ($type == "GSMCurrent") {
            $this->getAllGSMCurrentData();
        } else if ($type == "GSMHistory") {
            $this->getAllGSMHistoryData();
        }
    }//end getAllTableData()

    /**
     * 导出全量当前告警
     *
     * @return mixed
     */
    public function getAllGSMCurrentData()
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

    }//end getAllGSMCurrentData()

    /**
     * 获得所有历史告警数据
     *
     * @return mixed 所有历史告警数据
     */
    public function getAllGSMHistoryData()
    {
        $placeDim = Input::get("placeDim");
        $placeDimNameStr = Input::get("placeDimName");
        $dateFrom = Input::get("dateFrom");
        $dateTo = Input::get("dateTo");
        $phpCity = Input::get("city");
        $cities = explode(",", $phpCity);

        $text = "Event_time,city,subNetwork,ManagedElement,BtsSiteMgr,Cease_time,SP_text,Problem_text,Alarm_id";
        $conn = FMA_alarm_log_2G::selectRaw($text)
            ->whereBetween('Event_time', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->whereIn('city', $cities);
        if ($placeDimNameStr) {
            $placeDimNameArr = explode(",", $placeDimNameStr);
            $conn = $conn->whereIn($placeDim, $placeDimNameArr);
        }
        $items = $conn->where('Perceived_severity', '!=', 5)
            ->where('SP_text', '!=', '')
            ->orderBy('Event_time', 'desc')
            ->get()
            ->toArray();

        $result = array();
        $result["text"] = $text;
        $result['total'] = count($items);
        $result['result'] = 'true';
        $filename = "common/files/FMA_alarm_log_2G_" . date('YmdHis') . ".csv";
        $fileUtil = new FileUtil();
        $fileUtil->resultToCSV2($result["text"], $items, $filename);
        $result['filename'] = $filename;

        return json_encode($result);

    }//end getAllGSMHistoryData()

    /**
     * 获得历史告警数据的时间(天)列表
     *
     * @return array 天列表
     */
    public function getGSMAlarmTime()
    {
        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('alarm_2g');
        $table = 'FMA_alarm_log';
        $sql = "select DISTINCT SUBSTRING_INDEX(insert_time,' ',1) time from $table";
        $this->type = 'Alarm:historyAlarmQuery2G';
        return $this->getValue($db, $sql);

    }//end getGSMAlarmTime()

}//end class
