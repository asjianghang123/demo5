<?php

/**
 * CarrierByChannel.php
 *
 * @category Exporter
 * @package  App\Http\Controllers\Exporter
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
namespace APP\Http\Controllers\Exporter;

use App\Http\Requests\Request;
use DateInterval;
use DateTime;
use PDO;
use PHPExcel_Chart_DataSeries;
use App\Http\Controllers\Common\DataBaseConnection;
use App\Models\TABLES;

/**
 * 频段维度载波分布
 * Class CarrierByChannel
 *
 * @category Exporter
 * @package  App\Http\Controllers\Exporter
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class CarrierByChannel extends AbstractModel
{


    /**
     * CarrierByChannel constructor.
     *
     * @param Request $request HTTP请求
     */
    public function __construct(Request $request)
    {
        $this->request    = $request;
        $this->chartTitle = "基于频点分布";
        $this->chartType  = PHPExcel_Chart_DataSeries::TYPE_BARCHART;
        $this->chartData  = $this->getChartData();

    }//end __construct()


    /**
     * 生成图表数据
     *
     * @return mixed
     */
    public function getChartData()
    {
        if ($this->chartData != null) {
            return $this->chartData;
        }

        //$day_id = $this->request->get('day', date_sub(new DateTime(), new DateInterval('P1D'))->format('ymd'));
        //$db     = new PDO("mysql:host=192.168.3.144;dbname=kget$day_id", "root", "mongs");
        $rs = TABLES::select('TABLE_SCHEMA')->where('TABLE_NAME', '=', 'TempParameterRRUAndSlaveCount')->where('TABLE_SCHEMA', 'like', 'kget______')->orderBy('TABLE_SCHEMA', 'desc')->first()->toArray();
        $day_id = substr($rs['TABLE_SCHEMA'],4);
        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('mongs', "kget$day_id");
        $sql    = "select '$day_id' as series,t1.band as xTicks, count(*) as kpi FROM (select band, subNetwork from TempParameterRRUAndSlaveCount where band is not null) as  t1 left join (select connName, subNetwork from mongs.databaseconn) as t2
            on LOCATE(t1.subNetwork,t2.subNetwork) > 0 where t2.connName is not null group by series,xTicks";
        $this->chartData = collect($db->query($sql, PDO::FETCH_ASSOC)->fetchAll());
        return $this->chartData;

    }//end getChartData()


}//end class
