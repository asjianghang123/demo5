<?php

/**
 * NetworkChartsExporter.php
 *
 * @category Exporter
 * @package  App\Http\Controllers\Exporter
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
namespace APP\Http\Controllers\Exporter;

use App\Http\Controllers\Controller;
use App\Http\Requests\NetworkChartsRequest;
use App\Http\Requests\Request;
use DateInterval;
use DateTime;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use PDO;
use PHPExcel;
use PHPExcel_Chart;
use PHPExcel_Chart_DataSeries;
use PHPExcel_Chart_DataSeriesValues;
use PHPExcel_Chart_Layout;
use PHPExcel_Chart_Legend;
use PHPExcel_Chart_PlotArea;
use PHPExcel_Chart_Title;
use PHPExcel_Writer_Excel2007;
use App\Http\Controllers\Common\DataBaseConnection;


/**
 * Class NetworkChartsExporter
 * 短板概览报告
 *
 * @category Exporter
 * @package  App\Http\Controllers\Exporter
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class NetworkChartsExporter extends Controller
{


    /**
     * 创建Excel报告.
     *
     * @param Request $request HTTP请求
     *
     * @return string Excel文件名
     */
    public function export(NetworkChartsRequest $request)
    {
        // 创建excel对象.
        $excel = new PHPExcel();

        // 创建基站类型分布sheet.
        $sheetBSC = $excel->getSheet(0);
        $sheetBSC->setTitle('差小区概览');

        $access = new BadCellByCity($request);
        $sheetBSC->addChart($access->toExcelChart()->setTopLeftPosition('A1')->setBottomRightPosition('H20'));
        // 对角长度
        $sheetBSC->fromArray($access->toExcelArray(), null, 'A21');

       $sheetABN = $excel->createSheet(1);
        $sheetABN->setTitle('告警概览');

        // 当前告警
        $access = new AlarmByNum($request);
        $sheetABN->addChart($access->toExcelChart()->setTopLeftPosition('A1')->setBottomRightPosition('G20'));
        // 对角长度
        $sheetABN->fromArray($access->toExcelArray(), null, 'A21');

        /* // 历史告警
        $access = new AlarmByHistory($request);
        $sheetABN->addChart($access->toExcelChart()->setTopLeftPosition('H1')->setBottomRightPosition('CF20'));
        // 对角长度
        $sheetABN->fromArray($access->toExcelArray(), null, 'H21');*/

        $sheetPTO = $excel->createSheet(2);
        $sheetPTO->setTitle('参数概览');
        // Baseline检查->参数数量分布
        $access = new ParamByNum($request);
        $sheetPTO->addChart($access->toExcelChart()->setTopLeftPosition('A1')->setBottomRightPosition('H20'));
        $sheetPTO->fromArray($access->toExcelArray(), null, 'A21');
       // Baseline检查->基站数量分布
        $access = new ErbsByNum($request);
        $sheetPTO->addChart($access->toExcelChart()->setTopLeftPosition('I1')->setBottomRightPosition('P20'));
        $sheetPTO->fromArray($access->toExcelArray(), null, 'I21');
        /* // 一致性检查->参数数量分布
        $access = new ParamByConsistencyNum($request);
        $sheetPTO->addChart($access->toExcelChart()->setTopLeftPosition('A23')->setBottomRightPosition('H43'));
        $sheetPTO->fromArray($access->toExcelArray(), null, 'A44');
        // 一致性检查->小区数量分布
        $access = new ErbsByConsistencyNum($request);
        $sheetPTO->addChart($access->toExcelChart()->setTopLeftPosition('I23')->setBottomRightPosition('P43'));
        $sheetPTO->fromArray($access->toExcelArray(), null, 'I44');*/

        $sheetPTO = $excel->createSheet(3);
        $sheetPTO->setTitle('弱覆盖概览');
        $access = new WeakCoverOverview($request);
        $sheetPTO->addChart($access->toExcelChart()->setTopLeftPosition('A1')->setBottomRightPosition('BG20'));
        $sheetPTO->fromArray($access->toExcelArray(), null, 'A21');

        $sheetPTO = $excel->createSheet(4);
        $sheetPTO->setTitle('重叠覆盖概览');
        $access = new OverlapCoverview($request);
        $sheetPTO->addChart($access->toExcelChart()->setTopLeftPosition('A1')->setBottomRightPosition('BG20'));
        $sheetPTO->fromArray($access->toExcelArray(), null, 'A21');


        $writer = new PHPExcel_Writer_Excel2007($excel);
        $writer->setIncludeCharts(true);
        $writer->save('NetworkWeak.xlsx');
        return 'NetworkWeak.xlsx';

    }//end export()


}//end class


/**
 * 小区数量分布
 * Class ErbsByConsistencyNum
 *
 * @category Model
 * @package  App\Http\Controllers\Exporter
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class ErbsByConsistencyNum extends AbstractModel
{


    /**
     * ErbsByConsistencyNum constructor.
     *
     * @param NetworkChartsRequest $request HTTP请求
     */
    public function __construct(NetworkChartsRequest $request)
    {
        $this->request    = $request;
        $this->chartType  = PHPExcel_Chart_DataSeries::TYPE_BARCHART;
        $this->chartTitle = '小区数量分布';
        $this->chartData  = $this->getChartData();

    }//end __construct()


    /**
     * 生成图表数据
     *
     * @return mixed
     */
    public function getChartData()
    {
        if ($this->chartData !== null) {
            return $this->chartData;
        }

        $date = new DateTime();
        $date->sub(new DateInterval('P1D'));
        $yesDate  = $date->format('ymd');
        $yesDate1 = $date->format('Y-m-d');
        $db       = 'kget'.$yesDate;
        $res      = DB::select('select connName,subNetwork from databaseconn ');
        $sql      = '';
        foreach ($res as $items) {
            $subNetwork = $items->subNetwork;
            $subNetwork = $this->reCombine($subNetwork);
            $city       = $items->connName;
            $sql        = $sql." select sum(num) as kpi, '$city' as xTicks, '$yesDate1' as series from (
                            select count(distinct meContext) num from TempEUtranCellFreqRelation where subNetwork in (".$subNetwork.") union all
                            select count(distinct meContext) num from TempEUtranCellRelationUnidirectionalNeighborCell where subNetwork in (".$subNetwork.") union all
                            select count(distinct meContext) num from TempEUtranCellRelationExistNeighborCellWithoutX2 where subNetwork in (".$subNetwork.") union
                            select count(distinct meContext) num from TempEUtranCellRelationManyNeighborCell where subNetwork in (".$subNetwork.") union all
                            select count(distinct meContext) num from TempEUtranCellRelationFewNeighborCell where subNetwork in (".$subNetwork.") union all
                            select count(distinct meContext) num from TempExternalEUtranCellTDDActivePlmnListCheck where subNetwork in (".$subNetwork.") union all
                            select count(distinct meContext) num from TempEUtranCellRelationNeighOfPci where subNetwork in (".$subNetwork.") union all
                            select count(distinct meContext) num from TempEUtranCellRelationNeighOfNeighPci where subNetwork in (".$subNetwork.") union all
                            select count(distinct meContext) num from TempGeranCellRelation2GNeighbor where subNetwork in (".$subNetwork.") union all
                            select count(distinct meContext) num from TempParameter2GKgetCompare where subNetwork in (".$subNetwork.") union all
                            select count(distinct meContext) num from TempExternalNeigh4G where subNetwork in (".$subNetwork.") union all
                            select count(distinct meContext) num from TempParameterQCI_A1A2 where subNetwork in (".$subNetwork.") union all
                            select count(distinct meContext) num from TempParameterQCI_B2A2critical where subNetwork in (".$subNetwork.") 
                            ) t UNION ";
        }

        $sql = rtrim($sql, 'UNION ')." ORDER by xTicks";
        $db  = new PDO("mysql:host=localhost;dbname=$db", "root", "mongs");
        return collect($db->query($sql)->fetchAll());

    }//end getChartData()


    /**
     * 重拼子网字串
     *
     * @param string $subNetwork 子网字串
     *
     * @return string
     */
    protected function reCombine($subNetwork)
    {
        $subNetArr  = explode(",", $subNetwork);
        $subNetsStr = '';
        foreach ($subNetArr as $subNet) {
            $subNetsStr .= "'".$subNet."',";
        }

        return substr($subNetsStr, 0, -1);

    }//end reCombine()


}//end class


/**
 * 参数数量分布
 * Class ParamByConsistencyNum
 *
 * @category Model
 * @package  App\Http\Controllers\Exporter
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class ParamByConsistencyNum extends AbstractModel
{


    /**
     * ParamByConsistencyNum constructor.
     *
     * @param NetworkChartsRequest $request HTTP请求
     */
    public function __construct(NetworkChartsRequest $request)
    {
        $this->request    = $request;
        $this->chartType  = PHPExcel_Chart_DataSeries::TYPE_BARCHART;
        $this->chartTitle = '参数数量分布';
        $this->chartData  = $this->getChartData();

    }//end __construct()


    /**
     * 生成图表数据
     *
     * @return mixed
     */
    public function getChartData()
    {
        if ($this->chartData !== null) {
            return $this->chartData;
        }

        $date = new DateTime();
        $date->sub(new DateInterval('P1D'));
        $yesDate  = $date->format('ymd');
        $yesDate1 = $date->format('Y-m-d');
        $db       = 'kget'.$yesDate;
        $res      = DB::select('select connName,subNetwork from databaseconn ');
        $sql      = '';
        foreach ($res as $items) {
            $subNetwork = $items->subNetwork;
            $subNetwork = $this->reCombine($subNetwork);
            $city       = $items->connName;
            $sql        = $sql." select sum(num) as kpi, '$city' as xTicks, '$yesDate1' as series from (
                            select count(*) num from TempEUtranCellFreqRelation where subNetwork in (".$subNetwork.") union all
                            select count(*) num from TempEUtranCellRelationUnidirectionalNeighborCell where subNetwork in (".$subNetwork.") union all
                            select count(*) num from TempEUtranCellRelationExistNeighborCellWithoutX2 where subNetwork in (".$subNetwork.") union
                            select count(*) num from TempEUtranCellRelationManyNeighborCell where subNetwork in (".$subNetwork.") union all
                            select count(*) num from TempEUtranCellRelationFewNeighborCell where subNetwork in (".$subNetwork.") union all
                            select count(*) num from TempExternalEUtranCellTDDActivePlmnListCheck where subNetwork in (".$subNetwork.") union all
                            select count(*) num from TempEUtranCellRelationNeighOfPci where subNetwork in (".$subNetwork.") union all
                            select count(*) num from TempEUtranCellRelationNeighOfNeighPci where subNetwork in (".$subNetwork.") union all
                            select count(*) num from TempGeranCellRelation2GNeighbor where subNetwork in (".$subNetwork.") union all
                            select count(*) num from TempParameter2GKgetCompare where subNetwork in (".$subNetwork.") union all
                            select count(*) num from TempExternalNeigh4G where subNetwork in (".$subNetwork.") union all
                            select count(*) num from TempParameterQCI_A1A2 where subNetwork in (".$subNetwork.") union all
                            select count(*) num from TempParameterQCI_B2A2critical where subNetwork in (".$subNetwork.") 
                            ) t UNION ";
        }

        $sql = rtrim($sql, 'UNION ')." ORDER by xTicks";
        $db  = new PDO("mysql:host=localhost;dbname=$db", "root", "mongs");
        return collect($db->query($sql)->fetchAll());

    }//end getChartData()


    /**
     * 重组子网字串
     *
     * @param string $subNetwork 子网字串
     *
     * @return string
     */
    protected function reCombine($subNetwork)
    {
        $subNetArr  = explode(",", $subNetwork);
        $subNetsStr = '';
        foreach ($subNetArr as $subNet) {
            $subNetsStr .= "'".$subNet."',";
        }

        return substr($subNetsStr, 0, -1);

    }//end reCombine()


}//end class


/**
 * 基站数量分布
 * Class ErbsByNum
 *
 * @category Model
 * @package  App\Http\Controllers\Exporter
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class ErbsByNum extends AbstractModel
{


    /**
     * ErbsByNum constructor.
     *
     * @param NetworkChartsRequest $request HTTP请求
     */
    public function __construct(NetworkChartsRequest $request)
    {
        $this->request    = $request;
        $this->chartType  = PHPExcel_Chart_DataSeries::TYPE_BARCHART;
        $this->chartTitle = '基站数量分布';
        $this->chartData  = $this->getChartData();

    }//end __construct()


    /**
     * 生成图表数据
     *
     * @return mixed
     */
    public function getChartData()
    {
        if ($this->chartData !== null) {
            return $this->chartData;
        }

        $date = new DateTime();
        $date->sub(new DateInterval('P1D'));
        $yesDate  = $date->format('ymd');
        $yesDate1 = $date->format('Y-m-d');
        $db       = 'kget'.$yesDate;
        //$res      = DB::select('select connName,subNetwork from databaseconn ');
        $dbc        = new DataBaseConnection();
        $res      = $dbc->getCitySubNetCategories();
        $sql      = '';
        foreach ($res as $items) {
            $subNetwork = $items->subNetwork;
            $subNetwork = $this->reCombine($subNetwork);
            $city       = $items->connName;
            $sql        = $sql." select count(distinct meContext) as kpi, '$city' as xTicks, '$yesDate1' as series from ParaCheckBaseline where (category = 'A' or category = 'M') and subNetwork in (".$subNetwork.") UNION ";
        }

        $sql = rtrim($sql, 'UNION ')." ORDER by xTicks";
        $db  = new PDO("mysql:host=localhost;dbname=$db", "root", "mongs");
        return collect($db->query($sql)->fetchAll());

    }//end getChartData()


    /**
     * 重组子网字串
     *
     * @param string $subNetwork 子网字串
     *
     * @return string
     */
    protected function reCombine($subNetwork)
    {
        $subNetArr  = explode(",", $subNetwork);
        $subNetsStr = '';
        foreach ($subNetArr as $subNet) {
            $subNetsStr .= "'".$subNet."',";
        }

        return substr($subNetsStr, 0, -1);

    }//end reCombine()


}//end class


/**
 * 参数数量分布
 * Class ParamByNum
 *
 * @category Model
 * @package  App\Http\Controllers\Exporter
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class ParamByNum extends AbstractModel
{


    /**
     * ParamByNum constructor.
     *
     * @param NetworkChartsRequest $request HTTP请求
     */
    public function __construct(NetworkChartsRequest $request)
    {
        $this->request    = $request;
        $this->chartType  = PHPExcel_Chart_DataSeries::TYPE_BARCHART;
        $this->chartTitle = '参数数量分布';
        $this->chartData  = $this->getChartData();

    }//end __construct()


    /**
     * 生成图表数据
     *
     * @return mixed
     */
    public function getChartData()
    {
        if ($this->chartData !== null) {
            return $this->chartData;
        }

        $date = new DateTime();
        $date->sub(new DateInterval('P1D'));
        $yesDate  = $date->format('ymd');
        $yesDate1 = $date->format('Y-m-d');
        $db       = 'kget'.$yesDate;
        $dsn      = "mysql:host=localhost;dbname=$db";
        //$res      = DB::select('select connName,subNetwork from databaseconn ');
        $dbc        = new DataBaseConnection();
        $res      = $dbc->getCitySubNetCategories();
        $sql      = '';
        foreach ($res as $items) {
            $subNetwork = $items->subNetwork;
            $subNetwork = $this->reCombine($subNetwork);
            $city       = $items->connName;
            $sql        = $sql." select count(id) as kpi, '$city' as xTicks, '$yesDate1' as series from ParaCheckBaseline where (category = 'A' or category = 'M') and subNetwork in (".$subNetwork.") UNION ";
        }

        $sql = rtrim($sql, 'UNION ')." ORDER by xTicks";
        $db  = new PDO("mysql:host=localhost;dbname=$db", "root", "mongs");
        return collect($db->query($sql)->fetchAll());

    }//end getChartData()


    /**
     * 重组子网字串
     *
     * @param string $subNetwork 子网字串
     *
     * @return string
     */
    protected function reCombine($subNetwork)
    {
        $subNetArr  = explode(",", $subNetwork);
        $subNetsStr = '';
        foreach ($subNetArr as $subNet) {
            $subNetsStr .= "'".$subNet."',";
        }

        return substr($subNetsStr, 0, -1);

    }//end reCombine()


}//end class


/**
 * 历史告警
 * Class AlarmByHistory
 *
 * @category Model
 * @package  App\Http\Controllers\Exporter
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class AlarmByHistory extends AbstractModel
{


    /**
     * AlarmByHistory constructor.
     *
     * @param NetworkChartsRequest $request HTTP请求
     */
    public function __construct(NetworkChartsRequest $request)
    {
        $this->request    = $request;
        $this->chartType  = PHPExcel_Chart_DataSeries::TYPE_LINECHART;
        $this->chartTitle = '历史告警';
        $this->chartData  = $this->getChartData();

    }//end __construct()


    /**
     * 生成图表数据
     *
     * @return mixed
     */
    public function getChartData()
    {
        if ($this->chartData !== null) {
            return $this->chartData;
        }

        $sql = "select date as xTicks,city as series, alarm_num as kpi from FMA_alarm_log_group_by_city_date where date>'2016-05-01';";
        $db  = new PDO("mysql:host=localhost;dbname=Alarm", "root", "mongs");
        return collect($db->query($sql)->fetchAll());

    }//end getChartData()


}//end class


/**
 * 当前告警数量分布
 *
 * Class AlarmByNum
 *
 * @category Model
 * @package  App\Http\Controllers\Exporter
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class AlarmByNum extends AbstractModel
{


    /**
     * AlarmByNum constructor.
     *
     * @param NetworkChartsRequest $request HTTP请求
     */
    public function __construct(NetworkChartsRequest $request)
    {
        $this->request    = $request;
        $this->chartType  = PHPExcel_Chart_DataSeries::TYPE_BARCHART;
        $this->chartTitle = '当前告警';
        $this->chartData  = $this->getChartData();

    }//end __construct()


    /**
     * 生成图表数据
     *
     * @return mixed
     */
    public function getChartData()
    {
        if ($this->chartData !== null) {
            return $this->chartData;
        }

        $conn    = DB::connection('alarm');
        $sql_key = "select case Perceived_severity
                when 0 then 'Indeterminate(事件)'
                when 1 then 'Critical'
                when 2 then 'Major'
                when 3 then 'Minor'
                when 4 then 'Warning'
                when 5 then 'Cleared'
                end as type,Perceived_severity from FMA_alarm_list group by type order by type";
        $rs      = $conn->select($sql_key);
        $sql     = '';
        foreach ($rs as $item) {
            $type = $item->type;
            $sql  = $sql." select b.city as xTicks,'$type' as series,a.kpi from (select city as xTicks,
               count(*) as kpi from FMA_alarm_list where Perceived_severity='".$item->Perceived_severity."' and city is not null and city != '' group by city,Perceived_severity)a right join (select distinct city from FMA_alarm_list where city !='')b
                    on a.xTicks=b.city UNION ";
        }
        $sql = rtrim($sql, 'UNION ')." ORDER by xTicks";
        $db  = new PDO("mysql:host=localhost;dbname=Alarm", "root", "mongs");
        return collect($db->query($sql)->fetchAll());

    }//end getChartData()


    /**
     * 生成HighChart category
     *
     * @param array $rs 查询结果
     *
     * @return array
     */
    public function getHighChartCategory($rs)
    {
        $categories = array();
        foreach ($rs as $item) {
            $category = $item->category;
            if (array_search($category, $categories) === false) {
                $categories[] = $category;
            }
        }

        return $categories;

    }//end getHighChartCategory()


}//end class


/**
 * 差小区概览
 * Class BadCellByCity
 *
 * @category Model
 * @package  App\Http\Controllers\Exporter
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class BadCellByCity extends AbstractModel
{


    /**
     * BadCellByCity constructor.
     *
     * @param Request $request HTTP请求
     */
    public function __construct(NetworkChartsRequest $request)
    {
        $this->request    = $request;
        $this->chartType  = PHPExcel_Chart_DataSeries::TYPE_BARCHART;
        $this->chartTitle = '差小区概览';
        $this->chartData  = $this->getChartData();

    }//end __construct()


    /**
     * 生成图表数据
     *
     * @return mixed
     */
    public function getChartData()
    {
        if ($this->chartData !== null) {
            return $this->chartData;
        }

        $dayId = $this->request->get('day', date_sub(new DateTime(), new DateInterval('P1D'))->format('Y-m-d'));
        $sql   = "SELECT 'badHandoverCell' as series, city as xTicks, count(city) as kpi from badHandoverCell_ex where day_id = '".$dayId."' group by city UNION SELECT 'lowAccessCell' as series, city as xTicks, count(city) as kpi from lowAccessCell_ex where day_id = '".$dayId."' group by city union SELECT 'highLostCell' as series, city as xTicks, count(city) as kpi from highLostCell_ex where day_id = '".$dayId."' group by city";
        $db    = new PDO("mysql:host=localhost;dbname=AutoKPI", "root", "mongs");
        return collect($db->query($sql)->fetchAll());

    }//end getChartData()


}//end class
/**
 * 弱覆盖概览
 * Class WeakCoverOverview
 *
 * @category Model
 * @package  App\Http\Controllers\Exporter
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class WeakCoverOverview  extends AbstractModel
{
    /**
     * WeakCoverOverview constructor.
     *
     * @param Request $request HTTP请求
     */
    public function __construct(NetworkChartsRequest $request)
    {
        $this->request    = $request;
        $this->chartType  = PHPExcel_Chart_DataSeries::TYPE_BARCHART;
        $this->chartTitle = '弱覆盖概览';
        $this->chartData  = $this->getChartData();

    }//end __construct()


    /**
     * 生成图表数据
     *
     * @return mixed
     */
    public function getChartData()
    {
        if ($this->chartData !== null) {
            return $this->chartData;
        }

        $startDate = new DateTime();
        $startDate->sub(new DateInterval('P2M'));
        $endDate = new DateTime();
        $endDate->sub(new DateInterval('P1D'));
        $startDateId = $startDate->format('Y-m-d');
        $endDateId   = $endDate->format('Y-m-d');
        $dbc       = new DataBaseConnection();
        $dataBases = $dbc->getMRDatabases();

        $startDateId = $startDateId." 00:00:00";
        $endDateId   = $endDateId." 00:00:00";

        $test = [];
        $dayObj = array();
        foreach ($dataBases as $dataBase) {
            $db     = $dbc->getDB('MR', $dataBase);
            $city = $dbc->getMRToCity($dataBase);
            $sql    = "select dateId  as xTicks,'$city' as series, SUM(case when ratio110>0.2 then 1 else 0 end)/COUNT(*) as kpi  FROM mroWeakCoverage_day WHERE dateId BETWEEN '".$startDateId."' and '".$endDateId."' GROUP BY dateId ORDER BY dateId";
            $result = $db->query($sql, PDO::FETCH_ASSOC)->fetchAll();
            foreach ($result as $ar) {
                array_push($test, $ar);
                $dayObj[$ar['xTicks']] = $ar['xTicks'];
            }
        }
        ksort($dayObj);
        $temp = array();
        foreach ($dataBases as $dataBase) {
            $city = $dbc->getMRToCity($dataBase);
            foreach ($dayObj as $dateId) {
                   $flag = 0;
                   foreach ($test as $key => $value) {
                       if ($value['xTicks']== $dateId && $value['series'] == $city) {
                                array_push($temp, $value);
                                $flag = 1;
                                break;
                        }
                   }
                   if ($flag == 0) {
                        $arr = array();
                        $arr['xTicks'] = $dateId;
                        $arr['series'] = $city;
                        $arr['kpi'] = null;
                       array_push($temp, $arr);
                   }
                
            }

        }
        return collect($temp);

    }//end getChartData()

}

/**
 * 重叠覆盖概览
 * Class overlapCoverview
 *
 * @category Model
 * @package  App\Http\Controllers\Exporter
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class overlapCoverview  extends AbstractModel
{
    /**
     * overlapCoverview constructor.
     *
     * @param Request $request HTTP请求
     */
    public function __construct(NetworkChartsRequest $request)
    {
        $this->request    = $request;
        $this->chartType  = PHPExcel_Chart_DataSeries::TYPE_BARCHART;
        $this->chartTitle = '重叠覆盖概览';
        $this->chartData  = $this->getChartData();

    }//end __construct()


    /**
     * 生成图表数据
     *
     * @return mixed
     */
    public function getChartData()
    {
        if ($this->chartData !== null) {
            return $this->chartData;
        }

        $startDate = new DateTime();
        $startDate->sub(new DateInterval('P2M'));
        $endDate = new DateTime();
        $endDate->sub(new DateInterval('P1D'));
        $startDateId = $startDate->format('Y-m-d');
        $endDateId   = $endDate->format('Y-m-d');

        $dbc       = new DataBaseConnection();
        $dataBases = $dbc->getMRDatabases();

        $startDateId = $startDateId." 00:00:00";
        $endDateId   = $endDateId." 00:00:00";

        $test = [];
        $dayObj = array();
        foreach ($dataBases as $dataBase) {
            $db     = $dbc->getDB('MR', $dataBase);
            $city = $dbc->getMRToCity($dataBase);
            $sql = "select dateId as xTicks, '$city' as series, SUM(case when rate>0.05 then 1 else 0 end)/COUNT(*) as kpi  FROM mroOverCoverage_day WHERE  dateId BETWEEN '".$startDateId."' and '".$endDateId."' GROUP BY dateId ORDER BY dateId";
            $result = $db->query($sql, PDO::FETCH_ASSOC)->fetchAll();
            foreach ($result as $ar) {
                array_push($test, $ar);
                $dayObj[$ar['xTicks']] = $ar['xTicks'];
            }
        }
        ksort($dayObj);
        $temp = array();
        foreach ($dataBases as $dataBase) {
            $city = $dbc->getMRToCity($dataBase);
            foreach ($dayObj as $dateId) {
                   $flag = 0;
                   foreach ($test as $key => $value) {
                       if ($value['xTicks']== $dateId && $value['series'] == $city) {
                                array_push($temp, $value);
                                $flag = 1;
                                break;
                        }
                   }
                   if ($flag == 0) {
                        $arr = array();
                        $arr['xTicks'] = $dateId;
                        $arr['series'] = $city;
                        $arr['kpi'] = null;
                       array_push($temp, $arr);
                   }
                
            }

        }
        return collect($temp);

    }//end getChartData()

}