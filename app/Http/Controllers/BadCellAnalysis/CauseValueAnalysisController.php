<?php

/**
 * CauseValueAnalysisController.php
 *
 * @category BadCellAnalysis
 * @package  App\Http\Controllers\BadCellAnalysis
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
namespace App\Http\Controllers\BadCellAnalysis;

use App\Http\Controllers\Common\DataBaseConnection;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Common\MyRedis;
use PDO;

/**
 * 原因值分析
 * Class CauseValueAnalysisController
 *
 * @category BadCellAnalysis
 * @package  App\Http\Controllers\BadCellAnalysis
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class CauseValueAnalysisController extends MyRedis
{

    /**
     * 获取城市列表
     *
     * @return string 城市列表
     */
    public function getCitys()
    {
        $dbc   = new DataBaseConnection();
        $db    = $dbc->getDB('CDR');
        $sql   = "show dataBases";
        $res   = $db->query($sql);
        $items = array();
        $row   = $res->fetchAll(PDO::FETCH_ASSOC);
        foreach ($row as $r) {
            if ($r['DATABASE'] != 'Global') {
                $CHCity = $dbc->getCDRToCHName($r['DATABASE']);
                array_push($items, $CHCity."-".$r['DATABASE']);
            }
        }

        echo json_encode($items);

    }//end getCitys()


    /**
     * 获取原因值日期(天)
     *
     * @return string 原因值日期(天)
     */
    public function getCauseValueAnalysisData()
    {
        $dbname = input::get("dataBase");
        $dbc    = new DataBaseConnection();
        $db     = $dbc->getDB('CDR', $dbname);
        $table = 'causeCodeEsrvcc';
        $sql   = "select distinct date_id from causeCodeEsrvcc";
        $this->type = $dbname.':CauseValueAnalysis';
        return $this->getValue($db, $sql);
    }//end getCauseValueAnalysisData()


    /**
     * 获取一级图形数据
     *
     * @return array 原因值一级图表数据
     */
    public function getChartData()
    {
        $dbname = input::get("db");
        $dbc    = new DataBaseConnection();
        $db     = $dbc->getDB('CDR', $dbname);

        $table = 'causeCodeEsrvcc';
        $date  = input::get("date");
        $rs    = $db->query("select sum(timesFailure) timesFailure,sum(timesTotal) timesTotal from (select timesFailure,timesTotal from $table where date_id ='".$date."' GROUP BY timesFailure,timesTotal)t;");
        if ($rs) {
            $row          = $rs->fetchAll(PDO::FETCH_ASSOC);
            $timesFailure = $row[0]['timesFailure'];
        } else {
            $result['flag'] = 'error';
        }

        $sql        = "select causeCode,subCauseCode,sum(times) times from $table 
where date_id='".$date."' and causeCode !='SUCCESSFUL_HANDOVER' GROUP BY causeCode,subCauseCode order by times desc";
        $rs         = $db->query($sql);
        $series     = array();
        $categories = array();
        $data       = array();
        $result     = array();
        if ($rs) {
            $rows = $rs->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                array_push($categories, $row['causeCode']."/".$row['subCauseCode']);
                array_push($data, floatval(number_format(($row['times'] / $timesFailure * 100), 2)));
            }

            $series['data']       = $data;
            $series['name']       = 'causeCode/subCauseCode';
            $result['categories'] = $categories;
            $result['series']     = $series;
            $result['flag']       = 'true';
        } else {
            $result['flag'] = 'error';
        }

        return $result;

    }//end getChartData()


    /**
     * 二级钻取图形数据
     *
     * @return array
     */
    public function getDrillDownChartData()
    {
        $dbname = input::get("db");
        $dbc    = new DataBaseConnection();
        $db     = $dbc->getDB('CDR', $dbname);

        $table      = 'causeCodeEsrvcc';
        $causeCode  = Input::get('causeCode');
        $date       = input::get("date");
        $rs         = $db->query("select sum(times) as occurs,subCauseCode as type from $table where date_id = '".$date."' and causeCode='".$causeCode."' group by subCauseCode;");
        $categories = array();
        $series     = array();
        $data       = array();
        $result     = array();
        if ($rs) {
            $rows = $rs->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                array_push($categories, $row['type']);
                array_push($data, floatval($row['occurs']));
            }

            $series['name']       = 'subCauseCode';
            $series['data']       = $data;
            $result['categories'] = $categories;
            $result['series']     = $series;
            $result['flag']       = 'true';
        } else {
            $result['flag'] = 'error';
        }

        return $result;

    }//end getDrillDownChartData()


    /**
     * 获得原因值表格数据
     *
     * @return array 原因值表格数据
     */
    public function getTableData()
    {
        $page   = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $rows   = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 50;
        $offset = (($page - 1) * $rows);
        $limit  = " limit $offset,$rows";

        $dbname = input::get("db");
        $dbc    = new DataBaseConnection();
        $db     = $dbc->getDB('CDR', $dbname);

        $table  = 'causeCodeEsrvcc';
        $date   = input::get("date");
        $result = array();
        $rs     = $db->query("select causeCode,subCauseCode from $table where date_id='".$date."' and causeCode !='SUCCESSFUL_HANDOVER'  GROUP BY causeCode,subCauseCode order by causeCode,subCauseCode desc");
        if ($rs) {
            $row = $rs->fetchAll(PDO::FETCH_ASSOC);
            $result["total"] = count($row);
        } else {
            $result['message'] = '没有记录';
        }

        $timesFailure = '';
        $timesTotal   = '';
        $rs           = $db->query("select sum(timesFailure) timesFailure,sum(timesTotal) timesTotal from (select timesFailure,timesTotal from $table where date_id ='".$date."' GROUP BY timesFailure,timesTotal)t;");
        if ($rs) {
            $row          = $rs->fetchAll(PDO::FETCH_ASSOC);
            $timesFailure = $row[0]['timesFailure'];
            $timesTotal   = $row[0]['timesTotal'];
        } else {
            $result['message'] = '没有记录';
        }

        $sql   = "select date_id,causeCode,subCauseCode,sum(times) times from $table where date_id='".$date."' and causeCode !='SUCCESSFUL_HANDOVER' GROUP BY causeCode,subCauseCode order by times desc $limit;";
        $rs    = $db->query($sql);
        $items = array();
        if ($rs) {
            $row = $rs->fetchAll(PDO::FETCH_ASSOC);
            foreach ($row as $r) {
                $record["causeCode"]    = $r['causeCode'];
                $record["subCauseCode"] = $r['subCauseCode'];
                $record["times"]        = $r["times"];
                $record["timesFailure"] = $timesFailure;
                $record["timesTotal"]   = $timesTotal;
                $record['ratioFailure'] = number_format((100 * $r["times"] / $timesFailure), 2)."%";
                $record['ratioTotal']   = number_format((100 * $r["times"] / $timesTotal), 2)."%";
                array_push($items, $record);
            }

            $result["records"] = $items;
        } else {
            $result['message'] = '没有记录';
        }

        return $result;

    }//end getTableData()


    /**
     * 获得详细数据表头
     *
     * @return array
     */
    public function getDetailDataHeader()
    {
        $dbname = input::get("db");
        $dbc    = new DataBaseConnection();
        $db     = $dbc->getDB('CDR', $dbname);
        $table  = 'L_HANDOVER';
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

    }//end getDetailDataHeader()


    /**
     * 获得原因码
     *
     * @return array 原因码
     */
    public function getDetailData()
    {
        $page         = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $rows         = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 10;
        $offset       = (($page - 1) * $rows);
        $limit        = " limit $offset,$rows";
        $dbname       = input::get("db");
        $dbc          = new DataBaseConnection();
        $db           = $dbc->getDB('CDR', $dbname);
        $table        = 'L_HANDOVER';
        $date         = input::get("date");
        $result       = array();
        $array        = input::get("result");
        $causeCode    = $array[0];
        $subCauseCode = $array[1];
        $filter       = " where date_id ='".$date."' and srvcc_type = 'CS_ONLY' and causeCode = '".$causeCode."' and subCauseCode = '".$subCauseCode."' ";

        $sql = "select count(*) totalCount from $table $filter";
        $rs  = $db->query($sql);
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
        $dbname = input::get("db");
        $dbc    = new DataBaseConnection();
        $db     = $dbc->getDB('CDR', $dbname);
        $table  = 'L_HANDOVER';
        $date   = input::get("date");
        $result = array();
        $array  = explode(',', Input::get("result"));

        $causeCode    = $array[0];
        $subCauseCode = $array[1];
        $filter       = " where date_id ='".$date."' and srvcc_type = 'CS_ONLY' and causeCode = '".$causeCode."' and subCauseCode = '".$subCauseCode."' ";
        $filename     = "files/".$causeCode."_".$subCauseCode."_".$table."_".date('YmdHis').".csv";
        $sql          = "select * from $table limit 1";
        $rs           = $db->query($sql, PDO::FETCH_ASSOC);
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

            $text .= $key.',';
        }

        $text           = substr($text, 0, (strlen($text) - 1));
        $result['text'] = $text;

        $sql = "select * from $table $filter";
        $res = $db->query($sql);
        $row = $res->fetchAll(PDO::FETCH_ASSOC);

        $result['rows']   = $row;
        $result['total']  = count($row);
        $result['result'] = 'true';
        $this->resultToCSV2($result, $filename);
        $result['filename'] = $filename;
        echo json_encode($result);

    }//end exportFile()


    /**
     * 导出CSV文件
     *
     * @param array  $result   导出数据
     * @param string $filename 文件名
     *
     * @return void
     */
    protected function resultToCSV2($result, $filename)
    {
        $csvContent = mb_convert_encoding($result['text']."\n", 'GBK');
        $fp         = fopen($filename, "w");
        fwrite($fp, $csvContent);
        foreach ($result['rows'] as $row) {
            fputcsv($fp, $row);
        }

        fclose($fp);

    }//end resultToCSV2()


}//end class
