<?php

/**
 * SignalingBacktrackingController.php
 *
 * @category ComplaintHandling
 * @package  App\Http\Controllers\ComplaintHandling
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
namespace App\Http\Controllers\ComplaintHandling;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Utils\FileUtil;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Config;
use App\Models\Mongs\Task;
use App\Models\Mongs\EventDetail;

/**
 * 信令回溯
 * Class SignalingBacktrackingController
 *
 * @category ComplaintHandling
 * @package  App\Http\Controllers\ComplaintHandling
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class SignalingBacktrackingController extends Controller
{

    /**
     * 获得城市名
     *
     * @return string
     */
    public function getDataBase()
    {
        $type = Input::get("type");
        $user = Auth::user()->user;
        if ($user != 'admin') {
            $row = Task::where('type', $type)->where('status', 'complete')->where('owner', $user)->orderBy('taskName', 'asc')->get()->toArray();
        } else {
            $row = Task::where('type', $type)->where('status', 'complete')->orderBy('taskName', 'asc')->get()->toArray();
        }
        $items = array();
        foreach ($row as $qr) {
            array_push($items, ["id" => $qr["taskName"], "text" => $qr["taskName"]]);
        }
        return json_encode($items);

    }//end getDataBase()


    /**
     * 获得事件名
     *
     * @return string
     */
    public function getEventNameandEcgi()
    {
        $database = Input::get("database");
        Config::set("database.connections.kget.database", $database);
        $row = EventDetail::groupBy('eventName')->orderBy('eventName', 'asc')->get()->toArray();
        $items1 = array();
        foreach ($row as $qr) {
            array_push($items1, ["label" => $qr["eventName"], "value" => $qr["eventName"]]);
        }
        $returnData["eventName"] = $items1;
        return json_encode($returnData);

    }//end getEventNameandEcgi()

    /**
     * 获得时间概览表头
     *
     * @return string
     */
    public function getEventData()
    {
        $database = Input::get("db");
        $rows = Input::get('rows', 50);

        Config::set("database.connections.kget.database", $database);
        $eventDetail = new EventDetail;
        if (Input::get("filterSection") == 'true') {
            $eventDetail = $eventDetail->where('ueRef', input::get("ueRefChoosed"));
        } else {
            $eventName = Input::get("eventName");
            if (isset($eventName) && $eventName != '') {
                $eventDetail = $eventDetail->whereIn('eventName', $eventName);
            }

            $imsi = Input::get("imsi");
            if (isset($imsi) && $imsi != '') {
                $eventDetail = $eventDetail->where('imsi', $imsi);
            }

            $ueRef = Input::get("ueRef");
            if (isset($ueRef) && $ueRef != '') {
                $eventDetail = $eventDetail->whereIn('ueRef', explode(",", $ueRef));
            }

            $mmeS1apId = Input::get("mmeS1apId");
            if (isset($mmeS1apId) && $mmeS1apId != '') {
                $eventDetail = $eventDetail->whereIn('mmeS1apId', explode(",", $mmeS1apId));
            }
        }
        $eventDetail = $eventDetail->orderBy('eventTime', 'asc');
        $result = array();
        if (Input::get("viewType") == 'table') {
            $items = array();
            $row = $eventDetail->paginate($rows)->toArray();
            $result["total"] = $row['total'];
            foreach ($row['data'] as $qr) {
                array_push($items, $qr);
            }
            $result['rows'] = $items;
        } else {
            $row = $eventDetail->get()->toArray();
            $result['rows'] = $row;
        }
        return json_encode($result);

    }//end getEventData()


    /**
     * 获得筛选SQL字符串
     *
     * @param string $type 类型
     *
     * @return string
     */
    public function getFilter($type)
    {
        $filter = '';
        if ($type == "event") {
            $eventName = input::get("eventName");

            if (isset($eventName) && $eventName != '') {
                $filter = $this->checkFilter($filter);
                $eventName = implode(",", $eventName);
                $filter = "$filter eventName in('" . implode("','", explode(",", $eventName)) . "')";
            }

            $imsi = input::get("imsi");
            if (isset($imsi) && $imsi != '') {
                $filter = $this->checkFilter($filter);
                $filter = "$filter imsi ='" . $imsi . "'";
            }

            $ueRef = input::get("ueRef");
            if (isset($ueRef) && $ueRef != '') {
                $filter = $this->checkFilter($filter);
                $filter = "$filter ueRef in('" . implode("','", explode(",", $ueRef)) . "')";
            }

            $mmeS1apId = input::get("mmeS1apId");
            if (isset($mmeS1apId) && $mmeS1apId != '') {
                $filter = $this->checkFilter($filter);
                $filter = "$filter mmeS1apId in('" . implode("','", explode(",", $mmeS1apId)) . "')";
            }
        }//end if

        if ($filter != '') {
            $filter = " where " . $filter;
        }

        return $filter;

    }//end getFilter()


    /**
     * What's it?
     *
     * @param string $filter Filter
     *
     * @return string
     */
    public function checkFilter($filter)
    {
        if ($filter != '') {
            return "$filter and ";
        }
        return '';
    }//end checkFilter()


    /**
     * 获得全量事件概览
     *
     * @return string
     */
    public function getAllEventData()
    {
        $database = Input::get("db");
        Config::set("database.connections.kget.database", $database);
        $eventDetail = new EventDetail;
        $text = $eventDetail->getVisible();
        if (Input::get("filterSection") == 'true') {
            $eventDetail = $eventDetail->where('ueRef', input::get("ueRefChoosed"));
        } else {
            $eventName = Input::get("eventName");
            if (isset($eventName) && $eventName != '') {
                $eventDetail = $eventDetail->whereIn('eventName', $eventName);
            }

            $imsi = Input::get("imsi");
            if (isset($imsi) && $imsi != '') {
                $eventDetail = $eventDetail->where('imsi', $imsi);
            }

            $ueRef = Input::get("ueRef");
            if (isset($ueRef) && $ueRef != '') {
                $eventDetail = $eventDetail->whereIn('ueRef', explode(",", $ueRef));
            }

            $mmeS1apId = Input::get("mmeS1apId");
            if (isset($mmeS1apId) && $mmeS1apId != '') {
                $eventDetail = $eventDetail->whereIn('mmeS1apId', explode(",", $mmeS1apId));
            }
        }
        $row = $eventDetail->orderBy('eventTime', 'asc')->get()->toArray();
        $items = array();
        foreach ($row as $qr) {
            array_push($items, $qr);
        }
        $result = array();

        $fileName    = "common/files/信令流程_ueRef".$ueRef."_".date('YmdHis').".csv";
        $fileUtil = new FileUtil();
        $fileUtil->resultToCSV2(implode(",", $text), $items, $fileName);
        $result['fileName'] = $fileName;
        $result['result'] = 'true';
        return json_encode($result);
    }//end getAllEventData()


    /**
     * 获得信令详情
     *
     * @return string
     */
    public function showMessage()
    {
        $id = Input::get("id");
        $dbName = Input::get("db");
        $command = 'sudo common/sh/wsharkparser.sh ' . $dbName . " " . $id;
        $return = exec($command);
        return "common/files/" . $return;
    }//end showMessage()


    /**
     * 导出信令流程
     *
     * @return string
     */
    public function exportCSV()
    {
        $fileContent = Input::get("fileContent");
        $ueRef = Input::get("ueRef");
        $filename = "common/files/信令流程_ueRef" . $ueRef . "_" . date('YmdHis') . ".csv";
        $csvContent = mb_convert_encoding($fileContent, 'GBK');
        $fp = fopen($filename, "w");
        fwrite($fp, $csvContent);
        fclose($fp);
        return $filename;

    }//end exportCSV()


}//end class
