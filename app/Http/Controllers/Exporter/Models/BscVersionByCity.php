<?php

/**
 * BscVersionByCity.php
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
 * 城市维度基站版本分布
 * Class BscVersionByCity
 *
 * @category Exporter
 * @package  App\Http\Controllers\Exporter
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class BscVersionByCity extends AbstractModel
{


    /**
     * BscVersionByCity constructor.
     *
     * @param Request $request HTTP请求
     */
    public function __construct(Request $request)
    {
        $this->request    = $request;
        $this->chartType  = PHPExcel_Chart_DataSeries::TYPE_BARCHART;
        $this->chartTitle = "基站类型分布(基于城市)";
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

        $day_id     = $this->request->get('day', date_sub(new DateTime(), new DateInterval('P1D'))->format("ymd"));
        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('mongs', "kget$day_id");
        $categories = array();
        $sql        = "select SUBSTRING_INDEX(UP_CompatibilityIndex,'_',1) as category from UpgradePackage where UpgradePackageId in (select substring_index(currentUpgradePackage,'=',-1) from ConfigurationVersion) and left(UP_CompatibilityIndex,4) != '!!!!' and left(UP_CompatibilityIndex,4) != '' group by SUBSTRING_INDEX(UP_CompatibilityIndex,'_',1) order by SUBSTRING_INDEX(UP_CompatibilityIndex,'_',1)";
        $rs         = $db->query($sql)->fetchAll(PDO::FETCH_OBJ);
        foreach ($rs as $item) {
            $category = $item->category;
            if (array_search($category, $categories) === false) {
                $categories[] = $category;
            }
        }

        $cities = array();
        $sql    = "select connName as city, subNetwork as subNet from mongs.databaseconn";
        $rs     = $db->query($sql)->fetchAll(PDO::FETCH_OBJ);
        foreach ($rs as $item) {
            array_push($cities,  $item->city);
        }

        $sql   = "select t2.city as series, t3.version as xTicks, count(*) as kpi  from (select subNetwork,substring_index(currentUpgradePackage,'=',-1) as currentUpgradePackage from ConfigurationVersion) as t1 left join  
                (select connName as city, concat(subNetwork,subNetworkFdd) as subNet from mongs.databaseconn) as  t2 on LOCATE(t1.subNetwork,t2.subNet) > 0
                left join (select UpgradePackageId,SUBSTRING_INDEX(UP_CompatibilityIndex,'_',1) as version from UpgradePackage group by UpgradePackageId) as t3 on t3.UpgradePackageId=t1.currentUpgradePackage
                where t3.version != \"\" and LOCATE(\"!!!!\",t3.version) = 0 and t2.city is not null group by series,xTicks";
        $rs    = $db->query($sql)->fetchAll();
        $newRs = array();
        foreach ($cities as $city) {
            foreach ($categories as $category) {
                $flag = true;
                foreach ($rs as $items) {
                    if ($items['series'] == $city and $items['xTicks'] == $category) {
                        array_push($newRs, $items);
                        $flag = false;
                    }
                }

                if ($flag) {
                    $items           = array();
                    $items['series'] = $city;
                    $items['xTicks'] = $category;
                    $items['kpi']    = 0;
                    array_push($newRs, $items);
                }
            }
        }

        $this->chartData = collect($newRs);
        return $this->chartData;

    }//end getChartData()


}//end class
