<?php

/**
 * AreaCoverageController.php
 *
 * @category Controllers
 * @package  App\Http\Controllers
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
namespace App\Http\Controllers;

use App\Http\Controllers\Common\DataBaseConnection;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Common\MyRedis;
use PDO;

/**
 * 越区覆盖分析
 * Class AreaCoverageController
 *
 * @category Controllers
 * @package  App\Http\Controllers
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class AreaCoverageController extends MyRedis
{


    /**
     * 获得数据表头
     *
     * @return array
     */
    public function getAreaCoverageDataHeader()
    {
        $dateTime = Input::get('dateTime');
        $dbname   = $this->getMRDatabase(Input::get('dataBase'));
        $dbc      = new DataBaseConnection();
        $db       = $dbc->getDB('MR', $dbname);
        $table    = 'mroOverCoverageContribution_day';
        $result   = array();
        $sql      = "select dateId,ecgiNc,mr_LteNcEarfcn,mr_LteNcPci, sum(intensity) as intensity from $table WHERE dateId = '".$dateTime."' GROUP BY ecgiNc,mr_LteNcEarfcn,mr_LteNcPci limit 1";
        $rs       = $db->query($sql, PDO::FETCH_ASSOC);
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

    }//end getAreaCoverageDataHeader()


    /**
     * 获得MR数据库名
     *
     * @param string $city 城市名
     *
     * @return string
     */
    public function getMRDatabase($city)
    {
        $dbc = new DataBaseConnection();
        return $dbc->getMRDatabase($city);

    }//end getMRDatabase()


    /**
     * 获得越区覆盖数据
     *
     * @return string 当页数据
     */
    public function getAreaCoverageData()
    {
        $page     = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $rows     = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 50;
        $offset   = (($page - 1) * $rows);
        $limit    = " limit $offset,$rows";
        $dbname   = $this->getMRDatabase(Input::get('dataBase'));
        $dateTime = Input::get('dateTime');

        $result = array();
        $dbc    = new DataBaseConnection();
        $db     = $dbc->getDB('MR', $dbname);
        $table  = 'mroOverCoverageContribution_day';
        $sql    = "select dateId,ecgiNc,mr_LteNcEarfcn,mr_LteNcPci, sum(intensity) from ".$table." WHERE dateId = '".$dateTime."' GROUP BY ecgiNc,mr_LteNcEarfcn,mr_LteNcPci ";
        $res    = $db->query($sql);
        $row    = $res->fetchAll(PDO::FETCH_ASSOC);
        if (count($row) == 0) {
            $result['error'] = 'error';
            return json_encode($result);
        }

        $result["total"] = count($row);

        $sql = "select * from $table limit 1";
        $rs  = $db->query($sql, PDO::FETCH_ASSOC);
        if ($rs) {
        } else {
            $result['error'] = 'error';
            return json_encode($result);
        }

        $sql = "select dateId,ecgiNc,mr_LteNcEarfcn,mr_LteNcPci, sum(intensity) as intensity from ".$table." WHERE dateId = '".$dateTime."' GROUP BY ecgiNc,mr_LteNcEarfcn,mr_LteNcPci ".$limit;
        $res = $db->query($sql);
        $row = $res->fetchAll(PDO::FETCH_ASSOC);
        if (count($row) == 0) {
            $result['error'] = 'error';
            return json_encode($result);
        }

        $items = array();
        foreach ($row as $qr) {
            array_push($items, $qr);
        }

        $result['records'] = $items;

        echo json_encode($result);

    }//end getAreaCoverageData()

    /**
     * 导出越区覆盖数据
     *
     * @return string
     * 导出结果
     */
    public function getAllAreaCoverageData()
    {
        $dbname   = $this->getMRDatabase(Input::get('dataBase'));
        $dateTime = Input::get('dateTime');

        $dbc   = new DataBaseConnection();
        $db    = $dbc->getDB('MR', $dbname);
        $table = 'mroOverCoverageContribution_day';

        $sql = "select * from $table limit 1";
        $rs  = $db->query($sql, PDO::FETCH_ASSOC);
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

        $sql = "select dateId,ecgiNc,mr_LteNcEarfcn,mr_LteNcPci, sum(intensity) from ".$table." WHERE dateId = '".$dateTime."' GROUP BY ecgiNc,mr_LteNcEarfcn,mr_LteNcPci";
        $result['result'] = 'true';

        $filename = "files/".$dbname."_".$table."_".date('YmdHis').".csv";
        $this->resultToCSV2($result, $filename, $db, $sql);
        $result['filename'] = $filename;

        echo json_encode($result);

    }//end getAllAreaCoverageData()


    /**
     * 写入CSV文件
     *
     * @param array  $result   查询结果
     * @param string $filename CSV文件名
     * @param mixed  $db       数据库连接句柄
     * @param string $sql      SQL语句
     *
     * @return void
     */
    protected function resultToCSV2($result, $filename, $db, $sql)
    {
        $csvContent = mb_convert_encoding($result['text']."\n", 'GBK');
        $fp         = fopen($filename, "w");
        fwrite($fp, $csvContent);
        $stmt = $db->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
            fputcsv($fp, $row);
        }

        fclose($fp);

    }//end resultToCSV2()


    /**
     * 获得城市列表
     *
     * @return string
     */
    public function getAllCity()
    {
        $dbc = new DataBaseConnection();
        return $dbc->getCityOptions();

    }//end getAllCity()


    /**
     * 获得日期列表
     *
     * @return array
     */
    public function areaCoverageDate()
    {
        $city = Input::get('city');
        $city = $this->check_input($city);
        $dbname = $this->getMRDatabase($city);
        $dbc    = new DataBaseConnection();
        $db     = $dbc->getDB('MR', $dbname);
        $table  = 'mroOverCoverageContribution_day';
        $sql    = "select distinct dateId from $table";
        $this->type = $dbname.':areaCoverage';
        return $this->getValue($db, $sql);

    }//end areaCoverageDate()

    /**
     * 检查输入的字符串
     *
     * @param string $value 数据字串
     *
     * @return string
     */
    function check_input($value)
    {
        //去除斜杠
        if (get_magic_quotes_gpc()) {
            $value=stripslashes($value);
        }
        return $value;
    }


}//end class
