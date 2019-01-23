<?php

/**
 * A5ThresholdAnalysisController.php
 *
 * @category NetworkOptimization
 * @package  App\Http\Controllers\NetworkOptimization
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
namespace App\Http\Controllers\NetworkOptimization;

use App\Http\Controllers\Common\DataBaseConnection;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Common\MyRedis;
use PDO;
use App\Models\MR\MreA5Threshold;

/**
 * A5门限分析
 * Class A5ThresholdAnalysisController
 *
 * @category NetworkOptimization
 * @package  App\Http\Controllers\NetworkOptimization
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class A5ThresholdAnalysisController extends MyRedis
{


    /**
     * 获得A5分析结果表头
     *
     * @return array A5分析结果表头
     */
    public function getMreA5ThresholdDataHeader()
    {
        $dbname   = $this->getMRDatabase(Input::get('dataBase'));
        $dateTime = Input::get('dateTime');
        $result   = array();
        $rs = MreA5Threshold::on($dbname)->where('datetime_id', 'like', $dateTime."%");
        if ($rs->exists()) {
            if ($rs->count() > 0) {
                return $rs->first()->toArray();
            } else {
                $result['error'] = 'error';
                return $result;
            }
        } else {
            $result['error'] = 'error';
            return $result;
        }

    }//end getMreA5ThresholdDataHeader()


    /**
     * 获得MR数据库名
     *
     * @param string $city 城市名
     *
     * @return string 数据库名
     */
    public function getMRDatabase($city)
    {
        $dbc = new DataBaseConnection();
        return $dbc->getMRDatabase($city);

    }//end getMRDatabase()


    /**
     * 获得A5检查结果(分页)
     *
     * @return string
     */
    public function getMreA5ThresholdData()
    {
        // $page   = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $limit   = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 50;
        $dbname   = $this->getMRDatabase(Input::get('dataBase'));
        $dateTime = Input::get('dateTime');
        $result   = array();

        $rows = MreA5Threshold::on($dbname)->where('datetime_id', 'like', $dateTime."%")->paginate($limit)->toArray();      
        $result["total"] = $rows['total'];
        $result['records'] = $rows['data'];
        return json_encode($result);

    }//end getMreA5ThresholdData()


    /**
     * 导出全量A5分析结果
     *
     * @return string CSV文件名
     */
    public function getAllMreA5ThresholdData()
    {
        $dbname   = $this->getMRDatabase(Input::get('dataBase'));
        $dateTime = Input::get('dateTime');
        if (MreA5Threshold::on($dbname)->exists()) {
            $keys = ['datetime_id','ecgi','mr_LteNcEarfcn','mr_LteScRSRQ_No90_percent','mr_LteNcRSRQ_No90_percent','comments'];
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

        $rows = MreA5Threshold::on($dbname)->where('datetime_id', 'like', $dateTime."%")->get($keys)->toArray();

        $csvContent = mb_convert_encoding($result['text'] . "\n", 'gb2312', 'utf-8');
        $filename = "common/files/".$dbname."_MreA5Threshold_".date('YmdHis').".csv";
        $fp = fopen($filename, "w");
        fwrite($fp, $csvContent);
        foreach ($rows as $row) {
            fputcsv($fp, $row);
        }
        fclose($fp);

        $result['filename'] = $filename;
        $result['result'] = 'true';

        return json_encode($result);

    }//end getAllMreA5ThresholdData()


    /**
     * 写入CSV文件
     *
     * @param array  $result   查询结果
     * 
     * @param string $filename 文件名
     * 
     * @param mixed  $db       数据库连接句柄
     * 
     * @param string $sql      SQL字串
     * 
     * @param string $dateTime 日期
     *
     * @return void
     */
    protected function resultToCSV2All($result, $filename, $db, $sql, $dateTime)
    {
        $csvContent = mb_convert_encoding($result['text']."\n", 'GBK');
        $fp         = fopen($filename, "w");
        fwrite($fp, $csvContent);
        $stmt = $db->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL));
        $stmt->bindValue(1, $dateTime.'%');
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) {
            fputcsv($fp, $row);
        }

        fclose($fp);

    }//end resultToCSV2All()


    /**
     * 获取城市列表
     *
     * @return string
     */
    public function getAllCity()
    {
        $dbc = new DataBaseConnection();
        return $dbc->getCityOptions();

    }//end getAllCity()


    /**
     * 获得日期列表(天)
     *
     * @return array 日期列表
     */
    public function getfdfdf()
    {
        $city = Input::get('city');
        $city = $this->check_input($city);
        $dbname = $this->getMRDatabase($city);
        $dbc    = new DataBaseConnection();
        $db     = $dbc->getDB('MR', $dbname);
        $table  = 'mreA5Threshold';
        $sql    = "select distinct datetime_id from $table";
        $this->type = $dbname.':A5ThresholdAnalysis';
        return $this->getValue($db, $sql);

    }//end getfdfdf()
    function check_input($value)
    {
        //去除斜杠
        if (get_magic_quotes_gpc()) {
            $value=stripslashes($value);
        }
        return $value;
    }

}//end class
