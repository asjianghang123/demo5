<?php

/**
 * A2ThresholdAnalysisController.php
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
use App\Models\MR\MreA2Threshold;

/**
 * A2门限分析
 * Class A2ThresholdAnalysisController
 *
 * @category NetworkOptimization
 * @package  App\Http\Controllers\NetworkOptimization
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class A2ThresholdAnalysisController extends MyRedis
{


    /**
     * 获得A2门限数据表头
     *
     * @return array A2门限数据表头
     */
    public function getMreA2ThresholdDataHeader()
    {
        $dateTime = Input::get('dateTime');
        $dbname   = $this->getMRDatabase(Input::get('dataBase'));
        $result   = array();
        $rs = MreA2Threshold::on($dbname)->where('datetime_id', 'like', $dateTime."%");
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

    }//end getMreA2ThresholdDataHeader()


    /**
     * 获得MR数据库名
     *
     * @param string $city 城市
     *
     * @return string MR数据库名
     */
    public function getMRDatabase($city)
    {
        $dbc = new DataBaseConnection();
        return $dbc->getMRDatabase($city);

    }//end getMRDatabase()


    /**
     * 获得A2门限检查结果(分页)
     *
     * @return mixed
     */
    public function getMreA2ThresholdData()
    {
        // $page     = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $limit     = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 50;
        $dbname   = $this->getMRDatabase(Input::get('dataBase'));
        $dateTime = Input::get('dateTime');

        $result = array();

        $rows = MreA2Threshold::on($dbname)->where('datetime_id', 'like', $dateTime."%")->paginate($limit)->toArray();      
        $result["total"] = $rows['total'];
        $result['records'] = $rows['data'];

        return json_encode($result);

    }//end getMreA2ThresholdData()


    /**
     * 导出全量A2门限检查结果
     *
     * @return string CSV文件名
     */
    public function getAllMreA2ThresholdData()
    {   
        $dataBase = Input::get('dataBase');
        $dataBase = $this->check_input($dataBase);
        $dbname   = $this->getMRDatabase($dataBase);
        $dateTime = Input::get('dateTime');

        if (MreA2Threshold::on($dbname)->exists()) {
            $keys = ['datetime_id','ecgi','city','mr_LteScRSRQ_No90_percent'];
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

        $rows = MreA2Threshold::on($dbname)->where('datetime_id', 'like', $dateTime."%")->get($keys)->toArray();

        $csvContent = mb_convert_encoding($result['text'] . "\n", 'gb2312', 'utf-8');
        $filename = "common/files/".$dbname."_MreA2Threshold_".date('YmdHis').".csv";
        $fp = fopen($filename, "w");
        fwrite($fp, $csvContent);
        foreach ($rows as $row) {
            fputcsv($fp, $row);
        }
        fclose($fp);

        $result['filename'] = $filename;
        $result['result'] = 'true';
        return json_encode($result);

    }//end getAllMreA2ThresholdData()


    /**
     * 写入CSV文件
     *
     * @param array  $result   文件头
     * @param string $filename CSV文件名
     * @param mixed  $db       数据库连接句柄
     * @param string $sql      SQL字串
     *
     * @return void
     */
    protected function resultToCSV2All($result, $filename, $db, $sql)
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
     * 获得日期(天)列表
     *
     * @return array 日期列表
     */
    public function getfdfde()
    {
        $city = Input::get('city');
        $city = $this->check_input($city);
        $dbname = $this->getMRDatabase($city);
        $dbc    = new DataBaseConnection();
        $db     = $dbc->getDB('MR', $dbname);
        $table  = 'mreA2Threshold';
        $sql    = "select distinct datetime_id from $table";
        $this->type = $dbname.':A2ThresholdAnalysis';
        return $this->getValue($db, $sql);

    }//end getfdfde()

    function check_input($value)
    {
        //去除斜杠
        if (get_magic_quotes_gpc()) {
            $value=stripslashes($value);
        }
        return $value;
    }
}//end class
