<?php
/**
 * QueryAnalysis.php
 *
 * @category QueryAnalysis
 * @package  App\Http\Controllers\QueryAnalysis
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
namespace App\Http\Controllers\QueryAnalysis;

use App\Http\Controllers\Common\DataBaseConnection;
use App\Http\Controllers\Utils\FileUtil;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Common\MyRedis;
use PDO;
use App\Models\MR\MroWeakCoverage_hour;

/**
 * MRORSRP查询
 * Class MRORSRPQueryController
 *
 * @category QueryAnalysis
 * @package  App\Http\Controllers\QueryAnalysis
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 * @package App\Http\Controllers\QueryAnalysis
 */
class MRORSRPQueryController extends MyRedis
{


    /**
     * 获得MRORSRP分析结果字段
     *
     * @return array MRORSRP分析结果字段
     */
    public function getMRORSRPDataField()
    {
        $dateTime = Input::get('dateTime');
        $dbname = $this->getMRDatabase(Input::get('select'));
        $result = array();
        $rows = MroWeakCoverage_hour::on($dbname)->where('date', $dateTime)->exists();
        if ($rows) {
            $rs = MroWeakCoverage_hour::on($dbname)->where('date', $dateTime)->first()->toArray();
            $result["field"] = array_keys($rs);
            return $result;
        } else {
            $result['error'] = 'error';
            return $result;
        }
    }//end getMRORSRPDataField()

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
     * 获得MRORSRP分析结果
     *
     * @return string MRORSRP分析结果
     */
    public function getMRORSRPDataSplit()
    {
        $city = input::get("select");
        $dataType = Input::get('dataType');
        $limit = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 50;
       
        $dbname = $this->getMRDatabase(Input::get('select'));
        $dateTime = Input::get('dateTime');
        $return = array();

        $rows = MroWeakCoverage_hour::on($dbname)->where('date', $dateTime)->paginate($limit)->toArray();;
        $return["total"] = $rows['total'];
        $return['records'] = $rows['data'];
        return json_encode($return);
    }//end getMRORSRPDataSplit()

    /**
     * 导出MRORSRP数据
     *
     * @return array MRORSR数据
     */
    public function getAllData()
    {
        $dbname = $this->getMRDatabase(Input::get('select'));
        $dateTime = Input::get('dateTime');
        $result = array();
        $return = array();
        $rs = MroWeakCoverage_hour::on($dbname)->where('date', $dateTime)->exists();
        if ($rs) {
            $rs = MroWeakCoverage_hour::on($dbname)->where('date', $dateTime)->first()->toArray();
            $fileName = "common/files/MRORARPQuery_".$dateTime."_" . date('YmdHis') . ".csv";
            $column = implode(array_keys($rs), ',');
            $items = MroWeakCoverage_hour::on($dbname)->where('date', $dateTime)->get()->toArray();
            $fileUtil = new FileUtil();
            $fileUtil->resultToCSV2($column, $items, $fileName);
            $result['fileName'] = $fileName;
            $result['result'] = 'true';
        } else {
            $result['error'] = 'error';
            return $result;
        }
        
        return $result;
    }//end getAllData()



    /**
     * 获取城市列表
     *
     * @return string 城市列表(JSON)
     */
    public function getAllCity()
    {
        $dbc = new DataBaseConnection();
        return $dbc->getCityOptions();
    }//end resultToCSV2_All()

    function check_input($value)
    {
        //去除斜杠
        if (get_magic_quotes_gpc())
        {
            $value=stripslashes($value);
        }
        return $value;
    }

    /**
     * 获得日期列表(天)
     *
     * @return array 日期列表(天)
     */
    public function getDate()
    {
        $city = Input::get('city');
        $city = $this->check_input($city);
        $dbname = $this->getMRDatabase($city);
        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('MR', $dbname);
        $table = 'mroWeakCoverage_hour';
        $sql = "select distinct date from $table";
        
        $this->type = $dbname . ':MRORSRPQuery:' . $table;
        return $this->getValue($db, $sql);

    }//end getGSMNeighDataAll()

   
}//end class

