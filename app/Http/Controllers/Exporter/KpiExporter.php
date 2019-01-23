<?php

/**
 * KpiExporter.php
 *
 * @category Exporter
 * @package  App\Http\Controllers\Exporter
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
namespace APP\Http\Controllers\Exporter;

use App\Http\Controllers\Controller;
use App\Http\Requests\KpiExportRequest;
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

/**
 * 指标概览报告
 * Class KpiExporter
 *
 * @category Exporter
 * @package  App\Http\Controllers\Exporter
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class KpiExporter extends Controller
{


    /**
     * 导出指标概览报告。
     *
     * @param KpiExportRequest $request KPI导出请求
     *
     * @return string 文件名
     */
    public function export(KpiExportRequest $request)
    {

        // 创建EXCEL文档对象
        $excel = new PHPExcel();

        // 创建Sheet关键三项
        // $excel对象初始包含一个sheet,创建新的sheet使用createSheet方法。
        $sheetKey3 = $excel->getSheet(0);
        $sheetKey3->setTitle('关键三项指标');

        // 创建无线接通率Model
        $access      = new AccessRateModel($request);
        $chartAccess = $access->toExcelChart()->setTopLeftPosition('A1')->setBottomRightPosition('T20');
        $arrayAccess = $access->toExcelArray();
        $sheetKey3->addChart($chartAccess);
        $sheetKey3->fromArray($arrayAccess, null, 'A21');

        // 创建无线掉线率Model
        $lost      = new LostRateModel($request);
        $chartLost = $lost->toExcelChart()->setTopLeftPosition('A27')->setBottomRightPosition('T46');
        $arrayLost = $lost->toExcelArray();
        $sheetKey3->addChart($chartLost);
        $sheetKey3->fromArray($arrayLost, null, 'A47');

        // 创建切换成功率Model
        $handover      = new HandoverRate($request);
        $chartHandover = $handover->toExcelChart()->setTopLeftPosition('A53')->setBottomRightPosition('T72');
        $arrayHandover = $handover->toExcelArray();
        $sheetKey3->addChart($chartHandover);
        $sheetKey3->fromArray($arrayHandover, null, 'A73');

        // 创建VoLte指标Sheet
        $sheetVoLte = $excel->createSheet(1);
        $sheetVoLte->setTitle('VoLte指标');
        // 无线接通率(QCI=1)
        $voLteAccess      = new VolteAccessRate($request);
        $chartVoLteAccess = $voLteAccess->toExcelChart()->setTopLeftPosition('A1')->setBottomRightPosition('T20');
        $arrayVoLteAccess = $voLteAccess->toExcelArray();
        $sheetVoLte->addChart($chartVoLteAccess);
        $sheetVoLte->fromArray($arrayVoLteAccess, null, 'A21');
        // VoLTE用户切换成功率
        $voLteAccess      = new VolteSuccessRate($request);
        $chartVoLteAccess = $voLteAccess->toExcelChart()->setTopLeftPosition('A27')->setBottomRightPosition('T46');
        $arrayVoLteAccess = $voLteAccess->toExcelArray();
        $sheetVoLte->addChart($chartVoLteAccess);
        $sheetVoLte->fromArray($arrayVoLteAccess, null, 'A47');
        // E-RAB掉线率(QCI=1)
        $voLteAccess      = new VolteLostRate($request);
        $chartVoLteAccess = $voLteAccess->toExcelChart()->setTopLeftPosition('A53')->setBottomRightPosition('T72');
        $arrayVoLteAccess = $voLteAccess->toExcelArray();
        $sheetVoLte->addChart($chartVoLteAccess);
        $sheetVoLte->fromArray($arrayVoLteAccess, null, 'A73');
        // eSRVCC切换成功率
        $voLteAccess      = new VolteHSRate($request);
        $chartVoLteAccess = $voLteAccess->toExcelChart()->setTopLeftPosition('A79')->setBottomRightPosition('T98');
        $arrayVoLteAccess = $voLteAccess->toExcelArray();
        $sheetVoLte->addChart($chartVoLteAccess);
        $sheetVoLte->fromArray($arrayVoLteAccess, null, 'A99');

        // 创建Video指标Sheet
        $sheetVideo = $excel->createSheet(2);
        $sheetVideo->setTitle('Video指标');

        // 无线接通率(QCI=2)
        $videoKpi      = new VideoSuccessRate($request);
        $chartVideoKpi = $videoKpi->toExcelChart()->setTopLeftPosition('A1')->setBottomRightPosition('T20');
        $arratVideoKpi = $videoKpi->toExcelArray();
        $sheetVideo->addChart($chartVideoKpi);
        $sheetVideo->fromArray($arratVideoKpi, null, 'A21');

        // E-RAB掉线率(QCI=2)
        $videoKpi      = new VideoLostERABRate($request);
        $chartVideoKpi = $videoKpi->toExcelChart()->setTopLeftPosition('A27')->setBottomRightPosition('T46');
        $arratVideoKpi = $videoKpi->toExcelArray();
        $sheetVideo->addChart($chartVideoKpi);
        $sheetVideo->fromArray($arratVideoKpi, null, 'A47');

        // VideoCall用户切换成功率
        $videoKpi      = new VideoHandoverCallRate($request);
        $chartVideoKpi = $videoKpi->toExcelChart()->setTopLeftPosition('A53')->setBottomRightPosition('T72');
        $arratVideoKpi = $videoKpi->toExcelArray();
        $sheetVideo->addChart($chartVideoKpi);
        $sheetVideo->fromArray($arratVideoKpi, null, 'A73');

        // eSRVCC切换成功率
        $videoKpi      = new VideoESRVCCRate($request);
        $chartVideoKpi = $videoKpi->toExcelChart()->setTopLeftPosition('A79')->setBottomRightPosition('T98');
        $arratVideoKpi = $videoKpi->toExcelArray();
        $sheetVideo->addChart($chartVideoKpi);
        $sheetVideo->fromArray($arratVideoKpi, null, 'A99');

        // 创建ExcelWriter
        $writer = new PHPExcel_Writer_Excel2007($excel);
        $writer->setIncludeCharts(true);

        $writer->save('NetworkKpi.xlsx');
        return 'NetworkKpi.xlsx';

    }//end export()


}//end class


/**
 * VideoESRVCC切换成功率
 * Class VideoESRVCCRate
 *
 * @category Exporter
 * @package  App\Http\Controllers\Exporter
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class VideoESRVCCRate extends AbstractModel
{


    /**
     * VideoESRVCCRate constructor.
     *
     * @param Request $request HTTP请求
     */
    public function __construct(Request $request)
    {
        $this->request    = $request;
        $this->chartType  = PHPExcel_Chart_DataSeries::TYPE_LINECHART;
        $this->chartTitle = 'eSRVCC切换成功率';
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

        $startTime = $this->request->get(
            'startTime',
            date_sub(new DateTime(), new DateInterval('P15D'))
            ->format('Y-m-d')
        );
        $endTime   = $this->request->get('endTime', date('Y-m-d'));
        $sql       = "select City as series,date_id as xTicks, 100 * (SUM(HO_SuccOutInterEnbS1_2) + SUM(HO_SuccOutInterEnbX2_2) + SUM(HO_SuccOutIntraEnb_2)) / (SUM(HO_AttOutInterEnbS1_2) + SUM(HO_AttOutInterEnbX2_2) + SUM(HO_AttOutIntraEnb_2))as kpi from EutranCellTdd_cell_day where date_id>='$startTime' and date_id<='$endTime' group by date_id,City";
        return collect(DB::connection('nbm')->select($sql));

    }//end getChartData()


}//end class


/**
 * VideoCall用户切换成功
 * Class VideoHandoverCallRate
 *
 * @category Model
 * @package  App\Http\Controllers\Exporter
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class VideoHandoverCallRate extends AbstractModel
{


    /**
     * 构造方法
     *
     * VideoHandoverCallRate constructor.
     *
     * @param Request $request HTTP请求
     */
    public function __construct(Request $request)
    {
        $this->request    = $request;
        $this->chartType  = PHPExcel_Chart_DataSeries::TYPE_LINECHART;
        $this->chartTitle = 'VideoCall用户切换成功率';
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

        $startTime = $this->request->get(
            'startTime',
            date_sub(new DateTime(), new DateInterval('P15D'))
            ->format('Y-m-d')
        );
        $endTime   = $this->request->get('endTime', date('Y-m-d'));
        $sql       = "select City as series,date_id as xTicks, 100 * SUM(IRATHO_SuccOutGeran) / SUM(IRATHO_AttOutGeran) as kpi from EutranCellTdd_cell_day where date_id>='$startTime' and date_id<='$endTime' group by date_id,City";
        return collect(DB::connection('nbm')->select($sql));

    }//end getChartData()


}//end class


/**
 * E-RAB掉线率(QCI=2)
 * Class VideoLostERABRate
 *
 * @category Model
 * @package  App\Http\Controllers\Exporter
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class VideoLostERABRate extends AbstractModel
{


    /**
     * VideoLostERABRate constructor.
     *
     * @param Request $request HTTP请求
     */
    public function __construct(Request $request)
    {
        $this->request    = $request;
        $this->chartType  = PHPExcel_Chart_DataSeries::TYPE_LINECHART;
        $this->chartTitle = 'E-RAB掉线率(QCI=2)';
        $this->chartData  = $this->getChartData();

    }//end __construct()


    /**
     * 获得图表数据
     *
     * @return mixed
     */
    public function getChartData()
    {
        if ($this->chartData !== null) {
            return $this->chartData;
        }

        $startTime = $this->request->get(
            'startTime',
            date_sub(new DateTime(), new DateInterval('P15D'))
            ->format('Y-m-d')
        );
        $endTime   = $this->request->get('endTime', date('Y-m-d'));
        $sql       = "select City as series,date_id as xTicks, 100 * (SUM(ERAB_NbrReqRelEnb_2) - SUM(ERAB_NbrReqRelEnb_Normal_2) + SUM(ERAB_HoFail_2)) / SUM(ERAB_NbrSuccEstab_2) as kpi from EutranCellTdd_cell_day where date_id>='$startTime' and date_id<='$endTime' group by date_id,City";
        return collect(DB::connection('nbm')->select($sql));

    }//end getChartData()


}//end class


/**
 * 无线接通率(QCI=2)
 * Class VideoSuccessRate
 *
 * @category Model
 * @package  App\Http\Controllers\Exporter
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class VideoSuccessRate extends AbstractModel
{


    /**
     * VideoSuccessRate constructor.
     *
     * @param Reuest $request HTTP请求
     */
    public function __construct(Request $request)
    {
        $this->request    = $request;
        $this->chartType  = PHPExcel_Chart_DataSeries::TYPE_LINECHART;
        $this->chartTitle = '无线接通率(QCI=2)';
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

        $startTime = $this->request->get(
            'startTime',
            date_sub(new DateTime(), new DateInterval('P15D'))
            ->format('Y-m-d')
        );
        $endTime   = $this->request->get('endTime', date('Y-m-d'));
        $sql       = "select City as series,date_id as xTicks, 100 * (SUM(ERAB_NbrSuccEstab_2) / SUM(ERAB_NbrAttEstab_2) * SUM(RRC_SuccConnEstab) / SUM(RRC_AttConnEstab)) as kpi from EutranCellTdd_cell_day where date_id>='$startTime' and date_id<='$endTime' group by date_id,City";
        return collect(DB::connection('nbm')->select($sql));

    }//end getChartData()


}//end class


/**
 * ESRVCC切换成功率
 * Class VolteHSRate
 *
 * @category Model
 * @package  App\Http\Controllers\Exporter
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class VolteHSRate extends AbstractModel
{


    /**
     * ESRVCC切换成功率
     *
     * VolteHSRate constructor.
     *
     * @param Request $request HTTP请求
     */
    public function __construct(Request $request)
    {
        $this->request    = $request;
        $this->chartType  = PHPExcel_Chart_DataSeries::TYPE_LINECHART;
        $this->chartTitle = 'eSRVCC切换成功率';
        $this->chartData  = $this->getChartData();

    }//end __construct()


    /**
     * 获得图表数据
     *
     * @return mixed
     */
    public function getChartData()
    {
        if ($this->chartData !== null) {
            return $this->chartData;
        }

        $startTime = $this->request->get(
            'startTime',
            date_sub(new DateTime(), new DateInterval('P15D'))
            ->format('Y-m-d')
        );
        $endTime   = $this->request->get('endTime', date('Y-m-d'));
        $sql       = "select City as series,date_id as xTicks, 100 * SUM(IRATHO_SuccOutGeran) / SUM(IRATHO_AttOutGeran) as kpi from EutranCellTdd_cell_day where date_id>='$startTime' and date_id<='$endTime' group by date_id,City";
        return collect(DB::connection('nbm')->select($sql));

    }//end getChartData()


}//end class


/**
 * E-RAB掉线率(QCI=1)
 * Class VolteLostRate
 *
 * @category Model
 * @package  App\Http\Controllers\Exporter
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class VolteLostRate extends AbstractModel
{


    /**
     * VolteLostRate constructor.
     *
     * @param Request $request HTTP请求
     */
    public function __construct(Request $request)
    {
        $this->request    = $request;
        $this->chartType  = PHPExcel_Chart_DataSeries::TYPE_LINECHART;
        $this->chartTitle = 'E-RAB掉线率(QCI=1)';
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

        $startTime = $this->request->get(
            'startTime',
            date_sub(new DateTime(), new DateInterval('P15D'))
            ->format('Y-m-d')
        );
        $endTime   = $this->request->get('endTime', date('Y-m-d'));
        $sql       = "select City as series,date_id as xTicks, 100 * (SUM(ERAB_NbrReqRelEnb_1) - SUM(ERAB_NbrReqRelEnb_Normal_1) + SUM(ERAB_HoFail_1)) / SUM(ERAB_NbrSuccEstab_1) as kpi from EutranCellTdd_cell_day where date_id>='$startTime' and date_id<='$endTime' group by date_id,City";
        return collect(DB::connection('nbm')->select($sql));

    }//end getChartData()


}//end class


/**
 * VoLTE用户切换成功率
 * Class VolteSuccessRate
 *
 * @category Model
 * @package  App\Http\Controllers\Exporter
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class VolteSuccessRate extends AbstractModel
{


    /**
     * VolteSuccessRate constructor.
     *
     * @param Request $request HTTP请求
     */
    public function __construct(Request $request)
    {
        $this->request    = $request;
        $this->chartType  = PHPExcel_Chart_DataSeries::TYPE_LINECHART;
        $this->chartTitle = 'VoLTE用户切换成功率';
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

        $startTime = $this->request->get(
            'startTime',
            date_sub(new DateTime(), new DateInterval('P15D'))
            ->format('Y-m-d')
        );
        $endTime   = $this->request->get('endTime', date('Y-m-d'));
        $sql       = "select City as series,date_id as xTicks, 100 * (SUM(HO_SuccOutInterEnbS1_1) + SUM(HO_SuccOutInterEnbX2_1) + SUM(HO_SuccOutIntraEnb_1)) / (SUM(HO_AttOutInterEnbS1_1) + SUM(HO_AttOutInterEnbX2_1) + SUM(HO_AttOutIntraEnb_1)) as kpi from EutranCellTdd_cell_day where date_id>='$startTime' and date_id<='$endTime' group by date_id,City";
        return collect(DB::connection('nbm')->select($sql));

    }//end getChartData()


}//end class
