<?php

/**
 * SignalingDiagnoseController.php
 *
 * @category ComplaintHandling
 * @package  App\Http\Controllers\ComplaintHandling
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
namespace App\Http\Controllers\ComplaintHandling;

use App\Http\Controllers\Common\DataBaseConnection;
use App\Http\Requests;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Common\MyRedis;
use PDO;
use App\Models\CTR\InternalProcHoExecS1In;
use App\Models\CTR\InternalProcHoExecS1Out;
use App\Models\CTR\InternalProcHoExecX2In;
use App\Models\CTR\InternalProcHoExecX2Out;
use App\Models\CTR\InternalProcHoPrepS1In;
use App\Models\CTR\InternalProcHoPrepS1Out;
use App\Models\CTR\InternalProcHoPrepX2In;
use App\Models\CTR\InternalProcHoPrepX2Out;
use App\Models\CTR\InternalProcInitialCtxtSetup;
use App\Models\CTR\InternalProcRrcConnSetup;
use App\Models\CTR\InternalProcS1SigConnSetup;

use App\Models\CDR\UserInfo;
use App\Models\CDR\L_ATTACH;
use App\Models\CDR\L_DETACH;
use App\Models\CDR\L_BEARER_MODIFY;
use App\Models\CDR\L_DEDICATED_BEARER_ACTIVATE;
use App\Models\CDR\L_DEDICATED_BEARER_DEACTIVATE;
use App\Models\CDR\L_HANDOVER;
use App\Models\CDR\L_PDN_CONNECT;
use App\Models\CDR\L_PDN_DISCONNECT;
use App\Models\CDR\L_SERVICE_REQUEST;
use App\Models\CDR\L_TAU;

/**
 * 信令诊断
 * Class SignalingDiagnoseController
 *
 * @category ComplaintHandling
 * @package  App\Http\Controllers\ComplaintHandling
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class SignalingDiagnoseController extends MyRedis
{


    /**
     * 核心网测一级展示数据查询
     *
     * @return array 核心网诊断结果概览
     */
    function getCoreNetworkDiagnoseData()
    {
        $city = Input::get("city");
        $dateId = Input::get("dateTime");
        $hourId = Input::get("hourId");
        $userInfo = Input::get("userInfo");
        $data = array();
        if (!(strpos($userInfo, '460') === 0)) {
            $userInfo = $this->getImsiByMsisdn($userInfo);
            if ($userInfo == '') {
                return $this->getDefaultData();
            }
        }
        $L_ATTACH = L_ATTACH::on($city)->where('date_id', $dateId)->whereIn('hour_id', $hourId)->where('imsi', $userInfo)->where('result', '!=', 'SUCCESS')->count();
        $L_DETACH = L_DETACH::on($city)->where('date_id', $dateId)->whereIn('hour_id', $hourId)->where('imsi', $userInfo)->where('result', '!=', 'SUCCESS')->count();
        $L_BEARER_MODIFY = L_BEARER_MODIFY::on($city)->where('date_id', $dateId)->whereIn('hour_id', $hourId)->where('imsi', $userInfo)->where('result', '!=', 'SUCCESS')->count();
        $L_DEDICATED_BEARER_ACTIVATE = L_DEDICATED_BEARER_ACTIVATE::on($city)->where('date_id', $dateId)->whereIn('hour_id', $hourId)->where('imsi', $userInfo)->where('result', '!=', 'SUCCESS')->count();
        $L_DEDICATED_BEARER_DEACTIVATE = L_DEDICATED_BEARER_DEACTIVATE::on($city)->where('date_id', $dateId)->whereIn('hour_id', $hourId)->where('imsi', $userInfo)->where('result', '!=', 'SUCCESS')->count();
        $L_HANDOVER = L_HANDOVER::on($city)->where('date_id', $dateId)->whereIn('hour_id', $hourId)->where('imsi', $userInfo)->where('result', '!=', 'SUCCESS')->count();
        $L_PDN_CONNECT = L_PDN_CONNECT::on($city)->where('date_id', $dateId)->whereIn('hour_id', $hourId)->where('imsi', $userInfo)->where('result', '!=', 'SUCCESS')->count();
        $L_PDN_DISCONNECT = L_PDN_DISCONNECT::on($city)->where('date_id', $dateId)->whereIn('hour_id', $hourId)->where('imsi', $userInfo)->where('result', '!=', 'SUCCESS')->count();
        $L_SERVICE_REQUEST = L_SERVICE_REQUEST::on($city)->where('date_id', $dateId)->whereIn('hour_id', $hourId)->where('imsi', $userInfo)->where('result', '!=', 'SUCCESS')->count();
        $L_TAU = L_TAU::on($city)->where('date_id', $dateId)->whereIn('hour_id', $hourId)->where('imsi', $userInfo)->where('result', '!=', 'SUCCESS')->count();


        $data['series'] = [$L_ATTACH, $L_DETACH, $L_BEARER_MODIFY, $L_DEDICATED_BEARER_ACTIVATE, $L_DEDICATED_BEARER_DEACTIVATE, $L_HANDOVER, $L_PDN_CONNECT, $L_PDN_DISCONNECT, $L_SERVICE_REQUEST, $L_TAU];
        $data['categories'] = [
            "L_ATTACH",
            "L_DETACH",
            "L_BEARER_MODIFY",
            "L_DEDICATED_BEARER_ACTIVATE",
            "L_DEDICATED_BEARER_DEACTIVATE",
            "L_HANDOVER",
            "L_PDN_CONNECT",
            "L_PDN_DISCONNECT",
            "L_SERVICE_REQUEST",
            "L_TAU",
        ];
        $data['imsi'] = $userInfo;
        return $data;


    }//end getCoreNetworkDiagnoseData()


    /**
     * 通过msisdn找到对应imsi
     *
     * @param string $msisdn 手机号码
     *
     * @return string 用户imsi
     */
    function getImsiByMsisdn($msisdn)
    {
        $city = Input::get("city");
        $imsi = UserInfo::on($city)->where('msisdn', 'like', "%" . $msisdn)->first();
        if ($imsi) {
            return $imsi->imsi;
        } else {
            return '';
        }

    }//end getImsiByMsisdn()


    /**
     * 没有记录情况下的默认值
     *
     * @return array 缺省返回结果
     */
    public function getDefaultData()
    {
        $categories = [
            "L_ATTACH",
            "L_DETACH",
            "L_BEARER_MODIFY",
            "L_DEDICATED_BEARER_ACTIVATE",
            "L_DEDICATED_BEARER_DEACTIVATE",
            "L_HANDOVER",
            "L_PDN_CONNECT",
            "L_PDN_DISCONNECT",
            "L_SERVICE_REQUEST",
            "L_TAU",
        ];
        $series = [
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
        ];
        $data['series'] = $series;
        $data['categories'] = $categories;
        $data['imsi'] = '';
        return $data;

    }//end getDefaultData()


    /**
     * 拼接极地图所需数据形式
     *
     * @param array $result 结果分布数据
     * @param string $imsi 用户IMSI
     *
     * @return array 极地图展示数据
     */
    public function getHighChartPolarData($result, $imsi)
    {
        $series = array();
        $categories = array();
        foreach ($result as $item) {
            $num = $item->num;
            $type = $item->type;
            array_push($series, floatval($num));
            array_push($categories, $type);
        }

        $data['series'] = $series;
        $data['categories'] = $categories;
        $data['imsi'] = $imsi;
        return $data;

    }//end getHighChartPolarData()


    /**
     * 核心网侧获得表头数据
     *
     * @return array 核心网侧表头
     */
    function getCoreNetworkDiagnoseDetailHeader()
    {
        $city = Input::get("city");
        $tableName = Input::get("tableName");
        return $this->switchTable($tableName, $city)->first()->toArray();

    }//end getCoreNetworkDiagnoseDetailHeader()

    /**
     * 获得对应表的链接
     *
     * @return Model
     */
    public function switchTable($table, $city)
    {
        switch ($table) {
            case 'L_ATTACH':
                $conn = new L_ATTACH;
                break;
            case 'L_DETACH':
                $conn = new L_DETACH;
                break;
            case 'L_BEARER_MODIFY':
                $conn = new L_BEARER_MODIFY;
                break;
            case 'L_DEDICATED_BEARER_ACTIVATE':
                $conn = new L_DEDICATED_BEARER_ACTIVATE;
                break;
            case 'L_DEDICATED_BEARER_DEACTIVATE':
                $conn = new L_DEDICATED_BEARER_DEACTIVATE;
                break;
            case 'L_HANDOVER':
                $conn = new L_HANDOVER;
                break;
            case 'L_PDN_CONNECT':
                $conn = new L_PDN_CONNECT;
                break;
            case 'L_PDN_DISCONNECT':
                $conn = new L_PDN_DISCONNECT;
                break;
            case 'L_SERVICE_REQUEST':
                $conn = new L_SERVICE_REQUEST;
                break;
            case 'L_TAU':
                $conn = new L_TAU;
                break;
        }
        $conn->setConnection($city);
        return $conn;
    }//end getCoreNetworkDiagnoseDetail()

    /**
     * 核心网侧获取详细数据
     *
     * @return string 核心网侧诊断数据详细
     */
    function getCoreNetworkDiagnoseDetail()
    {
        $city = Input::get("city");
        $tableName = Input::get("tableName");
        $dateId = Input::get("dateTime");
        $hourId = Input::get("hourId");
        $imsi = Input::get("imsi");
        $rows = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 10;

        $row = $this->switchTable($tableName, $city)->where('date_id', $dateId)->whereIn('hour_id', $hourId)->where('imsi', $imsi)->where('result', '!=', 'SUCCESS')->paginate($rows)->toArray();
        $result = array();
        $result["total"] = $row['total'];
        $result['records'] = $row['data'];
        return json_encode($result);

    }//end getCoreNetworkDates()

    /**
     * 获取数据日期(天)列表
     *
     * @return array 天列表
     */
    function getCoreNetworkDates()
    {
        $city = Input::get("city");
        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('CDR', $city);
        $table = 'L_ATTACH';
        $sql = "select DISTINCT date_id from $table;";
        $this->type = $city . ':signalingDiagnose';
        return $this->getValue($db, $sql);

    }//end getTimingDiagramChartData()

    /**
     * 获得信令诊断时序图数据
     *
     * @return string
     */
    public function getTimingDiagramChartData()
    {
        $dataBase = Input::get("city");
        $city = str_replace("CDR", 'CTR', $dataBase);
        $dateId = Input::get("date");
        $userInfo = Input::get("userInfo");
        $result = array();
        if (!(strpos($userInfo, '460') === 0)) {
            $userInfo = $userInfo;
            $userInfo = $this->getImsiByMsisdn($userInfo);
            if (!$userInfo) {
                $result['successData'] = [];
                $result['abortData'] = [];
                $result['success'] = [];
                $result['abort'] = [];
                $result['successData_w'] = [];
                $result['abortData_w'] = [];
                $result['success_w'] = [];
                $result['abort_w'] = [];
                return json_encode($result);
            }
        }
        $successData = array();
        $success = array();
        $abortData = array();
        $abort = array();
        $successData_w = array();
        $success_w = array();
        $abortData_w = array();
        $abort_w = array();

        $coreTable = ['L_ATTACH', 'L_DETACH', 'L_BEARER_MODIFY', 'L_DEDICATED_BEARER_ACTIVATE', 'L_DEDICATED_BEARER_DEACTIVATE', 'L_HANDOVER', 'L_PDN_CONNECT', 'L_PDN_DISCONNECT', 'L_SERVICE_REQUEST', 'L_TAU'];
        foreach ($coreTable as $key => $table) {
            $rows = $this->switchTable($table, $dataBase)->where('date_id', $dateId)->whereIn('hour_id', [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23])->where('imsi', $userInfo)->get()->toArray();
            foreach ($rows as $row) {
                $eventTime = $row['eventTime'];
                $rowResult = $row['result'];
                $type = $table;
                if ($rowResult == 'SUCCESS') {
                    array_push($successData, array(intval($eventTime . '000') + 8 * 3600000, 1));
                    array_push($success, array("eventTime" => intval($eventTime), "value" => 1, "type" => $type, "result" => "SUCCESS"));
                } else {
                    array_push($abortData, array(intval($eventTime . '000') + 8 * 3600000, 1));
                    array_push($abort, array("eventTime" => intval($eventTime), "value" => 1, "type" => $type, "result" => $rowResult));
                }
            }
        }
        $result['successData'] = $successData;
        $result['abortData'] = $abortData;
        $result['success'] = $success;
        $result['abort'] = $abort;

        $wlanTable = ['internalProcHoExecS1In', 'internalProcHoExecS1Out', 'internalProcHoExecX2In', 'internalProcHoExecX2Out', 'internalProcHoPrepS1In', 'internalProcHoPrepS1Out', 'internalProcHoPrepX2In', 'internalProcHoPrepX2Out', 'internalProcInitialCtxtSetup', 'internalProcRrcConnSetup', 'internalProcS1SigConnSetup'];
        foreach ($wlanTable as $key => $table) {
            $rows = $this->switchTable_CTR($table, $city)->where('date_id', $dateId)->whereIn('hour_id', [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23])->where('imsi', $userInfo)->get()->toArray();
            foreach ($rows as $row) {
                $eventTime = strtotime($row['eventTime']);
                $rowResult = $row['result'];
                $type = $table;
                if ($row['result'] == 'EVENT_VALUE_SUCCESS' || $row['result'] == 'EVENT_VALUE_SUCCESSFUL') {
                    array_push($successData_w, array(intval($eventTime . '000'), 2));
                    array_push($success_w, array("eventTime" => intval($eventTime), "value" => 2, "type" => $type, "result" => "SUCCESS"));
                } else {
                    array_push($abortData_w, array(intval($eventTime . '000'), 2));
                    array_push($abort_w, array("eventTime" => intval($eventTime), "value" => 2, "type" => $type, "result" => $rowResult));
                }
            }
        }

        $result['successData_w'] = $successData_w;
        $result['abortData_w'] = $abortData_w;
        $result['success_w'] = $success_w;
        $result['abort_w'] = $abort_w;
        return json_encode($result);
    }//end getWlanNetworkDiagnoseData()

    /**
     * 获得对应表的链接
     *
     * @return Model
     */
    public function switchTable_CTR($table, $city)
    {
        switch ($table) {
            case 'internalProcHoExecS1In':
                $conn = new InternalProcHoExecS1In;
                break;
            case 'internalProcHoExecS1Out':
                $conn = new InternalProcHoExecS1Out;
                break;
            case 'internalProcHoExecX2In':
                $conn = new InternalProcHoExecX2In;
                break;
            case 'internalProcHoExecX2Out':
                $conn = new InternalProcHoExecX2Out;
                break;
            case 'internalProcHoPrepS1In':
                $conn = new InternalProcHoPrepS1In;
                break;
            case 'internalProcHoPrepS1Out':
                $conn = new InternalProcHoPrepS1Out;
                break;
            case 'internalProcHoPrepX2In':
                $conn = new InternalProcHoPrepX2In;
                break;
            case 'internalProcHoPrepX2Out':
                $conn = new InternalProcHoPrepX2Out;
                break;
            case 'internalProcInitialCtxtSetup':
                $conn = new InternalProcInitialCtxtSetup;
                break;
            case 'internalProcRrcConnSetup':
                $conn = new InternalProcRrcConnSetup;
                break;
            case 'internalProcS1SigConnSetup':
                $conn = new InternalProcS1SigConnSetup;
                break;
        }
        $conn->setConnection($city);
        return $conn;
    }//end getWlanDefaultData()

    /**
     * 无线网测一级展示数据查询
     *
     * @return array 无线网诊断数据概览
     */
    function getWlanNetworkDiagnoseData()
    {
        $dataBase = input::get("city");
        $city = str_replace("CDR", 'CTR', $dataBase);
        $dateId = Input::get("dateTime");
        $hourId = Input::get("hourId");
        $userInfo = Input::get("userInfo");
        $data = array();
        if (!(strpos($userInfo, '460') === 0)) {
            $userInfo = $this->getImsiByMsisdn($userInfo);
            if ($userInfo == '') {
                return $this->getWlanDefaultData();
            }
        }

        $data['categories'] = ['internalProcHoExecS1In', 'internalProcHoExecS1Out', 'internalProcHoExecX2In', 'internalProcHoExecX2Out', 'internalProcHoPrepS1In', 'internalProcHoPrepS1Out', 'internalProcHoPrepX2In', 'internalProcHoPrepX2Out', 'internalProcInitialCtxtSetup', 'internalProcRrcConnSetup', 'internalProcS1SigConnSetup'];
        $data['series'] = [];
        foreach ($data['categories'] as $key => $value) {
            $conn = $this->switchTable_CTR($value, $city)->where('date_id', $dateId)->whereIn('hour_id', $hourId)->where('imsi', $userInfo);
            if ($value == 'internalProcInitialCtxtSetup' || $value == 'internalProcRrcConnSetup' || $value == 'internalProcS1SigConnSetup') {
                $count = $conn->where('result', '!=', 'EVENT_VALUE_SUCCESS')->count();
            } else {
                $count = $conn->where('result', '!=', 'EVENT_VALUE_SUCCESSFUL')->count();
            }
            array_push($data['series'], $count);
        }
        $data['imsi'] = $userInfo;
        return $data;

    }//end getWlanNetworkDiagnoseDetailHeader()

    /**
     * 获得无线侧缺省诊断数据
     *
     * @return array 无线侧缺省诊断数据
     */
    public function getWlanDefaultData()
    {
        $categories = [
            "internalProcHoExecS1In",
            "internalProcHoExecS1Out",
            "internalProcHoExecX2In",
            "internalProcHoExecX2Out",
            "internalProcHoPrepS1In",
            "internalProcHoPrepS1Out",
            "internalProcHoPrepX2In",
            "internalProcHoPrepX2Out",
            "internalProcInitialCtxtSetup",
            "internalProcRrcConnSetup",
            "internalProcS1SigConnSetup",
        ];
        $series = [
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
            0,
        ];
        $data['series'] = $series;
        $data['categories'] = $categories;
        $data['imsi'] = '';
        return $data;

    }//end getWlanNetworkDiagnoseDetail()

    /**
     * 获得无线网侧诊断数据表头
     *
     * @return array 无线侧诊断数据表头
     */
    function getWlanNetworkDiagnoseDetailHeader()
    {
        $dataBase = Input::get("city");
        $city = str_replace("CDR", 'CTR', $dataBase);
        $tableName = Input::get("tableName");
        return $this->switchTable_CTR($tableName, $city)->first()->toArray();
    }//end getCitys()

    /**
     * 获得无线网侧诊断数据详细
     *
     * @return string 无线侧诊断数据详细(JSON)
     */
    function getWlanNetworkDiagnoseDetail()
    {
        $dataBase = Input::get("city");
        $city = str_replace("CDR", 'CTR', $dataBase);
        $tableName = Input::get("tableName");
        $dateId = Input::get("dateTime");
        $hourId = Input::get("hourId");
        $imsi = Input::get("imsi");
        $rows = Input::get('limit', 10);

        $conn = $this->switchTable_CTR($tableName, $city)->where('date_id', $dateId)->whereIn('hour_id', $hourId)->where('imsi', $imsi);
        if ($tableName == 'internalProcInitialCtxtSetup' || $tableName == 'internalProcRrcConnSetup' || $tableName == 'internalProcS1SigConnSetup') {
            $row = $conn->where('result', '!=', 'EVENT_VALUE_SUCCESS')->paginate($rows)->toArray();
        } else {
            $row = $conn->where('result', '!=', 'EVENT_VALUE_SUCCESSFUL')->paginate($rows)->toArray();
        }

        $result = array();
        $result["total"] = $row['total'];
        $result['records'] = $row['data'];
        return json_encode($result);

    }

    /**
     * 获得城市列表
     *
     * @return string
     */
    public function getCitys()
    {
        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('CDR');
        $sql = "show dataBases";
        $res = $db->query($sql);
        $items = array();
        $row = $res->fetchAll(PDO::FETCH_ASSOC);
        foreach ($row as $r) {
            if ($r['DATABASE'] != 'Global') {
                $CHCity = $dbc->getCDRToCHName($r['DATABASE']);
                array_push($items, $CHCity . "-" . $r['DATABASE']);
            }
        }

        return json_encode($items);

    }

}//end class
