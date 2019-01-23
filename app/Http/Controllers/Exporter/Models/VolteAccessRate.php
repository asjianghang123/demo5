<?php

/**
 * VolteAccessRate.php
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
use DB;
use PHPExcel_Chart_DataSeries;

/**
 * VOLTE无线接通率
 * Class VolteAccessRate
 *
 * @category Exporter
 * @package  App\Http\Controllers\Exporter
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class VolteAccessRate extends AbstractModel
{


    /**
     * VolteAccessRate constructor.
     *
     * @param Request $request HTTP请求
     */
    public function __construct(Request $request)
    {
        $this->request    = $request;
        $this->chartType  = PHPExcel_Chart_DataSeries::TYPE_LINECHART;
        $this->chartTitle = 'Volte无线接通率';
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
        $sql       = "select City as series,date_id as xTicks, 100*(SUM(ERAB_NbrSuccEstab_1)/SUM(ERAB_NbrAttEstab_1)*SUM(RRC_SuccConnEstab)/SUM(RRC_AttConnEstab)) as kpi from EutranCellTdd_cell_day where date_id>='$startTime' and date_id<='$endTime' group by date_id,City";
        return collect(DB::connection('nbm')->select($sql));

    }//end getChartData()


}//end class
