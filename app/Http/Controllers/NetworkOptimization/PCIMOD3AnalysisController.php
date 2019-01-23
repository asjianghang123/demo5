<?php

/**
 * PCIMOD3AnalysisController.php
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
use App\Models\MR\MroPciMod3_day;
use App\Models\MR\MroPciMod3Internal;

/**
 * PCI MOD3 分析
 * Class PCIMOD3AnalysisController
 *
 * @category NetworkOptimization
 * @package  App\Http\Controllers\NetworkOptimization
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class PCIMOD3AnalysisController extends MyRedis
{


    /**
     * 获得PCI模3检查结果表头
     *
     * @return array 检查结果表头
     */
    public function getMroPCIMOD3DataHeader()
    {
        $dateTime = Input::get('dateTime');
        $dbname   = $this->getMRDatabase(Input::get('dataBase'));
        $result   = array();

        $conn = MroPciMod3_day::on($dbname)->where('dateId', $dateTime);
        if ($conn->count()>0) {
            return $conn->first()->toArray();
        } else {
            $result['error'] = 'error';
            return $result;
        }
    }//end getMroPCIMOD3DataHeader()


    /**
     * 获得MR数据库名
     *
     * @param string $city 城市名
     *
     * @return string MR数据库名
     */
    public function getMRDatabase($city)
    {
        $dbc = new DataBaseConnection();
        return $dbc->getMRDatabase($city);

    }//end getMRDatabase()


    /**
     * 获得MOD3数据(分页)
     *
     * @return mixed
     */
    public function getMroPCIMOD3Data()
    {
        $page   = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $limit   = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 50;

        $dbname   = $this->getMRDatabase(Input::get('dataBase'));
        $dateTime = Input::get('dateTime');
        $result   = array();

        $rows = MroPciMod3_day::on($dbname)->where('dateId', $dateTime)->orderBy('userLabel', 'asc')->paginate($limit)->toArray();
        $result["total"] = $rows['total'];
        $result['records'] = $rows['data'];

        return json_encode($result);

    }//end getMroPCIMOD3Data()


    /**
     * 导出全量MOD3数据
     *
     * @return mixed
     */
    public function getAllMroPCIMOD3Data()
    {
        $dbname   = $this->getMRDatabase(Input::get('dataBase'));
        $dateTime = Input::get('dateTime');
        $result = array();
        $conn = MroPciMod3_day::on($dbname);
        if ($conn->exists()) {
            $text = "dateId,userLabel,ecgi,EutrancellTddName,mr_LteScEarfcn,mr_LteScPci,mr_LteNcPci,ecgiNeigh,distance,mr_LteNcEarfcn,mr_LteScPciMod3,mr_LteNcPciMod3,CountOfOverlapSample,TotalNumOfSample,OverlapRate";
        } else {
            $result['error'] = 'error';
            return json_encode($result);
        }
        $result['text'] = $text;

        $items = $conn->selectRaw($text)->where('dateId', $dateTime)->orderBy('userLabel', 'asc')->get()->toArray();
        if (count($items) == 0) {
            $result['error'] = 'error';
            return json_encode($result);
        }
        $result['rows']   = $items;
        $result['total']  = count($items);
        $result['result'] = 'true';

        $filename = "common/files/".$dbname."_MroPciMod3_".date('YmdHis').".csv";
        $this->resultToCSV2($result, $filename);
        $result['filename'] = $filename;
        $result['rows'] = null;
        return json_encode($result);

    }//end getAllMroPCIMOD3Data()


    /**
     * 写入CSV文件
     *
     * @param array  $result   查询结果
     * @param string $filename CSV文件名
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


    /**
     * 获得城市列表
     *
     * @return string 城市列表
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
    public function getPCIMOD3Date()
    {
        $dbname = $this->getMRDatabase(Input::get('city'));
        $dbname = $this->check_input($dbname);
        $dbc    = new DataBaseConnection();
        $db     = $dbc->getDB('MR', $dbname);
        $table  = 'mroPciMod3_day';
        $sql    = "select distinct dateId from $table";
        $this->type = $dbname.':PCIMOD3Analysis_day';
        return $this->getValue($db, $sql);


    }//end getPCIMOD3Date()

    function check_input($value)
    {
        //去除斜杠
        if (get_magic_quotes_gpc()) {
            $value=stripslashes($value);
        }
        return $value;
    }

    /**
     * 获得GENIUS标准检查结果表头
     *
     * @return array 检查结果表头
     */
    public function getMroPCIMOD3GeniusDataHeader()
    {
        $dateTime = Input::get('dateTime');
        $dbname   = $this->getMRDatabase(Input::get('dataBase'));
        $dbname   = $this->check_input($dbname);
        $result   = array();
        $conn = MroPciMod3Internal::on($dbname)->where('datetime_id', 'like', $dateTime.'%');
        if ($conn->exists()) {
            return $conn->first()->toArray();
        } else {
            $result['error'] = 'error';
            return $result;
        }

    }//end getMroPCIMOD3GeniusDataHeader()


    /**
     * 获得GENIUS标准MOD3检查结果(分页)
     *
     * @return void
     */
    public function getMroPCIMOD3GeniusData()
    {
        $page   = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $limit   = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 50;

        $dbname   = $this->getMRDatabase(Input::get('dataBase'));
        $dateTime = Input::get('dateTime');
        $result   = array();
        $rows = MroPciMod3Internal::on($dbname)->where('datetime_id', 'like', $dateTime.'%')->orderBy('ecgi', 'asc')->paginate($limit)->toArray();
        $result["total"] = $rows['total'];
        $result['records'] = $rows['data'];
        echo json_encode($result);

    }//end getMroPCIMOD3GeniusData()


    /**
     * 导出GENIUS标准检查结果
     *
     * @return void
     */
    public function getAllMroPCIMOD3GeniusData()
    {
        $dbname   = $this->getMRDatabase(Input::get('dataBase'));
        $dateTime = Input::get('dateTime');
        $result = array();
        $conn = MroPciMod3Internal::on($dbname);
        if ($conn->exists()) {
            $text = "datetime_id,ecgi,intensityScNc,intensitySc,ratio";
        } else {
            $result['error'] = 'error';
            return json_encode($result);
        }

        $result['text'] = $text;

        $items = $conn->selectRaw($text)->where('datetime_id', 'like', $dateTime.'%')->orderBy('ecgi', 'asc')->get()->toArray();

        $result['rows']   = $items;
        $result['total']  = count($items);
        $result['result'] = 'true';

        $filename = "common/files/".$dbname."_MroPciMod3Internal_".date('YmdHis').".csv";
        $this->resultToCSV2($result, $filename);
        $result['filename'] = $filename;
        $result['rows'] = null;

        echo json_encode($result);

    }//end getAllMroPCIMOD3GeniusData()


    /**
     * 获得GENIUS标准数据日期列表(天)
     *
     * @return array 日期列表
     */
    public function getPCIMOD3GeniusDate()
    {
        $dbname = $this->getMRDatabase(Input::get('city'));
        $dbc    = new DataBaseConnection();
        $db     = $dbc->getDB('MR', $dbname);
        $table  = 'mroPciMod3Internal';
        $sql    = "select distinct datetime_id from $table";
        $this->type = $dbname.':PCIMOD3Analysis1';
        return $this->getValue($db, $sql);

    }//end getPCIMOD3GeniusDate()


}//end class
