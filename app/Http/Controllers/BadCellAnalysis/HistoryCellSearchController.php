<?php

/**
 * HistoryCellSearchController.php
 *
 * @category BadCellAnalysis
 * @package  App\Http\Controllers\BadCellAnalysis
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
namespace App\Http\Controllers\BadCellAnalysis;

use App\Http\Controllers\Common\DataBaseConnection;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use PDO;
use App\Models\Mongs\Databaseconns;
use App\Models\AutoKPI\LowAccessCell;
use App\Models\AutoKPI\LowAccessCell_ex;
use App\Models\AutoKPI\HighLostCell;
use App\Models\AutoKPI\HighLostCell_ex;
use App\Models\AutoKPI\BadHandoverCell;
use App\Models\AutoKPI\BadHandoverCell_ex;
use App\Models\AutoKPI\InterfereCell;
use App\Models\AutoKPI\InterfereCell_avg;
use App\Models\AutoKPI\InterfereCell_one;

/**
 * 坏小区历史指教查询
 * Class HistoryCellSearchController
 *
 * @category BadCellAnalysis
 * @package  App\Http\Controllers\BadCellAnalysis
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class HistoryCellSearchController extends Controller
{

    
    /**
     * 获得单坏小区历史指标趋势
     *
     * @return void
     */
    public function getChartDataHistory()
    {
        $tables = Input::get('table');
        if ($tables == 'LowAccess') {
            $conn = new LowAccessCell;
        } else if ($tables == 'HighLost') {
            $conn = new HighLostCell;
        } else if ($tables == 'BadHandover') {
            $conn = new BadHandoverCell;
        } else if ($tables == 'Interference') {
            $conn = new InterfereCell;
        }

        $cell            = Input::get('cell');
        $startTime       = Input::get('startTime');
        $endTime         = Input::get('endTime');
        $yAxis_name_left = Input::get('yAxis_name_left');
        $yAxis_name_right = Input::get('yAxis_name_right');

        $conn = $conn->selectRaw('day_id,hour_id,'.$yAxis_name_left.','.$yAxis_name_right);
        if (strcmp($startTime, $endTime) == 0) {
            $conn = $conn->where('day_id', $endTime);
        } else if (strcmp($startTime, $endTime) < 0) {
            $conn = $conn->where('day_id', '>=', $startTime)
                        ->where('day_id', '<=', $endTime);
        }
        $rows = $conn->where('cell', $cell)
                    ->orderBy('day_id', 'asc')
                    ->orderBy('hour_id', 'asc')
                    ->get()
                    ->toArray();

        $yAxis      = array();
        $yAxis_2    = array();
        $items      = array();
        $returnData = array();
        $series     = array();
        $series_2   = array();
        $categories = array();
        foreach ($rows as $line) {
            $time = strval(strval($line['day_id'])." ".strval($line['hour_id'])).":00";
            $time = mb_convert_encoding($time, 'gb2312', 'utf-8');
            array_push($yAxis, $line[$yAxis_name_left]);
            array_push($yAxis_2, $line[$yAxis_name_right]);
            array_push($categories, $time);
        }

        $series['name']  = $yAxis_name_left;
        $series['color'] = '#89A54E';
        $series['type']  = 'spline';
        $series['data']  = $yAxis;

        $series_2['name']  = $yAxis_name_right;
        $series_2['color'] = '#4572A7';
        $series_2['type']  = 'column';
        $series_2['yAxis'] = 1;
        $series_2['data']  = $yAxis_2;
        array_push($items, $series_2);
        array_push($items, $series);
        $returnData['categories'] = $categories;
        $returnData['series']     = $items;
        echo json_encode($returnData);

    }//end getChartDataHistory()


    /**
     * 获得单小区历史指标详情
     *
     * @return array 单小区历史指标详情
     */
    public function getIndexCell()
    {
        $type      = Input::get('table');
        $cityArr   = Input::get('city');
        $startTime = Input::get('startTime');
        $endTime   = Input::get('endTime');
        $cell      = Input::get('cell');

        $result = [];
        if ($type == 'LowAccess') {
            $conn = LowAccessCell::selectRaw('day_id,hour_id,city,subNetwork,cell,无线接通率,RRC建立请求次数,RRC建立成功次数,RRC建立失败次数,RRC建立成功率,ERAB建立请求次数,ERAB建立成功次数,ERAB建立失败次数,ERAB建立成功率,License超限导致的RRC连接失败,承载准入拒绝导致的RRC连接失败,高负载导致的RRC连接失败,超载导致的RRC连接失败,无线进程失败导致的RRC连接失败,未指定的RRC连接失败,缺少资源导致的RRC连接失败,激活用户License超限导致的RRC连接失败,上行干扰电平dBm,估算TAkm,估算PUSCH_SINR,平均下行RSRP,平均下行CQI,平均下行RSRQ,小区退服时长s,小区人为闭锁时长s,最大RRC连接用户数,资源争抢的TTI数,CQI拥塞,SR拥塞,UE功率受限,RRC失败次数');

            $result['content'] = "day_id,hour_id,city,subNetwork,cell,无线接通率,RRC建立请求次数,RRC建立成功次数,RRC建立失败次数,RRC建立成功率,ERAB建立请求次数,ERAB建立成功次数,ERAB建立失败次数,ERAB建立成功率,License超限导致的RRC连接失败,承载准入拒绝导致的RRC连接失败,高负载导致的RRC连接失败,超载导致的RRC连接失败,无线进程失败导致的RRC连接失败,未指定的RRC连接失败,缺少资源导致的RRC连接失败,激活用户License超限导致的RRC连接失败,上行干扰电平dBm,估算TAkm,估算PUSCH_SINR,平均下行RSRP,平均下行CQI,平均下行RSRQ,小区退服时长s,小区人为闭锁时长s,最大RRC连接用户数,资源争抢的TTI数,CQI拥塞,SR拥塞,UE功率受限,RRC失败次数";
        } else if ($type == 'HighLost') {
            $conn = HighLostCell::selectRaw('day_id,hour_id,city,subNetwork,cell,无线掉线率,无线掉线次数,上下文建立成功数,遗留上下文数,小区闭锁导致的掉线,切换导致的掉线,S1接口故障导致的掉线,UE丢失导致的掉线,预清空导致的掉线,上行干扰电平dBm,估算TAkm,估算PUSCH_SINR,平均下行RSRP,平均下行CQI,平均下行RSRQ,小区退服时长s,小区人为闭锁时长s,最大RRC连接用户数,资源争抢的TTI数,CQI拥塞,SR拥塞,UE功率受限,ERAB建立失败次数');

            $result['content'] = "day_id,hour_id,city,subNetwork,cell,无线掉线率,无线掉线次数,上下文建立成功数,遗留上下文数,小区闭锁导致的掉线,切换导致的掉线,S1接口故障导致的掉线,UE丢失导致的掉线,预清空导致的掉线,上行干扰电平dBm,估算TAkm,估算PUSCH_SINR,平均下行RSRP,平均下行CQI,平均下行RSRQ,小区退服时长s,小区人为闭锁时长s,最大RRC连接用户数,资源争抢的TTI数,CQI拥塞,SR拥塞,UE功率受限,ERAB建立失败次数";
        } else if ($type == 'BadHandover') {
            $conn = BadHandoverCell::selectRaw('day_id,hour_id,city,subNetwork,cell,切换成功率,准备切换成功率,执行切换成功率,准备切换成功数,准备切换失败数,准备切换尝试数,执行切换成功数,执行切换失败数,执行切换尝试数,异频准备切换失败数,异频执行切换失败数,同频准备切换失败数,同频执行切换失败数');

            $result['content'] = "day_id,hour_id,city,subNetwork,cell,切换成功率,准备切换成功率,执行切换成功率,准备切换成功数,准备切换失败数,准备切换尝试数,执行切换成功数,执行切换失败数,执行切换尝试数,异频准备切换失败数,异频执行切换失败数,同频准备切换失败数,同频执行切换失败数";
        }
        $citys = Databaseconns::whereIn('cityChinese', $cityArr)->get(['connName'])->toArray();
        $allData = $conn->where('day_id', '>=', $startTime)
                    ->where('day_id', '<=', $endTime)
                    ->whereIn('city', $citys)
                    ->where('cell', $cell)
                    ->get()
                    ->toArray();

        $result['rows']     = $allData;
        $result['records']  = count($allData);
        $filename           = "common/files/".$type.date('YmdHis').".csv";
        $result['filename'] = $filename;
        $this->resultToCSV2($result, $filename);
        return $result;

    }//end getIndexCell()


    /**
     * 写入CSV文件
     *
     * @param array  $result   导出数据
     * @param string $filename CSV文件名
     *
     * @return void
     */
    protected function resultToCSV2($result, $filename)
    {
        $csvContent = mb_convert_encoding($result['content']."\n", 'gb2312', 'utf-8');
        $fp         = fopen($filename, "w");
        fwrite($fp, $csvContent);
        foreach ($result["rows"] as $row) {
            fputcsv($fp, $row);
        }

        fclose($fp);

    }//end resultToCSV2()


    /**
     * 获得单小区历史指标
     *
     * @return array 单小区历史指标
     */
    public function getHistoryCell()
    {
        $table     = Input::get('table');
        $cityArr   = Input::get('city');
        $startTime = Input::get('startTime');
        $endTime   = Input::get('endTime');
        $cell      = Input::get('cell');
        $hour      = Input::get('hour');

        if ($table == 'lowAccessCell_ex') {
            $conn = LowAccessCell_ex::selectRaw('id,city,subNetwork,cell, count(*) as 小时数, sum(RRC建立失败次数) as RRC建立失败次数, sum(ERAB建立失败次数) as ERAB建立失败次数');
        } else if ($table == 'highLostCell_ex') {
            $conn = HighLostCell_ex::selectRaw('id,city,subNetwork,cell, count(*) as 小时数, SUM(无线掉线次数) as 无线掉线次数');
        } else if ($table == 'badHandoverCell_ex') {
            $conn = BadHandoverCell_ex::selectRaw('id,city,subNetwork,cell, count(*) as 小时数,sum(准备切换失败数) as 准备切换失败数 ,sum(执行切换失败数) as 执行切换失败数 ,sum(异频准备切换失败数) as 异频准备切换失败数 ,sum(同频准备切换失败数) as 同频准备切换失败数 ,
                sum(同频执行切换失败数) as 同频执行切换失败数,sum(异频执行切换失败数) as 异频执行切换失败数');
        } else if ($table == 'interfereCell_avg') {
            $conn = InterfereCell_avg::selectRaw('id,city,subNetwork,cell, count(*) as 小时数');
        } else if ($table == 'interfereCell_one') {
            $conn = InterfereCell_one::selectRaw('id,city,subNetwork,cell, count(*) as 小时数');
        }
        $citys = Databaseconns::whereIn('cityChinese', $cityArr)->get(['connName'])->toArray();
        $conn = $conn->where('day_id', '>=', $startTime)
                    ->where('day_id', '<=', $endTime)
                    ->whereIn('city', $citys);
        if ($cell) {
            $conn = $conn->where('cell', $cell);
        }
        if ($hour) {
            $conn = $conn->whereIn('hour_id', $hour);
        }
        $rows = $conn->groupBy(['subNetwork', 'cell'])->orderBy('小时数', 'desc')->get()->toArray();
        $result = [];
        $allData = [];

        if ($table == 'lowAccessCell_ex') {
            foreach ($rows as $row) {
                $row['小时数'] = floatval($row['小时数']);
                $row['RRC建立失败次数']  = floatval($row['RRC建立失败次数']);
                $row['ERAB建立失败次数'] = floatval($row['ERAB建立失败次数']);
                array_push($allData, $row);
            }

            $result['content'] = "id,city,subNetwork,cell,小时数,RRC建立失败次数,ERAB建立失败次数";
        } else if ($table == 'highLostCell_ex') {
            foreach ($rows as $row) {
                $row['小时数']          = floatval($row['小时数']);
                $row['无线掉线次数'] = floatval($row['无线掉线次数']);
                array_push($allData, $row);
            }

            $result['content'] = "id,city,subNetwork,cell,小时数,无线掉线次数";
        } else if ($table == 'badHandoverCell_ex') {
            foreach ($rows as $row) {
                $row['小时数'] = floatval($row['小时数']);
                $row['准备切换失败数']       = floatval($row['准备切换失败数']);
                $row['执行切换失败数']       = floatval($row['执行切换失败数']);
                $row['异频准备切换失败数'] = floatval($row['异频准备切换失败数']);
                $row['同频准备切换失败数'] = floatval($row['同频准备切换失败数']);
                $row['同频执行切换失败数'] = floatval($row['同频执行切换失败数']);
                $row['异频执行切换失败数'] = floatval($row['异频执行切换失败数']);
                array_push($allData, $row);
            }

            $result['content'] = "id,city,subNetwork,cell,小时数,准备切换失败数,执行切换失败数,异频准备切换失败数,同频准备切换失败数,同频执行切换失败数,异频执行切换失败数";
        } else if ($table == 'interfereCell_avg' || $table == 'interfereCell_one') {
            foreach ($rows as $row) {
                $row['小时数'] = floatval($row['小时数']);
                array_push($allData, $row);
            }

            $result['content'] = "id,city,subNetwork,cell,小时数";
        }//end if
        $result['rows']     = $allData;
        $result['records']  = count($allData);
        $filename           = "common/files/".$table.date('YmdHis').".csv";
        $result['filename'] = $filename;
        $this->resultToCSV2($result, $filename);
        return $result;

    }//end getHistoryCell()


    /**
     * 获得城市列表
     *
     * @return string 城市列表
     */
    public function getCityOption()
    {
        $cityClass = new DataBaseConnection();
        return $cityClass->getCityOptions();

    }//end getCityOption()


    /**
     * 获得历史坏小区列表
     *
     * @return array 历史坏小区列表
     */
    public function getHistoryCellDate()
    {
        $type = Input::get('type');
        if ($type == '低接入小区') {
            $conn = new LowAccessCell;
        } else if ($type == '高掉线小区') {
            $conn = new HighLostCell;
        } else if ($type == '切换差小区') {
            $conn = new BadHandoverCell;
        } else if ($type == '高干扰小区') {
            $conn = new InterfereCell;
        }

        $result = array();
        $rows = $conn->distinct('day_id')->get(['day_id'])->toArray();
        $test   = [];
        if (count($rows) > 0) {
            foreach ($rows as $row) {
                $arr = explode(' ', $row['day_id']);
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

    }//end getHistoryCellDate()


}//end class
