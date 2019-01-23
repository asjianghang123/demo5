<?php

/**
 * IModel.php
 *
 * @category Exporter
 * @package  App\Http\Controllers\Exporter
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
namespace APP\Http\Controllers\Exporter;

use Illuminate\Database\Eloquent\Collection;

/**
 * Interface IModel
 *
 * @category Exporter
 * @package  App\Http\Controllers\Exporter
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 *
 * This interface define a model which we want to write to excel/pdf/....
 * The model should be a chart or a array (or other style in future).
 */
interface IModel
{


    /**
     * 生成Excel图表
     *
     * @return mixed
     */
    function toExcelChart();


    /**
     * 生成Excel表格
     *
     * @return mixed
     */
    function toExcelArray();


    /**
     * 返回Model所需数据，返回格式应该为一个约定的数组。
     * 格式:
     * [
     *   {series:'series1',xTick:'tick1',kpi:'kpi1'},
     *   .
     *   .
     *   .
     *   {series:'seriesN',xTick:'tickN',kpi:'kpiN'}
     * ]
     * series:序列名
     * xTick: X值
     * kpi: 指标值
     *
     * @return mixed
     */
    function getChartData();


}//end interface
