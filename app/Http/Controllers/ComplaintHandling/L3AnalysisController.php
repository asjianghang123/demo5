<?php

/**
 * L3AnalysisController.php
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
 * L3信令分析
 * Class L3AnalysisController
 *
 * @category ComplaintHandling
 * @package  App\Http\Controllers\ComplaintHandling
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class L3AnalysisController extends MyRedis
{


    /**
     * 获得城市列表
     *
     * @return void
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

        echo json_encode($items);

    }//end getCitys()


    /**
     * 获得L3分析数据日期(天)列表
     *
     * @return string
     */
    public function getL3AnalysisData()
    {
        $dbName = Input::get("dataBase");
        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('CDR', $dbName);
        $sql = "select distinct date_id from causeCode";
        $this->type = $dbName . ':L3Analysis';
        return json_encode($this->getValue($db, $sql));

    }//end getL3AnalysisData()


    /**
     * 获取一级图形数据
     *
     * @return array
     */
    public function getChartData()
    {
        $dbName = Input::get("db");
        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('CDR', $dbName);

        $table = 'causeCode';
        $date = Input::get("date");
        $eventName = Input::get('eventName');
        $timesFailure = '';
        $rs = $db->query("select sum(timesFailure) timesFailure FROM causeCode where date_id='" . $date . "' and eventName='" . $eventName . "'");
        if ($rs) {
            $row = $rs->fetchAll(PDO::FETCH_ASSOC);
            $timesFailure = $row[0]['timesFailure'];
        } else {
            $result['flag'] = 'error';
        }

        $rs = $db->query("select sum(timesTotal) timesTotal from (SELECT database(),t.timesTotal FROM causeCode t where date_id='" . $date . "' and eventName='" . $eventName . "' GROUP BY database(),timesTotal)tt;");
        if (!$rs) {
            $result['flag'] = 'error';
            return $result;
        }

        $sql = "select causeCode,subCauseCode,sum(times) times from $table where date_id ='" . $date . "' and eventName='" . $eventName . "' and timesFailure!=0 GROUP BY causeCode,subCauseCode order by times desc";
        $rs = $db->query($sql);

        $series = array();
        $categories = array();

        $data = array();
        $result = array();
        if ($rs) {
            $rows = $rs->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                array_push($categories, $row['causeCode'] . "/" . $row['subCauseCode']);
                array_push($data, floatval(number_format(($row['times'] / $timesFailure * 100), 2)));
            }

            $series['data'] = $data;
            $series['name'] = 'causeCode/subCauseCode';
            $result['categories'] = $categories;
            $result['series'] = $series;
            $result['flag'] = 'true';
        } else {
            $result['flag'] = 'error';
        }

        return $result;

    }//end getChartData()


    /**
     * 获取图形对应表数据
     *
     * @return array
     */
    public function getTableData()
    {
        $page = Input::get('page', 1);
        $rows = Input::get('limit', 50);
        $offset = (($page - 1) * $rows);

        $dbName = Input::get("db");
        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('CDR', $dbName);

        $table = 'causeCode';
        $date = Input::get("date");
        $eventName = Input::get('eventName');
        $result = array();
        $rs = $db->query("select causeCode,subCauseCode from $table where date_id='" . $date . "' and eventName='" . $eventName . "' and timesFailure!=0 GROUP BY causeCode,subCauseCode order by causeCode,subCauseCode desc");
        if ($rs) {
            $row = $rs->fetchAll(PDO::FETCH_ASSOC);
            $result["total"] = count($row);
        } else {
            $result['message'] = '没有记录';
        }

        $timesFailure = '';
        $timesTotal = '';
        $rs = $db->query("select sum(timesFailure) timesFailure FROM causeCode where date_id='" . $date . "' and eventName='" . $eventName . "'");
        if ($rs) {
            $row = $rs->fetchAll(PDO::FETCH_ASSOC);
            $timesFailure = $row[0]['timesFailure'];
        } else {
            $result['message'] = '没有记录';
        }

        $rs = $db->query("select sum(timesTotal) timesTotal from (SELECT database(),t.timesTotal FROM causeCode t where date_id='" . $date . "' and eventName='" . $eventName . "' GROUP BY database(),timesTotal)tt;");
        if ($rs) {
            $row = $rs->fetchAll(PDO::FETCH_ASSOC);

            $timesTotal = $row[0]['timesTotal'];
        } else {
            $result['message'] = '没有记录';
        }

        $sql = "select date_id,causeCode,subCauseCode,sum(times) times from $table where date_id ='" . $date . "' and eventName='" . $eventName . "' and timesFailure!=0 GROUP BY causeCode,subCauseCode order by times desc;";
        $rs = $db->query($sql);
        $items = array();
        if ($rs) {
            $row = $rs->fetchAll(PDO::FETCH_ASSOC);
            for ($i = $offset; $i < ($offset + $rows) && $i < count($row); $i++) {
                $r = $row[$i];
                $record["causeCode"] = $r['causeCode'];
                $record["subCauseCode"] = $r['subCauseCode'];
                $record["times"] = floatval($r["times"]);
                $record["timesFailure"] = floatval($timesFailure);
                $record["timesTotal"] = floatval($timesTotal);
                $record['ratioFailure'] = number_format((100 * $r["times"] / $timesFailure), 2) . "%";
                $record['ratioTotal'] = number_format((100 * $r["times"] / $timesTotal), 2) . "%";
                array_push($items, $record);
            }

            $result["records"] = $items;
        } else {
            $result['message'] = '没有记录';
        }

        return $result;

    }//end getTableData()


    /**
     * 获取详情表头数据
     *
     * @return array
     */
    public function getDetailDataHeader()
    {
        $dbName = Input::get("db");
        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('CDR', $dbName);
        $table = Input::get('eventName');
        $result = array();
        $sql = "select * from $table limit 1";
        $rs = $db->query($sql, PDO::FETCH_ASSOC);
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

    }//end getDetailDataHeader()


    /**
     * 获取详情数据
     *
     * @return array
     */
    public function getDetailData()
    {
        $page = Input::get('page', 1);
        $rows = Input::get('limit', 10);
        $offset = (($page - 1) * $rows);
        $limit = " limit $offset,$rows";
        $dbName = input::get("db");
        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('CDR', $dbName);
        $table = Input::get('eventName');
        $date = Input::get("date");
        $result = array();
        $array = Input::get("result");
        $causeCode = $array[0];
        $subCauseCode = $array[1];
        $filter = " where date_id ='" . $date . "' and causeCode = '" . $causeCode . "' and subCauseCode = '" . $subCauseCode . "' ";

        $sql = "select count(*) totalCount from $table $filter";
        $rs = $db->query($sql);
        $row = $rs->fetchAll(PDO::FETCH_ASSOC);
        $result["total"] = $row[0]['totalCount'];

        $sql = "select * from $table $filter $limit";
        $res = $db->query($sql);
        if ($res) {
            $row = $res->fetchAll(PDO::FETCH_ASSOC);
            if (count($row) == 0) {
                $result['error'] = 'error';
                return $result;
            }

            $result['records'] = $row;
        }

        return $result;

    }//end getDetailData()


    /**
     * 详情导出
     *
     * @return string
     */
    public function exportFile()
    {
        $dbName = Input::get("db");
        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('CDR', $dbName);
        $table = Input::get('eventName');
        $date = Input::get("date");
        $result = array();
        $array = explode(',', Input::get("result"));

        $causeCode = $array[0];
        $subCauseCode = $array[1];
        $filter = " where date_id ='" . $date . "' and causeCode = '" . $causeCode . "' and subCauseCode = '" . $subCauseCode . "' ";
        $filename = "files/" . $causeCode . "_" . $subCauseCode . "_" . $table . "_" . date('YmdHis') . ".csv";
        $sql = "select * from $table limit 1";
        $rs = $db->query($sql, PDO::FETCH_ASSOC);
        if ($rs) {
            $rows = $rs->fetchall();
            $keys = array_keys($rows[0]);
        } else {
            $result['error'] = 'error';
            return json_encode($result);
        }

        $text = '';
        foreach ($keys as $key) {
            if ($key == 'id') {
                continue;
            }

            $text .= $key . ',';
        }

        $text = substr($text, 0, (strlen($text) - 1));
        $result['text'] = $text;

        $sql = "select * from $table $filter";
        $res = $db->query($sql);
        $row = $res->fetchAll(PDO::FETCH_ASSOC);

        $result['rows'] = $row;
        $result['total'] = count($row);
        $result['result'] = 'true';
        $this->resultToCSV2($result, $filename);
        $result['filename'] = $filename;
        return json_encode($result);

    }//end exportFile()


    /**
     * 写入CSV文件
     *
     * @param array $result 导出数据
     * @param string $filename CSV文件名
     *
     * @return void
     */
    protected function resultToCSV2($result, $filename)
    {
        $csvContent = mb_convert_encoding($result['text'] . "\n", 'GBK');
        $fp = fopen($filename, "w");
        fwrite($fp, $csvContent);
        foreach ($result['rows'] as $row) {
            fputcsv($fp, $row);
        }

        fclose($fp);

    }//end resultToCSV2()


    /**
     * 获取各个信令的成功率
     *
     * @return array
     */
    function getSolidgaugeData()
    {
        $dbName = Input::get("db");
        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('CDR', $dbName);

        $date = input::get("date");
        $result = array();
        $sql = "select eventName,sum(timesFailure) timesFailure,sum(timesTotal) timesTotal from (
        select eventName,sum(timesFailure) timesFailure,timesTotal,database() from causeCode where date_id='" . $date . "'  GROUP BY eventName,database()
        )tt GROUP BY tt.eventName;";
        $rs = $db->query($sql, PDO::FETCH_ASSOC);
        $data = array();
        if ($rs) {
            foreach ($rs as $row) {
                $timesSuccess = ($row['timesTotal'] - $row['timesFailure']);
                $data[$row['eventName']] = number_format((100 * $timesSuccess / $row['timesTotal']), 2);
            }

            $result['flag'] = 'true';
            $result['series'] = $data;
        } else {
            $result['flag'] = 'error';
        }

        return $result;

    }//end getSolidgaugeData()


}//end class
