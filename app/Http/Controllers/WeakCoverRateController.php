<?php

/**
 * WeakCoverRateController.php
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
use App\Models\MR\MroWeakCoverage_day;

/**
 * 弱覆盖率分析
 * Class WeakCoverRateController
 *
 * @category Controllers
 * @package  App\Http\Controllers
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class WeakCoverRateController extends Controller
{


    /**
     * 获得城市列表
     *
     * @return string
     */
    public function getCitys()
    {
        $dbc = new DataBaseConnection();
        return $dbc->getCityOptions();

    }//end getCitys()


    /**
     * 生成MRO弱覆盖数据头
     *
     * @return array
     */
    public function getMroWeakCoverageDataHeader()
    {
        $dbname = $this->getMRDatabase(Input::get('dataBase'));
        $result = array();
        $conn = new MroWeakCoverage_day;
        $conn = $conn->setConnection($dbname);
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
    }//end getMroWeakCoverageDataHeader()


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
     * 获得MRO弱覆盖数据
     *
     * @return string
     */
    public function getMroWeakCoverageData()
    {
        // $page   = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $limit   = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 50;
        $dbname    = $this->getMRDatabase(Input::get('dataBase'));
        $dateTime  = Input::get('date');
        $sortBy    = isset($_REQUEST['sortBy']) ? $_REQUEST['sortBy'] : "ratio110";
        $direction = isset($_REQUEST['direction']) ? $_REQUEST['direction'] : 'desc';

        $rows = MroWeakCoverage_day::on($dbname)
            ->selectRaw('mroWeakCoverage_day.*,cellName,siteName')
            ->where('dateId', $dateTime)
            ->leftJoin('siteLte', 'siteLte.ecgi', '=', 'mroWeakCoverage_day.ecgi')
            ->orderBy($sortBy, $direction)
            ->paginate($limit)
            ->toArray();

    
        
        $return    = array();
        $return["total"] = $rows['total'];
        $return['records']  = $rows['data'];
        echo json_encode($return);

    }//end getMroWeakCoverageData()


    /**
     * 导出全量MRO弱覆盖数据
     *
     * @return array
     */
    public function getAllMroWeakCoverageData()
    {
        $dbname   = $this->getMRDatabase(Input::get('dataBase'));
        // print_r($dbname);
        // var_dump($dbname);
        $dateTime = Input::get('date');



        $text = "dateId,ecgi,cellName,siteName,band,avgRsrp,avgRsrq,numLess80,numLess80_90,numLess90_100,numLess100_110,numLess110,numTotal,ratio110";

        $conn = new MroWeakCoverage_day;
        $conn = $conn->setConnection($dbname);
        $rows = $conn->selectRaw('dateId,mroWeakCoverage_day.ecgi,cellName,siteName,mroWeakCoverage_day.band,avgRsrp,avgRsrq,numLess80,numLess80_90,numLess90_100,numLess100_110,numLess110,numTotal,ratio110')
            ->where('dateId', $dateTime)
            ->leftJoin('siteLte', 'siteLte.ecgi', '=', 'mroWeakCoverage_day.ecgi')
            ->get()
            ->toArray();



     

        $filename = "common/files/MroWeakCoverageData".$dateTime."__".date('YmdHis').".csv";
        $csvContent = mb_convert_encoding($text."\n", 'GBK');
        $fp         = fopen($filename, "w");
        fwrite($fp, $csvContent);
        foreach ($rows as $row) {
            fputcsv($fp, $row);
        }
        fclose($fp);
        $return   = array();
        $return['filename'] = $filename;
        return $return;

    }//end getAllMroWeakCoverageData()


    /**
     * 写入CSV文件
     *
     * @param array  $result   查询结果
     * @param string $filename CSV文件名
     * @param mixed  $db       数据库名
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
        $res = $db->query($sql);
        $dbc   = new DataBaseConnection();
        $dbs     = $dbc->getDB('mongs', 'mongs');
        while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            /*code...*/
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
     * 获得日期列表
     *
     * @return array
     */
    public function weakCoverRateDate()
    {
        $city = Input::get('city');
        $city = $this->check_input($city);
        $dbname = $this->getMRDatabase($city);
        if($dbname){
        	$dbc    = new DataBaseConnection();
	        $db     = $dbc->getDB('MR', $dbname);
	        $table  = 'mroWeakCoverage_day';
	 
	        $sql    = "select distinct dateId from $table";

	        $key = 'date:'.$city.':'.$table;
	        $dateUtil = new DateUtil();
	        return $dateUtil->getDateListWithData($db, $key, $sql);
	    }else{
	    	return;
	    }
     

    }//end weakCoverRateDate()


    /**
     * 获得日期列表
     *
     * @param string $value 数据字串
     *
     * @return array
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
