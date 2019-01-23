<?php

/**
 * HighInterferenceCellController.php
 *
 * @category BadCellAnalysis
 * @package  App\Http\Controllers\BadCellAnalysis
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
namespace App\Http\Controllers\BadCellAnalysis;

use App\Http\Controllers\Common\DataBaseConnection;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use PDO;

/**
 * 高干扰小区分析
 * Class HighInterferenceCellController
 *
 * @category BadCellAnalysis
 * @package  App\Http\Controllers\BadCellAnalysis
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class HighInterferenceCellController extends Controller
{


    /**
     * 获得城市名列表
     *
     * @return string 城市名列表
     */
    public function getCitys()
    {
        $dsn = new DataBaseConnection();
        $db  = $dsn->getDB('mongs', 'mongs');

        $sql = "select cityChinese,connName  from databaseconn ORDER BY cityChinese";
        $res = $db->query($sql);

        $row   = $res->fetchAll(PDO::FETCH_ASSOC);
        $items = array();
        foreach ($row as $qr) {
            if (count($items) > 0 && $items[(count($items) - 1)]['label'] == $qr['cityChinese']) {
                $items[(count($items) - 1)]['value'] = $items[(count($items) - 1)]['value'].",".$qr['connName'];
            } else {
                array_push($items, ["value" => $qr['connName'], "label" => $qr['cityChinese']]);
            }
        }

        echo json_encode($items);

    }//end getCitys()


    /**
     * 获得单高干扰小区分页列表
     *
     * @return void
     */
    public function getCellData()
    {
        $page      = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $rows      = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 50;
        $offset    = (($page - 1) * $rows);
        $limit     = " limit $offset,$rows";
        $sortBy    = isset($_REQUEST['sortBy']) ? $_REQUEST['sortBy'] : "times";
        $direction = isset($_REQUEST['direction']) ? $_REQUEST['direction'] : 'desc';
        $order     = " order by $sortBy $direction ";

        $dsn = new DataBaseConnection();
        $db  = $dsn->getDB('autokpi');

        $phpCity = input::get("city");
        $citys   = explode(",", $phpCity);
        $citystr = "";
        foreach ($citys as $city) {
            $citystr .= "'".$city."',";
        }

        $citystr = substr($citystr, 0, -1);

        $dateFrom = input::get("datefrom");
        $dateTo   = input::get("dateto");
        $cell     = input::get("cell");
        $table    = input::get("table");
        $hours    = input::get("hours");

        if ($dateFrom == $dateTo) {
            $filter = "where day_id = '$dateFrom'";
        } else {
            $filter = "where day_id >= '$dateFrom' and day_id <='$dateTo'";
        }

        if ($citystr) {
            $filter .= " and city in ($citystr)";
        }

        if ($cell) {
            $filter .= "and cell = '$cell'";
        }

        if ($hours != '') {
            $hour   = implode(',', $hours);
            $filter = $filter." AND hour_id in($hour)";
        }

        $result = array();
        $sql    = "select count(*) from (select city,subNetwork,cell, count(*) as times from $table ".$filter." group by subNetwork,cell order by times desc) as tmp";
        $res    = $db->query($sql);
        $row    = $res->fetchAll(PDO::FETCH_ASSOC);
        $result["total"] = $row[0]['count(*)'];

        $sql   = "select * from (select city,subNetwork,cell, count(*) as times from $table ".$filter." group by subNetwork,cell order by times desc) as search_tmp $order".$limit;
        $res   = $db->query($sql);
        $row   = $res->fetchAll(PDO::FETCH_ASSOC);
        $items = array();
        foreach ($row as $qr) {
            array_push($items, $qr);
        }

        $result['records'] = $items;
        echo json_encode($result);

    }//end getCellData()


    /**
     * 获得全部高干扰小区列表
     *
     * @return void
     */
    public function getAllCellData()
    {
        $dsn = new DataBaseConnection();
        $db  = $dsn->getDB('autokpi', 'AutoKPI');

        $phpCity = input::get("city");
        $citys   = explode(",", $phpCity);
        $citystr = "";
        foreach ($citys as $city) {
            $citystr .= "'".$city."',";
        }

        $citystr = substr($citystr, 0, -1);

        $dateFrom = input::get("datefrom");
        $dateTo   = input::get("dateto");
        $cell     = input::get("cell");
        $table    = input::get("table");
        $type     = input::get("type");
        $hours    = input::get("hours");

        if ($dateFrom == $dateTo) {
            $filter    = "where day_id = '$dateFrom'";
            $date_file = $dateFrom;
        } else {
            $filter    = "where day_id >= '$dateFrom' and day_id <='$dateTo'";
            $date_file = $dateFrom."_".$dateTo;
        }

        if ($citystr) {
            $filter .= " and city in ($citystr)";
        }

        if ($cell) {
            $filter .= "and cell = '$cell'";
        }

        if ($hours != '') {
            $hour   = implode(',', $hours);
            $filter = $filter." AND hour_id in($hour)";
        }

        $sql   = "select * from (select city,subNetwork,cell, count(*) as times from $table ".$filter." group by subNetwork,cell order by times desc) as search_tmp";
        $res   = $db->query($sql);
        $row   = $res->fetchAll(PDO::FETCH_ASSOC);
        $items = array();
        foreach ($row as $qr) {
            array_push($items, $qr);
        }

        $result           = array();
        $result["text"]   = "city,subNetwork,cell,times";
        $result['rows']   = $items;
        $result['total']  = count($items);
        $result['result'] = 'true';

        $filename = "common/files/高干扰小区分析_".$type."_".$date_file."_".date('YmdHis').".csv";
        $this->resultToCSV2($result, $filename);
        $result['filename'] = $filename;
        if (count($items) > 1000) {
            $result['rows'] = array_slice($items, 0, 1000);
        }

        echo json_encode($result);

    }//end getAllCellData()


    /**
     * 写入CSV文件
     *
     * @param array  $result   导出数据
     * @param string $filename CSV文件名
     *
     * @return void
     */
    protected function resultToCSV2($result, $filename)
    {
        $csvContent = mb_convert_encoding($result['text']."\n", 'GBK', "UTF-8");
        $fp         = fopen($filename, "w");
        fwrite($fp, $csvContent);
        foreach ($result['rows'] as $row) {
            fputcsv($fp, $row);
        }

        fclose($fp);

    }//end resultToCSV2()


    /**
     * 获得告警数据
     *
     * @return void
     */
    public function getAlarmData()
    {
        $page   = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $rows   = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 50;
        $offset = (($page - 1) * $rows);
        $limit  = " limit $offset,$rows";

        $dsn = new DataBaseConnection();
        $db  = $dsn->getDB('alarm', 'Alarm');

        $rowData   = input::get('rowData');
        $date1     = input::get('alarmHighInterfereCellDataFrom');
        $date2     = input::get('alarmHighInterfereCellDataTo');
        $rowData_1 = $rowData['cell'];
        $rowData_2 = explode('_', $rowData_1);
        $rowData_3 = $rowData_2[0];
        if ($rowData_3 == $rowData_1) {
            $rowData_3 = substr($rowData_3, 0, (strlen($rowData_3) - 1));
        }

        $filter = " where meContext = '$rowData_3' and to_days(Event_time) between to_days('$date1') and to_days('$date2') ";

        $result = array();
        $res    = $db->query("select count(*) from FMA_alarm_log".$filter);
        $row    = $res->fetchAll(PDO::FETCH_ASSOC);
        $result["total"] = $row[0]['count(*)'];

        $sql   = "select Event_time,meContext,eutranCell,Cease_time,SP_text,Problem_text from FMA_alarm_log".$filter." order by Event_time asc".$limit;
        $res   = $db->query($sql);
        $row   = $res->fetchAll(PDO::FETCH_ASSOC);
        $items = array();
        foreach ($row as $qr) {
            array_push($items, $qr);
        }

        $result['records'] = $items;
        echo json_encode($result);

    }//end getAlarmData()


    /**
     * 获得单高干扰小区发生时间(天)列表
     *
     * @return void
     */
    public function getTimeList()
    {
        $dsn = new DataBaseConnection();
        $db  = $dsn->getDB('autokpi');

        $dateFrom = input::get("datefrom");
        $dateTo   = input::get("dateto");
        $cell     = input::get("cell");
        $url      = input::get("url");
        if ($dateFrom == $dateTo) {
            $sql = "select DISTINCT day_id from interfereCell where day_id='$dateFrom' and cell='$cell'";
        } else {
            $sql = "select DISTINCT day_id from interfereCell where day_id>='$dateFrom' and day_id<='$dateTo' and cell='$cell'";
        }

        $series     = array();
        $returnData = array();
        $res        = $db->query($sql);
        $row        = $res->fetchAll(PDO::FETCH_ASSOC);
        $numIndex   = 0;
        foreach ($row as $qr) {
            $numIndex++;
            $time            = strval($qr['day_id']);
            $time            = mb_convert_encoding($time, 'gb2312', 'utf-8');
            $series['id']    = $numIndex;
            $series['label'] = $time;
            $series['value'] = $time;
            array_push($returnData, $series);
        }

        $open = fopen($url, "w");
        fwrite($open, json_encode($returnData));
        fclose($open);
        echo json_encode($returnData);

    }//end getTimeList()


    /**
     * 获得时域图表数据
     *
     * @return void
     */
    public function getTimeChartData()
    {
        $dsn = new DataBaseConnection();
        $db  = $dsn->getDB('autokpi');

        $dateFrom = input::get("datefrom");
        $dateTo   = input::get("dateto");
        $sf1      = input::get("sf1");
        $sf2      = input::get("sf2");
        $sf6      = input::get("sf6");
        $sf7      = input::get("sf7");
        $cell     = input::get("cell");

        if ($dateFrom == $dateTo) {
            $sql = "select day_id,hour_id,$sf1,$sf2,$sf6,$sf7 from interfereCell where day_id='$dateFrom' and cell='$cell'";
        } else {
            $sql = "select day_id,hour_id,$sf1,$sf2,$sf6,$sf7 from interfereCell where day_id>='$dateFrom' and day_id<='$dateTo' and cell='$cell'";
        }

        $items      = array();
        $returnData = array();
        $series     = array();
        $series_2   = array();
        $series_3   = array();
        $series_4   = array();
        $data       = array();
        $data_2     = array();
        $data_3     = array();
        $data_4     = array();
        $categories = array();

        $res = $db->query($sql);
        $row = $res->fetchAll(PDO::FETCH_NUM);
        foreach ($row as $line) {
            $time = strval(strval($line[0])." ".strval($line[1])).":00";
            $time = mb_convert_encoding($time, 'gb2312', 'utf-8');
            array_push($data, $line[2]);
            array_push($data_2, $line[3]);
            array_push($data_3, $line[4]);
            array_push($data_4, $line[5]);
            array_push($categories, $time);
        }

        $series['name'] = $sf1;
        $series['data'] = $data;

        $series_2['name'] = $sf2;
        $series_2['data'] = $data_2;

        $series_3['name'] = $sf6;
        $series_3['data'] = $data_3;

        $series_4['name'] = $sf7;
        $series_4['data'] = $data_4;

        array_push($items, $series);
        array_push($items, $series_2);
        array_push($items, $series_3);
        array_push($items, $series_4);

        $returnData['categories'] = $categories;
        $returnData['series']     = $items;

        echo json_encode($returnData);

    }//end getTimeChartData()


    /**
     * 获得频域图表数据
     *
     * @return void
     */
    public function getFrequencyChartData()
    {
        $dsn    = new DataBaseConnection();
        $db     = $dsn->getDB('autokpi');
        $cell   = input::get("cell");
        $day_id = input::get("day_id");

        $filter_categories = "select COLUMN_NAME from information_schema.COLUMNS where table_name='interfereCell'";
        $filter_series     = "select * from interfereCell where day_id='$day_id' and cell='$cell'";

        $items      = array();
        $returnData = array();
        $series     = array();
        $categories = array();

        $res = $db->query($filter_categories);
        $row = $res->fetchAll(PDO::FETCH_NUM);
        foreach ($row as $line_categories) {
            if (strstr($line_categories[0], "PRB") == false) {
                continue;
            }

            array_push($categories, $line_categories[0]);
        }

        $res  = $db->query($filter_series);
        $rows = $res->fetchAll(PDO::FETCH_NUM);
        for ($j = 0; $j < count($rows); $j++) {
            $data = array();
            $i    = 0;
            foreach ($rows[$j] as $line) {
                if (($i >= 0 && $i <= 9) || $i == 20 || $i == 111 || $i == 112) {
                    $i++;
                    continue;
                }

                $i++;
                array_push($data, floatval($line));
            }

            $series['name'] = $rows[$j][2].'时';
            $series['data'] = $data;
            array_push($items, $series);
        }

        $returnData['categories'] = $categories;
        $returnData['series']     = $items;

        echo json_encode($returnData);

    }//end getFrequencyChartData()


    /**
     * 获得高干扰小区发生时间(天)列表
     *
     * @return array 高干扰小区发生时间(天)列表
     */
    public function highTime()
    {
        $dsn    = new DataBaseConnection();
        $db     = $dsn->getDB('autokpi');
        $table  = 'interfereCell';
        $result = array();
        $sql    = "select distinct day_id from $table";
        $rs     = $db->query($sql, PDO::FETCH_ASSOC);
        $test   = [];
        if ($rs) {
            $rows = $rs->fetchall();

            if (count($rows) > 0) {
                foreach ($rows as $row) {
                    $arr = explode(' ', $row['day_id']);
                    if ($arr[0] == '0000-00-00') {
                        continue;
                    }

                    array_push($test, $arr[0]);
                }

                return $test;
            } else {
                $result['error'] = 'error';
                return $result;
            }
        } else {
            $result['error'] = 'error';
            return $result;
        }//end if

    }//end highTime()


    /**
     * 获得LTE补邻区数据表头
     *
     * @return array LTE补邻区数据表头
     */
    public function getLTENeighborHeader1()
    {
        $cityEN = Input::get('city');
        $cityCH = $this->ENcityToCHcity($cityEN);
        $dbname = $this->getMRDatabase($cityCH);
        $dsn    = new DataBaseConnection();
        $db     = $dsn->getDB('MR', $dbname);
        $table  = 'mreServeNeigh';
        $result = array();
        $sql    = "select * from $table limit 1";
        $rs     = $db->query($sql, PDO::FETCH_ASSOC);
        if ($rs) {
            $rows = $rs->fetchall();
            if (count($rows) > 0) {
                return $rows[0];
            } else {
                $result['error'] = 'error';
                return $result;
            }
        } else {
            $result['error'] = 'error';
            return $result;
        }

    }//end getLTENeighborHeader1()


    /**
     * 获得LTE补邻区数据
     *
     * @return void
     */
    public function getLTENeighborData1()
    {
        $cell = Input::get('cell');
        $dsn  = new DataBaseConnection();
        $db   = $dsn->getDB('mongs', 'mongs');
        $sql  = "select ecgi from siteLte where cellName = '$cell'";
        $res  = $db->query($sql);
        $row  = $res->fetchAll(PDO::FETCH_ASSOC);
        $ecgi = $row[0]['ecgi'];

        $cityEN = Input::get('city');
        $cityCH = $this->ENcityToCHcity($cityEN);
        $dbname = $this->getMRDatabase($cityCH);

        $dateTime = Input::get('dateTime');

        $result = array();
        $return = array();
        $dsn    = new DataBaseConnection();
        $db     = $dsn->getDB('MR', $dbname);
        $table  = 'mreServeNeigh';
        $sql    = "select * from $table limit 1";
        $rs     = $db->query($sql, PDO::FETCH_ASSOC);
        $rows   = $rs->fetchall();
        $keys   = array_keys($rows[0]);
        $text   = '';
        foreach ($keys as $key) {
            if ($key == 'id') {
                continue;
            }

            $text .= $key.',';
        }

        $text           = substr($text, 0, (strlen($text) - 1));
        $result['text'] = $text;
        $sql            = "select * from $table where mr_LteScEarfcn = mr_LteNcEarfcn and ecgi = '$ecgi' and datetime_id like '".$dateTime."%'";
        $rs = $db->query($sql);
        if (!$rs) {
            $return["total"]   = 0;
            $result['rows']    = [];
            $return['records'] = [];
            echo json_encode($return);
            return;
        }

        $rows = $rs->fetchall(PDO::FETCH_ASSOC);

        $return["total"] = count($rows);
        $rowsId          = array();
        foreach ($rows as $row) {
            array_shift($row);
            array_push($rowsId, $row);
        }

        $result['rows'] = $rowsId;

        $sql     = "select * from $table where mr_LteScEarfcn = mr_LteNcEarfcn and ecgi = '$ecgi' and datetime_id like '".$dateTime."%' order by datetime_id";
        $rows    = $db->query($sql, PDO::FETCH_ASSOC)->fetchall();
        $allData = array();
        foreach ($rows as $row) {
            array_push($allData, $row);
        }

        $return['records'] = $allData;
        echo json_encode($return);

    }//end getLTENeighborData1()


}//end class
