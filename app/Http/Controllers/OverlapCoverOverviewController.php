<?php

/**
 * OverlapCoverOverviewController.php
 *
 * @category Controllers
 * @package  App\Http\Controllers
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
namespace App\Http\Controllers;

use App\Http\Controllers\Common\DataBaseConnection;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Common\MyRedis;
use PDO;
use App\Models\MR\MroOverCoverage_day;

/**
 * 重叠覆盖概览
 * Class OverlapCoverOverviewController
 *
 * @category Controllers
 * @package  App\Http\Controllers
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class OverlapCoverOverviewController extends MyRedis
{


    /**
     * 获得重叠覆盖概览数据
     *
     * @return string
     */
    public function SearchOverlapCoverOverview()
    {
        $date      = Input::get('date');
        $dbc       = new DataBaseConnection();
        $dataBases = $dbc->getMRDatabases();
        $series    = array();
        $return    = array();

        foreach ($dataBases as $dataBase) {
            $conn = MroOverCoverage_day::on($dataBase)->where('dateId', $date);
            $total = $conn->count();
            if ($total == 0) {
                $value = 0;
            } else {
                $num = $conn->where('rate', '>', '0.05')->count();
                $value = floatval($num/$total)*100;
            }
            $series['name']   = '';
            $series['data'][] = $value;
            $return['category'][] = $dbc->getMRToCHName($dataBase);
        }

        $return['date']     = $date;
        $return['series'][] = $series;
        return json_encode($return);

    }//end SearchOverlapCoverOverview()


    /**
     * 获得日期列表
     *
     * @return array
     */
    public function getBusyTime()
    {
        $dbc       = new DataBaseConnection();
        $dataBases = $dbc->getMRDatabases();
        foreach ($dataBases as $dataBase) {
            $dbc    = new DataBaseConnection();
            $db     = $dbc->getDB('MR', $dataBase);
            $result = array();
            $table  = 'mroOverCoverage_day';

            $sql  = "select distinct dateId from $table where rate > 0.05";
            $this->type = $dataBase.':overlapCoverOverview';
            return $this->getValue($db, $sql);
        }//end foreach

    }//end getBusyTime()


}//end class
