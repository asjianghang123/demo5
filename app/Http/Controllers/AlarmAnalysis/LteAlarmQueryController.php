<?php

/**
 * LteAlarmQueryController.php
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
use App\Models\Alarm\FMA_alarm_list;
use App\Models\Alarm\FMA_alarm_log;

/**
 * Lte告警查询
 * Class LteAlarmQueryController
 *
 * @category AlarmAnalysis
 * @package  App\Http\Controllers\AlarmAnalysis
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class LteAlarmQueryController extends MyRedis
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
     * 获取LTE查询信息
     *
     *
     * @return mixed 当前页LTE告警数据
     */

    public function getTableData()
    {
        // exit;
        $type = input::get('type');

        if ($type == "LteCurrent") {
            $this->getLteCurrentData();
        } else if ($type == "LteHistory") {
            $this->getLteHistoryData();
        }

    }//end getTableData()

    /**
     * 获得当前告警
     *
     * @return mixed
     */
    protected function getLteCurrentData()
    {
        $placeDim = input::get("placeDim");
        $placeDimNameStr = input::get("placeDimName");
        $format = input::get('subnet');
        $subNetwork = $this->getAllSubNetwork($format);
        $phpCity = input::get("city");
        $citys = explode(",", $phpCity);
        $page = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $limit = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 50;

        $conn = FMA_alarm_list::whereIn('FMA_alarm_list.city', $citys)
            ->whereIn('FMA_alarm_list.subNetwork', $subNetwork);
        if ($placeDimNameStr) {
            $placeDimNameArr = explode(",", $placeDimNameStr);
            $conn = $conn->whereIn($placeDim, $placeDimNameArr);
        }
        $rows = $conn->where('Perceived_severity', '!=', 5)
            ->where('SP_text', '!=', '')
            ->leftJoin(DB::raw('(select siteNameChinese,siteName,siteType,cluster from mongs.siteLte group by siteName) as s'), 'siteName', '=', 'meContext')
            ->leftJoin('Alarm.AlarmInfo', 'alarmNameE', '=', 'SP_text')
            ->orderBy('Event_time', 'desc')
            ->paginate($limit)
            ->toArray();
        $result["total"] = $rows['total'];
        $result['records'] = $rows['data'];
        echo json_encode($result);
    }

    protected function getAllSubNetwork($format)
    {
        $city = input::get('city');
        $cityArr = explode(',', $city);
        // $format     = input::get('subnet');

        $result = array();

        foreach ($cityArr as $key => $value) {
            $cityChinese = Databaseconns::select('cityChinese')->where('connName', '=', $value)->groupBy('cityChinese')->get()->toArray();
            // print_r($cityChinese);
            $databaseConns = Databaseconns::where('cityChinese', '=', $cityChinese[0]['cityChinese'])->get();
            foreach ($databaseConns as $key => $databaseConn) {
                if ($format == 'TDD') {
                    $subStr = $databaseConn->subNetwork;
                } else if ($format == 'FDD') {
                    $subStr = $databaseConn->subNetworkFdd;
                } else if ($format == 'NBIOT') {
                    $subStr = $databaseConn->subNetworkNbiot;
                }
                if ($subStr) {
                    $subArr = explode(',', $subStr);
                    // print_r($subArr);
                    $result = array_merge($result, $subArr);
                }
            }
        }
        return $result;
    }//end getAllSubNetwork()

    /**
     * 获取分页历史告警数据
     *
     * @return mixed 当前页历史告警数据
     */
    protected function getLteHistoryData()
    {
        $placeDim = Input::get("placeDim");
        $placeDimNameStr = Input::get("placeDimName");

        $format = Input::get('subnet');
        $subNetwork = $this->getAllSubNetwork($format);

        $dateFrom = Input::get("dateFrom");
        $dateTo = Input::get("dateTo");
        $phpCity = Input::get("city");
        $cities = explode(",", $phpCity);
        $limit = Input::get('limit', 50);

        $conn = FMA_alarm_log::whereBetween('Event_time', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->whereIn('FMA_alarm_log.city', $cities)->whereIn('FMA_alarm_log.subNetwork', $subNetwork);
        if ($placeDimNameStr) {
            $placeDimNameArr = explode(",", $placeDimNameStr);
            $conn = $conn->whereIn($placeDim, $placeDimNameArr);
        }
        $rows = $conn->where('Perceived_severity', '!=', 5)
            ->where('SP_text', '!=', '')
            ->leftJoin(DB::raw('(select siteNameChinese,siteName,siteType,cluster from mongs.siteLte group by siteName) as s'), 'siteName', '=', 'meContext')
            ->leftJoin('Alarm.AlarmInfo', 'alarmNameE', '=', 'SP_text')
            ->orderBy('Event_time', 'desc')
            ->paginate($limit)
            ->toArray();
        $result = array();
        $result["total"] = $rows['total'];
        $result['records'] = $rows['data'];
        echo json_encode($result);
    }//end getLteHistoryData()

    /**
     * 获得所有LTE告警数据
     *
     * @return mixed 所有LTE告警数据
     */

    public function getAllTableData()
    {
        $type = Input::get('type');
        if ($type == "LteCurrent") {
            $this->getAllLteCurrentData();
        } else if ($type == "LteHistory") {
            $this->getAllLteHistoryData();
        }
    }//end getAllTableData()

    /**
     * 导出全量当前告警
     *
     * @return mixed
     */
    protected function getAllLteCurrentData()
    {
        $placeDim = Input::get("placeDim");
        $placeDimNameStr = Input::get("placeDimName");
        $phpCity = Input::get("city");
        $cities = explode(",", $phpCity);

        $format = Input::get('subnet');
        $subNetwork = $this->getAllSubNetwork($format);

        $conn = FMA_alarm_list::selectRaw('Event_time,FMA_alarm_list.city,FMA_alarm_list.subNetwork,meContext,eutranCell,SP_text,Problem_text,Alarm_id,cluster,siteType,siteNameChinese,alarmNameC,levelC')
            ->whereIn('FMA_alarm_list.city', $cities)
            ->whereIn("FMA_alarm_list.subNetwork", $subNetwork);
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

    }//end getAllLteCurrentData()

    /**
     * 获得所有历史告警数据
     *
     * @return mixed 所有历史告警数据
     */
    protected function getAllLteHistoryData()
    {
        $placeDim = Input::get("placeDim");
        $placeDimNameStr = Input::get("placeDimName");
        $dateFrom = Input::get("dateFrom");
        $dateTo = Input::get("dateTo");
        $phpCity = Input::get("city");
        $cities = explode(",", $phpCity);
        $format = Input::get('subnet');
        $subNetwork = $this->getAllSubNetwork($format);

        $conn = FMA_alarm_log::selectRaw('Event_time,FMA_alarm_log.city,FMA_alarm_log.subNetwork,meContext,eutranCell,Cease_time,SP_text,Problem_text,Alarm_id,cluster,siteType,siteNameChinese,alarmNameC,levelC')
            ->whereBetween('Event_time', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->whereIn('FMA_alarm_log.city', $cities)->whereIn('FMA_alarm_log.subNetwork', $subNetwork);
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
    }//end getAllLteHistoryData()

    /**
     * 获得历史告警数据的时间(天)列表
     *
     * @return array 天列表
     */
    public function getLteAlarmTime()
    {
        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('mongs', 'Alarm');
        $table = 'FMA_alarm_log';
        $sql = "select DISTINCT SUBSTRING_INDEX(insert_time,' ',1) time from $table";
        $this->type = 'Alarm:historyAlarmQuery';
        return $this->getValue($db, $sql);
    }
}//end class
