<?php

/**
 * XinlinghuisuController.php
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
 * 信令回溯
 * Class XinlinghuisuController
 *
 * @category ComplaintHandling
 * @package  App\Http\Controllers\ComplaintHandling
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class XinlinghuisuController extends MyRedis
{


    /**
     * 获得分页事件列表
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
        $db = $dbc->getDB('CTR', $dataBase);
        $filter = $this->getFilter('event');
        $result = array();
        $sql = "select * from eventDetail $filter order by eventTime asc";
        if (Input::get("viewType") == 'table') {
            $rs = $db->query("select count(*) as sum from eventDetail" . $filter);
            $row = $rs->fetchAll(PDO::FETCH_ASSOC);
            $result["total"] = $row[0]['sum'];
            $sql = $sql . " limit $offset,$rows";
        }

        $res = $db->query($sql);
        $row = $res->fetchAll(PDO::FETCH_ASSOC);
        $items = array();
        foreach ($row as $qr) {
            array_push($items, $qr);
        }

        $result['rows'] = $items;
        return json_encode($result);

    }//end getEventData()


    /**
     * 拼接筛选字符串
     *
     * @param string $type 事件类型
     *
     * @return string 筛选SQL字串
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

            $imsi = Input::get("imsi");
            $city = Input::get("dataBase");
            if (isset($imsi) && $imsi != '') {
                $filter = $this->checkFilter($filter);
                $temp = substr($imsi, 0, 1);
                if ($temp == "1") {
                    $dataBase = "CDR_" . explode("_", $city)[1];
                    $dbc = new DataBaseConnection();
                    $db = $dbc->getDB('CDR', $dataBase);
                    $sql = "select imsi from userInfo where msisdn = '$imsi'";
                    $rs = $db->query($sql);
                    $row = $rs->fetchAll(PDO::FETCH_ASSOC);
                    if ($row) {
                        $imsi = $row[0]['imsi'];
                    }
                }

                $filter = "$filter imsi ='" . $imsi . "'";
            }

            $hours = Input::get("hours");
            if (isset($hours) && $hours != '') {
                $hours_str = implode(",", $hours);
                $filter = $this->checkFilter($filter);
                $filter = "$filter hour_id in ($hours_str)";
            }

            if (Input::get("filterSection") == 'true') {
                $filter = $this->checkFilter($filter);
                $filter = " $filter imsi='" . input::get("ueRefChoosed") . "'";
            }
        }//end if

        if ($filter != '') {
            $filter = " where " . $filter;
        }

        return $filter;

    }//end getFilter()


    /**
     * Please...
     *
     * @param string $filter 筛选条件
     *
     * @return string SQL字串
     */
    public function checkFilter($filter)
    {
        if ($filter != '') {
            return "$filter and ";
        }
        return '';
    }//end checkFilter()


    /**
     * 获得全量事件列表
     *
     * @return string
     */
    public function getAllEventData()
    {
        $dataBase = Input::get("dataBase");
        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('CTR', $dataBase);
        $filter = $this->getFilter('event');

        $sql = "select * from eventDetail $filter order by eventTime asc";
        $res = $db->query($sql);

        $row = $res->fetchAll(PDO::FETCH_ASSOC);
        $items = array();
        foreach ($row as $qr) {
            array_push($items, $qr);
        }

        $result = array();

        $head = $db->query("select COLUMN_NAME from information_schema.COLUMNS where table_name = 'eventDetail' and table_schema = 'CTR'");
        $text = array();
        foreach ($head as $h) {
            array_push($text, $h["COLUMN_NAME"]);
        }

        $result['text'] = implode(",", $text);
        $result['rows'] = $items;
        return json_encode($result);

    }//end getAllEventData()


    /**
     * 获得信令详细解码
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
     * 导出指定用户信令流程
     *
     * @return string
     */
    public function exportCSV()
    {
        $fileContent = Input::get("fileContent");
        $ueRef = Input::get("ueRef");
        date_default_timezone_set('PRC');
        $filename = "common/files/信令流程_imsi" . $ueRef . "_" . date('YmdHis') . ".csv";

        $csvContent = mb_convert_encoding($fileContent, 'GBK');
        $fp = fopen($filename, "w");
        fwrite($fp, $csvContent);
        fclose($fp);
        return $filename;

    }//end exportCSV()


    /**
     * 获得数据日期(天)列表
     *
     * @return string
     */
    public function getDataGroupByDate()
    {
        $dataBase = Input::get("dataBase");
        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('CTR', $dataBase);
        $table = 'eventDetail';
        $sql = "select distinct date_id from $table";
        $this->type = $dataBase . ':xinlinghuisu';
        return json_encode($this->getValue($db, $sql));

    }//end getDataGroupByDate()


    /**
     * 获得城市列表
     *
     * @return array 城市列表
     */
    public function getCityDate()
    {
        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('CTR');
        $sql = "SHOW DATABASES";
        $rs = $db->query($sql);
        $rows = $rs->fetchAll(PDO::FETCH_ASSOC);
        $dbc = new DataBaseConnection();
        $cityArr = array();
        foreach ($rows as $row) {
            $city = $dbc->getCHAndCtrName($row['DATABASE']);
            array_push($cityArr, $city);
        }
        return $cityArr;
    }//end getCityDate()
}//end class
