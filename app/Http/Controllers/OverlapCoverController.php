<?php

/**
 * OverlapCoverController.php
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
use App\Http\Controllers\Utils\DateUtil;
use PDO;
use App\Models\MR\MroOverCoverage_day;

/**
 * 重叠覆盖分析
 *
 * @category Controllers
 * @package  App\Http\Controllers
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class OverlapCoverController extends Controller
{


    /**
     * 获得数据头
     *
     * @return array
     */
    public function getOverlapCoverDataHeader()
    {
        $dateTime = Input::get('dateTime');
        $dbname   = $this->getMRDatabase(Input::get('dataBase'));
        $result   = array();
        $conn = MroOverCoverage_day::on($dbname)->where('dateId', $dateTime);
        if ($conn->exists()) {
            $row = $conn->first()->toArray();

            $date['dateId']=$row['dateId'];
            $date['ecgi']=$row['ecgi'];
            $date['cellName']='';
            $date['siteName']='';
            unset($row['dateId']);
            unset($row['ecgi']);
            $date_rows=array_merge($date, $row);
            return $date_rows;
        } else {
            $result['error'] = 'error';
            return $result;
        }

    }//end getOverlapCoverDataHeader()


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
     * 获得重叠覆盖数据(分页)
     *
     * @return string
     */
    public function getOverlapCoverData()
    {
        $page      = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $limit      = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 50;
        $dbname    = $this->getMRDatabase(Input::get('dataBase'));
        $dateTime  = Input::get('dateTime');
        $sortBy    = Input::get('sortBy')?Input::get('sortBy'):'rate';
        $direction = Input::get('direction')?Input::get('direction'):'desc';
        $rows = MroOverCoverage_day::on($dbname)->selectRaw('mroOverCoverage_day.*,cellName,siteName')
                ->where('dateId', $dateTime)
                ->leftJoin('GLOBAL.siteLte', 'siteLte.ecgi', '=', 'mroOverCoverage_day.ecgi')
                    ->orderBy($sortBy, $direction)
                    ->paginate($limit)
                    ->toArray();

        $result    = array();
        $result["total"] = $rows['total'];
        $result['records'] = $rows['data'];

        echo json_encode($result);

    }//end getOverlapCoverData()


    /**
     * 导出全量重叠覆盖数据
     *
     * @return void
     */
    public function getAllOverlapCoverData()
    {
        $dbname   = $this->getMRDatabase(Input::get('dataBase'));
        $dateTime = Input::get('dateTime');
        $text = 'dateId,ecgi,cellName,siteName,sample,all_sample,rate,intensity';
        $filename = "files/".$dbname."_mroOverCoverage_day_".date('YmdHis').".csv";
        $rows = MroOverCoverage_day::on($dbname)->selectRaw('dateId,mroOverCoverage_day.ecgi,cellName,siteName,sample,all_sample,rate,intensity')
                ->where('dateId', $dateTime)
                ->leftJoin('GLOBAL.siteLte', 'siteLte.ecgi', '=', 'mroOverCoverage_day.ecgi')
                ->get()
                ->toArray();

        $csvContent = mb_convert_encoding($text."\n", 'GBK');
        $fp         = fopen($filename, "w");
        fwrite($fp, $csvContent);
        foreach ($rows as $row) {
            fputcsv($fp, $row);
        }
        fclose($fp);
        
        $result = array();
        $result['result'] = 'true';
        $result['filename'] = $filename;
        echo json_encode($result);

    }//end getAllOverlapCoverData()


    /**
     * 写入CSV
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



        // $stmt = $db->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        // $stmt->execute();
        // while ($row = $stmt->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
        //     fputcsv($fp, $row);
        // }

        $res     = $db->query($sql);
        $dbc     = new DataBaseConnection();
        $dbs     = $dbc->getDB('mongs', 'mongs');
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $sqls="select cellName,siteName from siteLte where ecgi='".$row['ecgi']."'";
            $rss    = $dbs->query($sqls, PDO::FETCH_ASSOC);
            if ($rss) {
                $rowss=array();
                $rowss = $rss->fetchall();
                $date['dateId']=$row['dateId'];
                $date['ecgi']=$row['ecgi'];
                $row= array_splice($row, 2);           
                $date['cellName']=isset($rowss[0]['cellName'])?$rowss[0]['cellName']:'';
                $date['siteName']=isset($rowss[0]['siteName'])?$rowss[0]['siteName']:'';             
                $row=array_merge($date, $row);
            }  
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
    public function overlapCoverDate()
    {
        $city = Input::get('city');
        $city = $this->check_input($city);
        $dbname = $this->getMRDatabase($city);
        $dbc    = new DataBaseConnection();
        $db     = $dbc->getDB('MR', $dbname);
        $table  = 'mroOverCoverage_day';
        $result = array();
        $sql    = "select distinct dateId from $table";
        $key = 'date:'.$city.':'.$table;
        $dateUtil = new DateUtil();
        return $dateUtil->getDateListWithData($db, $key, $sql);

    }//end overlapCoverDate()


    /**
     * 检查数据
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

    /**
     * 获得GENIUS标准表头
     *
     * @return array
     */
    public function overlapCoverGeniusDataHeader()
    {
        $dateTime = Input::get('dateTime');
        $dbname   = $this->getMRDatabase(Input::get('dataBase'));
        $dbc      = new DataBaseConnection();
        $db       = $dbc->getDB('MR', $dbname);
        $table    = 'mroOverCoverageInternal';
        $result   = array();
        $sql      = "select * from $table WHERE datetime_id = '".$dateTime." 08:00:00' limit 1";
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

    }//end overlapCoverGeniusDataHeader()


    /**
     * 获得GENIUS标准重叠覆盖数据
     *
     * @return string
     */
    public function overlapCoverGeniusData()
    {
        $page      = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $rows      = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 50;
        $offset    = (($page - 1) * $rows);
        $limit     = " limit $offset,$rows";
        $dbname    = $this->getMRDatabase(Input::get('dataBase'));
        $dateTime  = Input::get('dateTime');
        $sortBy    = isset($_REQUEST['sortBy']) ? $_REQUEST['sortBy'] : "intensity";
        $direction = isset($_REQUEST['direction']) ? $_REQUEST['direction'] : 'desc';
        $order     = " order by $sortBy $direction ";

        $result = array();
        $dbc    = new DataBaseConnection();
        $db     = $dbc->getDB('MR', $dbname);
        $table  = 'mroOverCoverageInternal';

        $rs  = $db->query("select count(*) totalCount from ".$table." WHERE datetime_id = '".$dateTime." 08:00:00'");
        $row = $rs->fetchAll(PDO::FETCH_ASSOC);

        $result["total"] = $row[0]['totalCount'];

        $sql = "select * from ".$table." WHERE datetime_id = '".$dateTime." 08:00:00'$order ".$limit;
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

    }//end overlapCoverGeniusData()


    /**
     * 导出全量重叠覆盖数据
     *
     * @return string
     */
    public function allOverlapCoverGeniusData()
    {
        $dbname   = $this->getMRDatabase(Input::get('dataBase'));
        $dateTime = Input::get('dateTime');

        $dbc   = new DataBaseConnection();
        $db    = $dbc->getDB('MR', $dbname);
        $table = 'mroOverCoverageInternal';

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

        $sql = "select datetime_id,ecgi,intensity from ".$table." WHERE datetime_id = '".$dateTime." 08:00:00'";
        $result['result'] = 'true';

        $filename = "files/".$dbname."_".$table."_".date('YmdHis').".csv";
        $this->resultToCSV2($result, $filename, $db, $sql);
        $result['filename'] = $filename;
        echo json_encode($result);

    }//end allOverlapCoverGeniusData()


    /**
     * 获得日期列表(GENIUS标准)
     *
     * @return array
     */
    public function overlapCoverGeniusDate()
    {
        $dbname = $this->getMRDatabase(Input::get('city'));
        $dbc    = new DataBaseConnection();
        $db     = $dbc->getDB('MR', $dbname);
        $table  = 'mroOverCoverageInternal';
        $result = array();
        $sql    = "select distinct datetime_id from $table";
        $rs     = $db->query($sql, PDO::FETCH_ASSOC);
        $test   = [];
        if ($rs) {
            $rows = $rs->fetchall();
            if (count($rows) > 0) {
                foreach ($rows as $row) {
                    $arr = explode(' ', $row['datetime_id']);
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

    }//end overlapCoverGeniusDate()


}//end class
