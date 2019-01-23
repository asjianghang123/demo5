<?php

/**
 * NetworkChartsTimerController.php
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
use App\Models\AutoKPI\SysCoreTemp_city_hour;
use App\Models\NBM\EutranCellTdd_city_Hour;

/**
 * 获得当前时间
 * Class NetworkChartsTimerController
 *
 * @category Controllers
 * @package  App\Http\Controllers
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class NetworkChartsTimerController extends Controller
{


    /**
     * 获得当前时间
     *
     * @return void
     */
    public function getNowTime()
    {
        $row   = SysCoreTemp_city_hour::orderBy('id', 'desc')->first()->toArray();
        $arr[] = $row['hour_id'];
        $row   = EutranCellTdd_city_Hour::groupBy(['date_id','hour_id'])->orderBy('date_id', 'desc')->orderBy('hour_id', 'desc')->first()->toArray();
        $arr[] = $row['hour_id'];
        $arr[] = $row['hour_id'];
        echo json_encode($arr);

    }//end getNowTime()


}//end class
