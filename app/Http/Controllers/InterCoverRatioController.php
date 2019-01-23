<?php

/**
 * InterCoverRatioController.php
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
use App\Models\AutoKPI\InterfereRate_city_day;

/**
 * 干扰小区占比
 * Class InterCoverRatioController
 *
 * @category Controllers
 * @package  App\Http\Controllers
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class InterCoverRatioController extends MyRedis
{


    /**
     * 获得各地市干扰小区占比
     *
     * @return void
     */
    public function searchInterCoverRatio()
    {
        $date   = Input::get('date');
        $dbc    = new DataBaseConnection();
        $citys  = $dbc->getENCityArr();
        $series = array();
        $return = array();
        $rows = InterfereRate_city_day::selectRaw('高干扰小区占比,cityChinese')
                    ->where('day_id', $date)
                    ->whereIn('city', $citys)
                    ->leftJoin('mongs.databaseconn', 'connName', '=', 'city')
                    ->get()
                    ->toArray();
        foreach ($rows as $row) {
            $series['name'] = '';
            $series['data'][] = floatval($row['高干扰小区占比']);
            $return['category'][] = $row['cityChinese'];
        }

        $return['date'] = $date;
        $return['series'][] = $series;
        return json_encode($return);

    }//end SearchInterCoverRatio()


    /**
     * 获得日期列表
     *
     * @return array
     */
    public function getInterTime()
    {
        $dbc    = new DataBaseConnection();
        $db     = $dbc->getDB('mongs', 'AutoKPI');
        $table  = 'interfereRate_city_day';
        $sql    = "select distinct day_id from $table";
        $this->type = 'AutoKPI:interCoverRatio';
        return $this->getValue($db, $sql);

    }//end getInterTime()


}//end class
