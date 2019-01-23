<?php

/**
 * MRONeighAnalysisController.php
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
use App\Models\MR\MroServeNeigh_day;
use App\Models\Mongs\NeighOptimizationWhiteList;

/**
 * MRO邻区分析
 * Class MRONeighAnalysisController
 *
 * @category NetworkOptimization
 * @package  App\Http\Controllers\NetworkOptimization
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class MRONeighAnalysisController extends MyRedis
{


    /**
     * 获得MRO邻区分析数据头
     *
     * @return array
     */
    public function getMroServeNeighDataHeader()
    {
        $dbname   = $this->getMRDatabase(Input::get('dataBase'));
        $dateTime = Input::get('dateTime');
        $result   = array();
        $rs = MroServeNeigh_day::on($dbname)->where('dateId', $dateTime)->exists();
        if ($rs) {
            $keys = ['dateId','ecgi','ecgiNeigh_direct','isdefined_direct','distance_direct','mr_LteNcEarfcn','mr_LteNcPci','ncFreq_session','nc_session_num','nc_session_ratio','ncFreq_times_num','nc_times_num','nc_times_ratio','avg_mr_LteScRSRP','avg_mr_LteScRSRQ','avg_mr_LteNcRSRP','avg_mr_LteNcRSRQ'];
            $text = '';
            foreach ($keys as $key) {
                if ($key == 'id') {
                    continue;
                }
                $key = trans('message.MRO.'.$key);
                $text .= $key . ',';
            }

            $text = substr($text, 0, strlen($text) - 1);
            $result['text'] = $text;
            $result['field'] = "dateId,ecgi,ecgiNeigh_direct,isdefined_direct,distance_direct,mr_LteNcEarfcn,mr_LteNcPci,ncFreq_session,nc_session_num,nc_session_ratio,ncFreq_times_num,nc_times_num,nc_times_ratio,avg_mr_LteScRSRP,avg_mr_LteScRSRQ,avg_mr_LteNcRSRP,avg_mr_LteNcRSRQ";
            return $result;
        } else {
            $result['error'] = 'error';
            return $result;
        }

    }//end getMroServeNeighDataHeader()


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
     * 获得分页MRO测量邻区数据
     *
     * @return mixed
     */
    public function getMroServeNeighData()
    {
        // 获取白名单
        $city     = input::get("dataBase");
        $dataType = Input::get('dataType');
        $OptimizationType = input::get("OptimizationType");
        $rows = NeighOptimizationWhiteList::where('OptimizationType', $OptimizationType)->where('dataType', $dataType)->where('city', $city)->get();
        $whiteList = [];
        if ($rows) {
            $rows = $rows->toArray();
            foreach ($rows as $row) {
                array_push($whiteList, $row['ecgi']);
            }
        }

        $input9  = Input::get('input9');
        $input10 = Input::get('input10');
        $input11 = Input::get('input11');
        $input12 = Input::get('input12');
        $input13 = Input::get('input13');
        $input14 = Input::get('input14');
        // $page    = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $limit    = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 50;

        $dbname   = $this->getMRDatabase(Input::get('dataBase'));
        $dateTime = Input::get('dateTime');
        $result   = array();
        $return   = array();

        $conn = MroServeNeigh_day::on($dbname)
                ->where('nc_session_ratio', '>=', $input9)
                ->where('nc_times_ratio', '>=', $input10)
                ->where('avg_mr_LteScRSRP', '>=', $input12)
                ->where('avg_mr_LteScRSRQ', '>=', $input13)
                ->where('avg_mr_LteNcRSRP', '>=', $input14)
                ->where('dateId', $dateTime)
                ->where(function($query){
                    $query->where('isdefined_direct', '!=', 1)
                        ->orWhereNull('isdefined_direct');
                });
        if (count($whiteList) > 0) {
            $conn = $conn->whereNotIn('ecgi', $whiteList);
        }
        if ($conn->count()==0) {
            $result['error'] = 'error';
            return json_encode($result);
        }
        $rows = $conn->paginate($limit)->toArray();
        $return["total"] = $rows['total'];
        $return['records'] = $rows['data'];
        return json_encode($return);

    }//end getMroServeNeighData()


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
     * 导出全量MRO测量邻区数据
     *
     * @return array 导出结果
     */
    public function getAllMroServeNeighData()
    {
        $dbname   = $this->getMRDatabase(Input::get('dataBase'));
        $dateTime = Input::get('dateTime');
        $input9  = Input::get('input9');
        $input10 = Input::get('input10');
        $input11 = Input::get('input11');
        $input12 = Input::get('input12');
        $input13 = Input::get('input13');
        $input14 = Input::get('input14');
        $result   = array();
        $return   = array();
        $rs = MroServeNeigh_day::on($dbname)->exists();
        if ($rs) {
            $keys = ['id','dateId','ecgi','ecgiNeigh_direct','isdefined_direct','distance_direct','mr_LteNcEarfcn','mr_LteNcPci','ncFreq_session','nc_session_num','nc_session_ratio','ncFreq_times_num','nc_times_num','nc_times_ratio','avg_mr_LteScRSRP','avg_mr_LteScRSRQ','avg_mr_LteNcRSRP','avg_mr_LteNcRSRQ'];
        } else {
            $result['error'] = 'error';
            return $result;
        }

        $text = '';
        foreach ($keys as $key) {
            if ($key == 'id') {
                continue;
            }
            $key = trans('message.MRO.'.$key);
            $text .= $key.',';
        }

        $text           = substr($text, 0, (strlen($text) - 1));
        $result['text'] = $text;

        $rows = MroServeNeigh_day::on($dbname)
                ->where('nc_session_ratio', '>=', $input9)
                ->where('nc_times_ratio', '>=', $input10)
                ->where('avg_mr_LteScRSRP', '>=', $input12)
                ->where('avg_mr_LteScRSRQ', '>=', $input13)
                ->where('avg_mr_LteNcRSRP', '>=', $input14)
                ->where('dateId', $dateTime)
                ->where(function($query){
                    $query->where('isdefined_direct', '!=', 1)
                        ->orWhereNull('isdefined_direct');
                })
                ->get($keys)
                ->toArray();

        $csvContent = mb_convert_encoding($result['text'] . "\n", 'gb2312', 'utf-8');
        $filename = "common/files/AllMroServeNeighData".date('YmdHis').".csv";
        $fp = fopen($filename, "w");
        fwrite($fp, $csvContent);
        foreach ($rows as $row) {
            array_shift($row);
            fputcsv($fp, $row);
        }
        fclose($fp);

        $return['filename'] = $filename;
        return $return;

    }//end getAllMroServeNeighData()


    /**
     * 写入CSV(CURSOR)
     *
     * @param array  $result   查询结果
     * @param string $filename 文件名
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
            array_shift($row);
            fputcsv($fp, $row);
        }

        fclose($fp);

    }//end resultToCSV2_All()


    /**
     * 获得城市列表
     *
     * @return string 城市列表(JSON)
     */
    public function getAllCity()
    {
        $dbc = new DataBaseConnection();
        return $dbc->getCityOptions();

    }//end getAllCity()


    /**
     * 获得日期列表(天)
     *
     * @return array 日期列表(天)
     */
    public function getfdfdb()
    {
        $city = Input::get('city');
        $city = $this->check_input($city);
        $dbname = $this->getMRDatabase($city);
        $type = Input::get('type');
        $type = $this->check_input($type);
        $dbc    = new DataBaseConnection();
        $db     = $dbc->getDB('MR', $dbname);
        if ($type == 'MRO') {
            $table  = 'mroServeNeigh_day';
        } else {
            $table  = 'mreServeNeigh_day';
        }
        $sql    = "select distinct dateId from $table";
        $this->type = $dbname.':MROServeNeighAnalysis:'.$table;
        return $this->getValue($db, $sql);

    }//end getfdfdb()
    function check_input($value)
    {
        //去除斜杠
        if (get_magic_quotes_gpc()) {
            $value=stripslashes($value);
        }
        return $value;
    }
}//end class
