<?php

/**
 * BscByCA.php
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
 * CA维度基站分布
 * Class BscByCA
 *
 * @category Exporter
 * @package  App\Http\Controllers\Exporter
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class BscByCA extends AbstractModel
{


    /**
     * BscByCA constructor.
     *
     * @param Request $request HTTP请求
     */
    public function __construct(Request $request)
    {
        $this->request    = $request;
        $this->chartType  = PHPExcel_Chart_DataSeries::TYPE_PIECHART;
        $this->chartTitle = '基于CA分布';
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

        $day_id = $this->request->get('day', date_sub(new DateTime(), new DateInterval('P1D'))->format('ymd'));
        $sql    = "select '$day_id' as series,if(OptionalFeatureLicenseId='CarrierAggregation' and serviceState=1,'CA','非CA') as xTicks,count(*) as kpi from OptionalFeatureLicense group by series,xTicks";
        //$db     = new PDO("mysql:host=192.168.3.144;dbname=kget$day_id", "root", "mongs");
        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('mongs', "kget$day_id");
        return collect($db->query($sql)->fetchAll());

    }//end getChartData()


}//end class
