<?php

/**
 * BscVersionByType.php
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
use Illuminate\Support\Collection;
use PDO;
use PHPExcel_Chart_DataSeries;
use App\Http\Controllers\Common\DataBaseConnection;
use App\Models\TABLES;
/**
 * 类型维度基站版本分布
 * Class BscVersionByType
 *
 * @category Exporter
 * @package  App\Http\Controllers\Exporter
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class BscVersionByType extends AbstractModel
{


    /**
     * BscVersionByType constructor.
     *
     * @param Request $request HTTP请求
     */
    public function __construct(Request $request)
    {
        $this->request    = $request;
        $this->chartTitle = "基于类型分布";
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

        $day_id = $this->request->get('day', date_sub(new DateTime(), new DateInterval('P1D'))->format('ymd'));
        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('mongs', "kget$day_id");
        $sql    = "select softwareVersion as series, siteType as xTicks, count(*) as kpi from TempSiteVersion where softwareVersion is not null and softwareVersion !='' and softwareVersion !='!!!!' and siteType is not null group by siteType,softwareVersion order by softwareVersion";
        $this->chartData = collect($db->query($sql)->fetchAll());
        return $this->chartData;

    }//end getChartData()


}//end class
