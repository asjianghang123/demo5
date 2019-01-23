<?php

/**
 * MRENeighAnalysisController.php
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
use PDO;
use App\Models\MR\MreServeNeigh_day;
use App\Models\Mongs\NeighOptimizationWhiteList;

/**
 * MRE邻区分析
 * Class MRENeighAnalysisController
 *
 * @category NetworkOptimization
 * @package  App\Http\Controllers\NetworkOptimization
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class MRENeighAnalysisController extends Controller
{


    /**
     * 获得分析结果表头
     *
     * @return array 分析结果表头
     */
    public function getMreServeNeighDataHeader()
    {
        $dbname   = $this->getMRDatabase(Input::get('dataBase'));
        $dateTime = Input::get('dateTime');
        $dbc      = new DataBaseConnection();
        $result   = array();
        $rs = MreServeNeigh_day::on($dbname)->whereRaw('mr_LteScEarfcn = mr_LteNcEarfcn')->where('dateId', $dateTime)->exists();
        if ($rs) {
            $keys = ['dateId','ecgi','ecgiNeigh_direct','isdefined_direct','distance_direct','mr_LteNcEarfcn','mr_LteNcPci','ncFreq_session','nc_session_num','nc_session_ratio','ncFreq_times_num','nc_times_num','nc_times_ratio','avg_mr_LteScRSRP','avg_mr_LteScRSRQ','avg_mr_LteNcRSRP','avg_mr_LteNcRSRQ'];
            $text = '';
            foreach ($keys as $key) {
                if ($key == 'id') {
                    continue;
                }
                $key = trans('message.4GMRE.'.$key);
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

    }//end getMreServeNeighDataHeader()


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
     * 获得MRE测量邻区分页数据
     *
     * @return string 当前页MRE测量邻区数据(JSON)
     */
    public function getMreServeNeighData()
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

        $input1 = Input::get('input1');
        $input2 = Input::get('input2');
        $input3 = Input::get('input3');
        $input4 = Input::get('input4');
        $input5 = Input::get('input5');
        $input6 = Input::get('input6');
        $input7 = Input::get('input7');
        $input8 = Input::get('input8');
        // $page   = isset($_REQUEST['page']) ? intval($_REQUEST['page']) : 1;
        $limit   = isset($_REQUEST['limit']) ? intval($_REQUEST['limit']) : 50;

        $dbname   = $this->getMRDatabase(Input::get('dataBase'));
        $dateTime = Input::get('dateTime');
        $result   = array();
        $return   = array();

        $rs = MreServeNeigh_day::on($dbname)
                ->whereRaw('mr_LteScEarfcn = mr_LteNcEarfcn')
                ->where('nc_session_ratio', '>=', $input1)
                ->where('nc_times_ratio', '>=', $input3)
                ->where('avg_mr_LteScRSRP', '>=', $input6)
                ->where('avg_mr_LteScRSRQ', '>=', $input7)
                ->where('avg_mr_LteNcRSRP', '>=', $input8)
                ->where('dateId', $dateTime)
                ->where(function($query){
                    $query->where('isdefined_direct', '!=', 1)
                        ->orWhereNull('isdefined_direct');
                });
        if (count($whiteList) > 0) {
            $rs = $rs->whereNotIn('ecgi', $whiteList);
        }
        if ($rs->count()==0) {
            $result['error'] = 'error';
            return json_encode($result);
        }
        $rows = $rs->paginate($limit)->toArray();
        $return["total"] = $rows['total'];
        $return['records'] = $rows['data'];
        return json_encode($return);

    }//end getMreServeNeighData()


    /**
     * 写入CSV文件
     *
     * @param array  $result   查询结果
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


    /**
     * 导出全量MRE测量邻区数据
     *
     * @return array 导出结果
     */
    public function getAllMreServeNeighData()
    {
        $dbname   = $this->getMRDatabase(Input::get('dataBase'));
        $dateTime = Input::get('dateTime');
        $input1 = Input::get('input1');
        $input2 = Input::get('input2');
        $input3 = Input::get('input3');
        $input4 = Input::get('input4');
        $input5 = Input::get('input5');
        $input6 = Input::get('input6');
        $input7 = Input::get('input7');
        $input8 = Input::get('input8');
        $result   = array();
        $return   = array();
        $rs = MreServeNeigh_day::on($dbname)->exists();
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
            $key = trans('message.4GMRE.'.$key);
            $text .= $key.',';
        }

        $text           = substr($text, 0, (strlen($text) - 1));
        $result['text'] = $text;

        $rows = MreServeNeigh_day::on($dbname)
                ->whereRaw('mr_LteScEarfcn = mr_LteNcEarfcn')
                ->where('nc_session_ratio', '>=', $input1)
                ->where('nc_times_ratio', '>=', $input3)
                ->where('avg_mr_LteScRSRP', '>=', $input6)
                ->where('avg_mr_LteScRSRQ', '>=', $input7)
                ->where('avg_mr_LteNcRSRP', '>=', $input8)
                ->where('dateId', $dateTime)
                ->where(function($query){
                    $query->where('isdefined_direct', '!=', 1)
                        ->orWhereNull('isdefined_direct');
                })
                ->get($keys)
                ->toArray();

        $csvContent = mb_convert_encoding($result['text'] . "\n", 'gb2312', 'utf-8');
        $filename = "common/files/AllMreServeNeighData".date('YmdHis').".csv";
        $fp = fopen($filename, "w");
        fwrite($fp, $csvContent);
        foreach ($rows as $row) {
            array_shift($row);
            fputcsv($fp, $row);
        }
        fclose($fp);

        $return['filename'] = $filename;
        return $return;

    }//end getAllMreServeNeighData()


    /**
     * 导出CSV文件(CURSOR)
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
     * 获取城市列表
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
     * @return array 日期(天)列表
     */
    public function getfdfdc()
    {
        $dbname = $this->getMRDatabase(Input::get('city'));
        $dbc    = new DataBaseConnection();
        $db     = $dbc->getDB('MR', $dbname);
        $table  = 'mreServeNeigh_day';
        $result = array();
        $sql    = "select distinct dateId from $table";
        $rs     = $db->query($sql, PDO::FETCH_ASSOC);
        $test   = [];
        if ($rs) {
            $rows = $rs->fetchall();
            if (count($rows) > 0) {
                foreach ($rows as $row) {
                    $arr = explode(' ', $row['dateId']);
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

    }//end getfdfdc()


}//end class
