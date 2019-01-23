<?php

/**
 * AbstractModel.php
 *
 * @category Exporter
 * @package  App\Http\Controllers\Exporter
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
namespace APP\Http\Controllers\Exporter;
use App\Http\Requests\Request;
use Illuminate\Support\Collection;
use PHPExcel_Chart;
use PHPExcel_Chart_DataSeries;
use PHPExcel_Chart_DataSeriesValues;
use PHPExcel_Chart_Layout;
use PHPExcel_Chart_Legend;
use PHPExcel_Chart_PlotArea;
use PHPExcel_Chart_Title;

/**
 * 抽象类
 * Class AbstractModel
 *
 * @category Exporter
 * @package  App\Http\Controllers\Exporter
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
abstract class AbstractModel implements IModel
{
    /**
     * 图表类型
     * 
     * @var PHPExcel_Chart_DataSeries $chartType 图表类型
     */
    protected $chartType;

    /**
     * 图表数据
     *
     * @var Collection $chartData 图表数据
     */
    protected $chartData;

    /**
     * 图表标题
     *
     * @var string $chartTitle 图表标题
     */
    protected $chartTitle;

    /**
     * HTTP请求
     *
     * @var Request $request HTTP请求
     */
    protected $request;


    /**
     * 创建ExcelChart。
     * 在默认情况下，只考虑最简单的XYChart。
     * 若需求更加复杂的chart,请在子类中重写该方法。
     *
     * @return mixed
     */
    public function toExcelChart()
    {

        // 创建dataSeriesLabels
        $dataSeriesLabels = array();
        $series           = $this->chartData->pluck('series')->unique();
        foreach ($series as $item) {
            $dataSeriesLabels[] = new PHPExcel_Chart_DataSeriesValues('String', null, null, 1, array($item));
        }

        unset($series);

        // 创建xTickValues
        $xTicks          = $this->chartData->pluck('xTicks')->unique();
        $xAxisTickValues = array(new PHPExcel_Chart_DataSeriesValues('String', null, null, $xTicks->count(), $xTicks->toArray()));
        unset($xTicks);

        // 创建dataSeriesValues
        $group            = $this->chartData->groupBy('series');
        $dataSeriesValues = array();
        foreach ($group as $key => $value) {
            $dataSeriesValues[] = new PHPExcel_Chart_DataSeriesValues('Number', null, null, $value->count(), $value->pluck('kpi')->toArray());
        }

        unset($group);

        // 创建dataSeries
        $series = new PHPExcel_Chart_DataSeries(
            $this->chartType,
            // PHPExcel_Chart_DataSeries::GROUPING_STANDARD,
            null,
            range(0, (count($dataSeriesValues) - 1)),
            $dataSeriesLabels,
            $xAxisTickValues,
            $dataSeriesValues
        );

        // 创建plotArea
        $layout = new PHPExcel_Chart_Layout();
        $layout->setShowPercent(true);
        $plotArea = new PHPExcel_Chart_PlotArea($layout, array($series));

        // 创建legend
        $legend = new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_BOTTOM, null, false);

        // 设置chartTitle.
        $title = new PHPExcel_Chart_Title($this->chartTitle);

        // 设置y-Axis Label.
        $yAxisLabel = new PHPExcel_Chart_Title('Value');

        // 创建chart.
        return new PHPExcel_Chart(
            'AccessRate',
            $title,
            $legend,
            $plotArea,
            true,
            0,
            null,
            $yAxisLabel
        );

    }//end toExcelChart()


    /**
     * 创建指定格式的数组，可直接写入Excel表格
     * 数组格式
     * [
     * {'',xTick1,xTick2...xTickN},
     * {series1,number1_1,number1_2,...},
     * {seriesN,number_N_1,number_N_2,...}
     * ]
     *
     * @return array
     */
    public function toExcelArray()
    {
        $chartData = $this->getChartData();
        $data      = array();
        // 第一行
        $data[] = array_merge([''], $chartData->pluck('xTicks')->unique()->toArray());
        // 数据行
        foreach ($chartData->groupBy('series') as $key => $value) {
            $data[] = array_merge([$key], $value->pluck('kpi')->toArray());
        }

        return $data;

    }//end toExcelArray()


}//end class
