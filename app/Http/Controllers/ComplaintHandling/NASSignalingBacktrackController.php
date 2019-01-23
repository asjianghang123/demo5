<?php

/**
 * NASSignalingBacktrackController.php
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
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Common\MyRedis;
use PDO;

/**
 * NAS信令分析
 * Class NASSignalingBacktrackController
 *
 * @category ComplaintHandling
 * @package  App\Http\Controllers\ComplaintHandling
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class NASSignalingBacktrackController extends MyRedis
{


    /**
     * 获得分页信令列表
     *
     * @return string
     */
    public function getEventData()
    {
        $page = Input::get('page', 1);
        $rows = Input::get('rows', 50);
        $offset = (($page - 1) * $rows);
        $dataBase = Input::get("dataBase");
        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('CDR', $dataBase);

        $filter = $this->getFilter('event');
        $columns = "eventTime,date_id,hour_id,milliSec,duration,result,msisdn,imsi,imeiTac,imeisv,lCauseProtType,causeCode,subCauseCode,tai,ecgi,mmei";
        $result = array();
        $sql = "select * from (
                select $columns, 'L_ATTACH' as eventName FROM L_ATTACH $filter
                union all select $columns,'L_DETACH' as eventName FROM L_DETACH $filter
                union all select $columns,'L_BEARER_MODIFY' as eventName FROM L_BEARER_MODIFY $filter
                union all select $columns,'L_DEDICATED_BEARER_ACTIVATE' as eventName FROM L_DEDICATED_BEARER_ACTIVATE $filter
                union all select $columns,'L_DEDICATED_BEARER_DEACTIVATE' as eventName FROM L_DEDICATED_BEARER_DEACTIVATE $filter
                union all select $columns,'L_HANDOVER' as eventName FROM L_HANDOVER $filter
                union all select $columns,'L_PDN_CONNECT' as eventName FROM L_PDN_CONNECT $filter
                union all select $columns,'L_PDN_DISCONNECT' as eventName FROM L_PDN_DISCONNECT $filter
                union all select $columns,'L_SERVICE_REQUEST' as eventName FROM L_SERVICE_REQUEST $filter
                union all select $columns,'L_TAU' as eventName FROM L_TAU $filter
                ) as total order by eventTime";

        $sum_sql = "SELECT sum(num) as sum FROM (
                    select count(*) AS num FROM L_ATTACH $filter
                    union all select count(*) AS num FROM L_DETACH $filter
                    union all select count(*) AS num FROM L_BEARER_MODIFY $filter
                    union all select count(*) AS num FROM L_DEDICATED_BEARER_ACTIVATE $filter
                    union all select count(*) AS num FROM L_DEDICATED_BEARER_DEACTIVATE $filter
                    union all select count(*) AS num FROM L_HANDOVER $filter
                    union all select count(*) AS num FROM L_PDN_CONNECT $filter
                    union all select count(*) AS num FROM L_PDN_DISCONNECT $filter
                    union all select count(*) AS num FROM L_SERVICE_REQUEST $filter
                    union all select count(*) AS num FROM L_TAU $filter
                    ) as total";
        if (Input::get("viewType") == 'table') {
            $rs = $db->query($sum_sql);
            $row = $rs->fetchAll(PDO::FETCH_ASSOC);

            $result["total"] = $row[0]['sum'];
            $sql = $sql . " limit $offset,$rows";
        }

        $res = $db->query($sql);
        $row = $res->fetchAll(PDO::FETCH_ASSOC);
        $items = array();
        date_default_timezone_set('PRC');
        foreach ($row as $qr) {
            $qr['eventTime'] = date("Y-m-d H:i:s", $qr['eventTime']);
            array_push($items, $qr);
        }

        $result['rows'] = $items;
        return json_encode($result);

    }//end getEventData()


    /**
     * 拼接筛选SQL语句
     *
     * @param string $type 类型
     *
     * @return string
     */
    public function getFilter($type)
    {
        $filter = '';
        if ($type == "event") {
            $date = Input::get("date");
            if (isset($date) && $date != '') {
                $filter = $this->checkFilter($filter);
                $filter = "$filter date_id = '$date'";
            }

            $hours = Input::get("hours");
            if (isset($hours) && $hours != '') {
                $hours_str = implode(",", $hours);
                $filter = $this->checkFilter($filter);
                $filter = "$filter hour_id in ($hours_str)";
            } else {
                $filter = $this->checkFilter($filter);
            }

            $imsi = Input::get("imsi");
            if (isset($imsi) && $imsi != '') {
                $filter = $this->checkFilter($filter);
                $temp = substr($imsi, 0, 1);
                if ($temp == "1") {
                    $dataBase = Input::get("dataBase");
                    $dbc = new DataBaseConnection();
                    $db = $dbc->getDB('CDR', $dataBase);
                    $sql = "select imsi from userInfo where msisdn like '%" . $imsi . "' limit 1";
                    $res = $db->query($sql);
                    $row = $res->fetch(PDO::FETCH_ASSOC);
                    $imsi = $row['imsi'];
                }

                if ($imsi) {
                    $filter = "$filter imsi = $imsi";
                } else {
                    $filter = "$filter imsi = 123";
                }
            }

            if (Input::get("filterSection") == 'true') {
                $filter = $this->checkFilter($filter);
                $filter = " $filter CONCAT(imsi,'') ='" . input::get("ueRefChoosed") . "'";
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

    }//end checkFilter()


    /**
     * 导出全量信令
     *
     * @return string
     */
    public function getAllEventData()
    {
        $dataBase = Input::get("dataBase");
        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('CDR', $dataBase);

        $filter = $this->getFilter('event');
        $columns = "eventTime,date_id,hour_id,milliSec,duration,result,msisdn,imsi,imeiTac,imeisv,lCauseProtType,causeCode,subCauseCode,tai,ecgi,mmei";
        $sql = "select * from (
                select $columns, 'L_ATTACH' as eventName FROM L_ATTACH $filter
                union all select $columns,'L_DETACH' as eventName FROM L_DETACH $filter
                union all select $columns,'L_BEARER_MODIFY' as eventName FROM L_BEARER_MODIFY $filter
                union all select $columns,'L_DEDICATED_BEARER_ACTIVATE' as eventName FROM L_DEDICATED_BEARER_ACTIVATE $filter
                union all select $columns,'L_DEDICATED_BEARER_DEACTIVATE' as eventName FROM L_DEDICATED_BEARER_DEACTIVATE $filter
                union all select $columns,'L_HANDOVER' as eventName FROM L_HANDOVER $filter
                union all select $columns,'L_PDN_CONNECT' as eventName FROM L_PDN_CONNECT $filter
                union all select $columns,'L_PDN_DISCONNECT' as eventName FROM L_PDN_DISCONNECT $filter
                union all select $columns,'L_SERVICE_REQUEST' as eventName FROM L_SERVICE_REQUEST $filter
                union all select $columns,'L_TAU' as eventName FROM L_TAU $filter
                ) as total order by eventTime";
        $res = $db->query($sql);

        $row = $res->fetchAll(PDO::FETCH_ASSOC);
        date_default_timezone_set('PRC');
        $ueRef = Input::get("imsi");
        $filename = "common/files/NAS信令流程_imsi" . $ueRef . "_" . date('YmdHis') . ".csv";
        $fp = fopen($filename, "w");
        $csvContent = mb_convert_encoding($columns . "\n", 'GBK');
        fwrite($fp, $csvContent);
        date_default_timezone_set('PRC');
        foreach ($row as $qr) {
            $qr['eventTime'] = date("Y-m-d H:i:s", $qr['eventTime']);
            fputcsv($fp, $qr);
        }

        fclose($fp);
        return json_encode($filename);

    }//end getAllEventData()


    /**
     * 获得全量信令
     *
     * @return string
     */
    public function getAllEventData1()
    {
        $dataBase = Input::get("dataBase");
        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('CDR', $dataBase);

        $filter = $this->getFilter('event');
        $columns = "eventTime,date_id,hour_id,milliSec,duration,result,msisdn,imsi,imeiTac,imeisv,lCauseProtType,causeCode,subCauseCode,tai,ecgi,mmei";
        $sql = "select * from (
                select $columns, 'L_ATTACH' as eventName FROM L_ATTACH $filter
                union all select $columns,'L_DETACH' as eventName FROM L_DETACH $filter
                union all select $columns,'L_BEARER_MODIFY' as eventName FROM L_BEARER_MODIFY $filter
                union all select $columns,'L_DEDICATED_BEARER_ACTIVATE' as eventName FROM L_DEDICATED_BEARER_ACTIVATE $filter
                union all select $columns,'L_DEDICATED_BEARER_DEACTIVATE' as eventName FROM L_DEDICATED_BEARER_DEACTIVATE $filter
                union all select $columns,'L_HANDOVER' as eventName FROM L_HANDOVER $filter
                union all select $columns,'L_PDN_CONNECT' as eventName FROM L_PDN_CONNECT $filter
                union all select $columns,'L_PDN_DISCONNECT' as eventName FROM L_PDN_DISCONNECT $filter
                union all select $columns,'L_SERVICE_REQUEST' as eventName FROM L_SERVICE_REQUEST $filter
                union all select $columns,'L_TAU' as eventName FROM L_TAU $filter
                ) as total order by eventTime";
        $res = $db->query($sql);

        $row = $res->fetchAll(PDO::FETCH_ASSOC);
        $items = array();
        foreach ($row as $qr) {
            array_push($items, $qr);
        }

        $result = array();

        $result['text'] = $columns;
        $result['rows'] = $items;
        return json_encode($result);

    }//end getAllEventData1()


    /**
     * 获得详细信令
     *
     * @return string
     */
    public function showMessage()
    {
        $id = Input::get("id");
        $dbName = Input::get("db");
        $date_id = Input::get("date_id");
        $hour_id = Input::get("hour_id");
        $command = 'sudo common/sh/wsharkparser_mycat.sh mycat ' . $dbName . " " . $id . " " . $date_id . " " . $hour_id;
        exec($command);
        return "/mongs_web/ctrsystem/files/event_" . $dbName . "_" . $id . ".xml";
    }//end showMessage()


    /**
     * 导出用户信令流程
     *
     * @return string
     */
    public function exportCSV()
    {
        $fileContent = Input::get("fileContent");
        $ueRef = Input::get("ueRef");
        date_default_timezone_set('PRC');
        $filename = "common/files/NAS信令流程_imsi" . $ueRef . "_" . date('YmdHis') . ".csv";

        $csvContent = mb_convert_encoding($fileContent, 'GBK');
        $fp = fopen($filename, "w");
        fwrite($fp, $csvContent);
        fclose($fp);
        return $filename;
    }//end exportCSV()


    /**
     * 获得CDR数据日期列表
     *
     * @return string
     */
    public function getDataGroupByDate()
    {
        $dataBase = Input::get("dataBase");
        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('CDR', $dataBase);
        $table = 'L_ATTACH';
        $sql = "select distinct date_id from $table";
        $this->type = $dataBase . ':NASSignalingBacktrack';
        return json_encode($this->getValue($db, $sql));

    }//end getDataGroupByDate()


    /**
     * 获得城市列表
     *
     * @return string
     */
    public function getCityDate()
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
                array_push($items, array("label" => $CHCity, "value" => $r['DATABASE']));
            }
        }

        return json_encode($items);

    }//end getCityDate()


}//end class
