<?php

/**
 * ENBAnalysisController.php
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
use App\Models\CTR\CauseCode_day;

/**
 * ENB信令分析
 * Class ENBAnalysisController
 *
 * @category ComplaintHandling
 * @package  App\Http\Controllers\ComplaintHandling
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class ENBAnalysisController extends MyRedis
{


    /**
     * 获取城市列表
     *
     * @return void
     */
    public function getCitys()
    {
        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('CTR');
        $sql = "show dataBases";
        $res = $db->query($sql);
        $items = array();
        $row = $res->fetchAll(PDO::FETCH_ASSOC);
        foreach ($row as $r) {
            if ($r['DATABASE'] != 'Global') {
                $CHCity = $dbc->getCHAndCtrName($r['DATABASE']);
                array_push($items, $CHCity);
            }
        }

        echo json_encode($items);

    }//end getCitys()


    /**
     * 获取数据日期(天)列表
     *
     * @return void
     */
    public function getENBAnalysisDate()
    {
        $dbName = Input::get("dataBase");
        $dbName = $this->check_input($dbName);
        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('CTR', $dbName);

        $sql = "select distinct date_id from eventSuccess where date_id !='0000-00-00' ";
        $this->type = $dbName . ':ENBAnalysis';
        return json_encode($this->getValue($db, $sql));

    }//end getENBAnalysisDate()

    function check_input($value)
    {
        //去除斜杠
        if (get_magic_quotes_gpc()) {
            $value = stripslashes($value);
        }
        return $value;
    }

    /**
     * 获取一级图形数据
     *
     * @return array
     */
    public function getChartData()
    {
        $dbName = Input::get("db");
        $table = Input::get("table");
        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('CTR', $dbName);
        $date = Input::get("date");
        $result = array();
        $total = 0;

        if ($table == 'internalProcUeCtxtRelease') {
            $sql = "SELECT
                sum(times) AS num
            FROM
                causeCode_hour
            WHERE
                date_id = '$date'
            and
               eventName ='$table'
            and
                result  != 'Normal_Release'
            AND result  != 'User_Inactivity'
            AND result  != 'Successful_Handover'
            AND result  != 'Detach';";
        } else {
            $sql = "SELECT
                sum(times) AS num
            FROM
                causeCode_hour
            WHERE
                date_id = '$date'
            and
               eventName ='$table'
            and
                result != 'EVENT_VALUE_SUCCESSFUL'
            AND result != 'EVENT_VALUE_SUCCESS';";
        }//end if

        $rs = $db->query($sql);
        if ($rs) {
            $rows = $rs->fetchAll(PDO::FETCH_ASSOC);
            $total = intval($rows[0]['num']);
        } else {
            $result['flag'] = 'error';
        }

        if ($total != 0) {
            if ($table == 'internalProcUeCtxtRelease') {
                $sql = "SELECT
                    sum(times) AS num,
                    result
                FROM
                    causeCode_hour
                WHERE
                 date_id = '$date'
                and
                   eventName ='$table'
                and
                    result  != 'Normal_Release'
                AND result  != 'User_Inactivity'
                AND result  != 'Successful_Handover'
                AND result  != 'Detach'
                GROUP BY
                    result
                ORDER BY
                    num DESC";
            } else {
                $sql = "SELECT
                    count(*) AS num,
                    result
                FROM
                    causeCode_hour
                WHERE
                 date_id = '$date'
                and
                   eventName ='$table'
                and
                    result != 'EVENT_VALUE_SUCCESSFUL'
                AND result != 'EVENT_VALUE_SUCCESS'
                GROUP BY
                    result
                ORDER BY
                    num DESC";

            }//end if

            $rs = $db->query($sql);
            $series = array();
            $categories = array();
            $data = array();
            $items = array();
            if ($rs) {
                $rows = $rs->fetchAll(PDO::FETCH_ASSOC);
                foreach ($rows as $row) {
                    array_push($categories, $row['result']);
                    array_push($data, floatval(number_format(($row['num'] / $total * 100), 2)));
                    array_push($items, $row);
                }

                $series['data'] = $data;
                $series['name'] = $table;
                $result['categories'] = $categories;
                $result['series'] = $series;
                $result['table'] = $items;
            } else {
                $result['flag'] = 'error';
            }
        } else {
            $result['flag'] = 'error';
        }//end if
        return $result;

    }//end getChartData()


    /**
     * 获取详情表头数据
     *
     * @return array
     */
    public function getdetailDataHeader()
    {
        $dbName = Input::get("db");
        $table = Input::get("table");
        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('CTR', $dbName);
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

    }//end getdetailDataHeader()


    /**
     * 获取详情数据
     *
     * @return string
     */
    public function getdetailData()
    {
        $page = Input::get('page', 1);
        $rows = Input::get('limit', 10);
        $offset = (($page - 1) * $rows);
        $limit = " limit $offset,$rows";
        $dbName = Input::get("db");
        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('CTR', $dbName);
        $table = Input::get('table');
        $date = Input::get("date");
        $result = array();
        $array = Input::get("result");

        if ($table != 'internalProcUeCtxtRelease') {
            $filter = " where date_id ='" . $date . "' and result = '" . $array . "'";
        } else {
            $filter = " where date_id ='" . $date . "' and 3gppCause = '" . $array . "'";
        }

        $sql = "select count(*) totalCount from $table $filter";
        $rs = $db->query($sql);
        $row = $rs->fetchAll(PDO::FETCH_ASSOC);
        $result["total"] = $row[0]['totalCount'];

        $sql = "select * from $table $filter $limit";
        $res = $db->query($sql);
        if ($res) {
            $rows = $res->fetchAll(PDO::FETCH_ASSOC);
            if (count($rows) == 0) {
                $result['error'] = 'error';
                return json_encode($result);
            }

            $items = array();
            foreach ($rows as $row) {
                array_push($items, $row);
            }

            $result['records'] = $items;
        }

        return json_encode($result);

    }//end getdetailData()


    /**
     * 详情导出
     *
     * @return string
     */
    public function exportFile()
    {
        $dbName = Input::get("db");
        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('CTR', $dbName);
        $table = Input::get('table');
        $date = Input::get("date");
        $result = array();
        $array = Input::get("result");

        if ($table != 'internalProcUeCtxtRelease') {
            $filter = " where date_id ='" . $date . "' and result = '" . $array . "'";
        } else {
            $filter = " where date_id ='" . $date . "' and 3gppCause = '" . $array . "'";
        }

        $filename = "files/" . $array . "_" . $table . "_" . date('YmdHis') . ".csv";
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
     * 获取success图形数据
     *
     * @return string
     */
    public function getSuccessChartData()
    {
        $dbName = Input::get("db");
        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('CTR', $dbName);
        $date = Input::get("date");
        $items = array();
        $sql = "SELECT
                    SUM(total_count) AS total,
                    SUM(success_count) AS success,
                    table_name
                FROM
                    eventSuccess
                WHERE
                    date_id = '$date'
                GROUP BY
                    table_name
                ORDER BY
                    table_name";
        $rs = $db->query($sql);
        if ($rs) {
            $rows = $rs->fetchAll(PDO::FETCH_ASSOC);
            if (count($rows) != 0) {
                foreach ($rows as $row) {
                    $total = intval($row['total']);
                    $success = intval($row['success']);
                    if ($total == 0) {
                        $items[$row['table_name']] = 0;
                    } else {
                        $items[$row['table_name']] = floatval(number_format(($success / $total * 100), 2));
                    }
                }
                return json_encode($items);
            }
            return "error";
        }
        return "error";
    }//end getSuccessChartData()

}//end class
