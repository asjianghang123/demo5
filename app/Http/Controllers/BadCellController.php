<?php

/**
 * BadCellController.php
 *
 * @category Controllers
 * @package  App\Http\Controllers
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
namespace App\Http\Controllers;

use App\Http\Requests;
use DateInterval;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

/**
 * 坏小区统计
 * Class BadCellController
 *
 * @category Controllers
 * @package  App\Http\Controllers
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class BadCellController extends Controller
{


    /**
     * 获得坏小区统计结果
     *
     * @return string
     */
    public function getBadCellData()
    {
        $date = new DateTime();
        $date->sub(new DateInterval('P1D'));
        $dayId  = $date->format('Y-m-d');
        $sql    = 'select city,count(city) as num,"badHandoverCell" as type from badHandoverCell_ex where day_id = "'.$dayId.'" group by city union select city,count(city) as num,"highLostCell" as type from highLostCell_ex where day_id = "'.$dayId.'" group by city union select city,count(city) as num,"lowAccessCell" as type from lowAccessCell_ex where day_id = "'.$dayId.'" group by city';
        $result = DB::connection('autokpi')->select($sql);
        return $this->getHighChartData($result);

    }//end getBadCellData()


    /**
     * 生成HighChart图表数据
     *
     * @param array $result 查询结果
     *
     * @return string
     */
    public function getHighChartData($result)
    {
        $series   = array();
        $category = array();
        foreach ($result as $item) {
            $city = $item->city;
            $num  = $item->num;
            $type = $item->type;
            if (array_search($city, $category) === false) {
                $category[] = $city;
            }

            if (!array_key_exists($type, $series)) {
                $series[$type] = array();
            }

            $series[$type][] = floatval($num);
        }

        $data['category'] = $category;
        $data['series']   = array();
        foreach ($series as $key => $value) {
            $data['series'][] = [
                                 'name' => $key,
                                 'data' => $value,
                                ];
        }

        return json_encode($data);

    }//end getHighChartData()


    /**
     * 生成DirlDown数据
     *
     * @return string
     */
    function getDrillDownDonutPie()
    {
        $city = Input::get('city');
        $date = new DateTime();
        $date->sub(new DateInterval('P1D'));
        $dayId = $date->format('Y-m-d');
        $sql   = 'select city,count(city) as num,"badHandoverCell" as type from badHandoverCell_ex where day_id = "'.$dayId.'" and city="'.$city.'" union select city,count(city) as num,"highLostCell" as type from highLostCell_ex where day_id = "'.$dayId.'" and city="'.$city.'" union select city,count(city) as num,"lowAccessCell" as type from lowAccessCell_ex where day_id = "'.$dayId.'" and city="'.$city.'"';
        $conn  = DB::connection('autokpi');
        $rs    = $conn->select($sql);
        $data  = array();
        $badCellType_Data = array();
        foreach ($rs as $item) {
            $arr        = array();
            $type       = $item->type;
            $num        = $item->num;
            $arr[$type] = floatval($num);
            foreach ($arr as $key => $value) {
                $badCellType_Data[] = [
                                       'name' => $key,
                                       'y'    => $value,
                                      ];
            }
        }

        $data['badCellType_Data'] = $badCellType_Data;
        return json_encode($data);

    }//end getDrillDownDonutPie()


}//end class
