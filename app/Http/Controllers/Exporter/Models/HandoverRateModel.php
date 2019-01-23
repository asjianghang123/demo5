<?php

/**
 * HandoverRate.php
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
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use PHPExcel_Chart_DataSeries;

/**
 * 切换成功率
 * Class HandoverRate
 *
 * @category Exporter
 * @package  App\Http\Controllers\Exporter
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class HandoverRate extends AbstractModel
{


    /**
     * HandoverRate constructor.
     *
     * @param Request $request HTTP请求
     */
    public function __construct(Request $request)
    {
        $this->request    = $request;
        $this->chartType  = PHPExcel_Chart_DataSeries::TYPE_LINECHART;
        $this->chartTitle = '切换成功率';
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
        $sql       = "select city as series,day_id as xTicks,CASE city WHEN 'changzhou' THEN if (day_id='2016-11-29',SUM(切换成功率),SUM(切换成功率)/2) WHEN 'nantong' THEN if (day_id<'2016-12-05',SUM(切换成功率),SUM(切换成功率)/2) ELSE SUM(切换成功率) END as kpi from SysCoreTemp_city_day where day_id>='$startTime' and day_id<='$endTime' GROUP BY city,day_id";
        return collect(DB::connection('autokpi')->select($sql));

    }//end getChartData()


}//end class
