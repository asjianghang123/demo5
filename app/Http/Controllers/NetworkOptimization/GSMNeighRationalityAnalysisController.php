<?php

/**
 * GSMNeighRationalityAnalysisController.php
 *
 * @category NetworkOptimization
 * @package  App\Http\Controllers\NetworkOptimization
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
namespace App\Http\Controllers\NetworkOptimization;

use App\Http\Controllers\Common\DataBaseConnection;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use PDO;

/**
 * 2G邻区合理性分析
 * Class GSMNeighRationalityAnalysisController
 *
 * @category NetworkOptimization
 * @package  App\Http\Controllers\NetworkOptimization
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class GSMNeighRationalityAnalysisController extends Controller
{


    /**
     * 获取2G邻区合理性分析数据
     *
     * @return string 2G邻区合理性检查结果
     */
    public function getGSMNeighRationalityData()
    {
        $dateTime = Input::get('dateTime');
        $dbname   = 'Global';
        $dbc      = new DataBaseConnection();
        $db       = $dbc->getDB('MR', $dbname);
        $res      = $dbc->getCityCategories();
        $category = array();
        foreach ($res as $items) {
            $city = $items->category;
            if (array_search($city, $category) === false) {
                $category[] = $city;
            }

            $sql = "select count(*) count from TempGeranCellRelation where city='$city'";
            $rs  = $db->query($sql, PDO::FETCH_ASSOC);
            if ($rs) {
                $occurs = $rs->fetchColumn();
            } else {
                $occurs = 0;
            }

            $dbname_city = $dbc->getMRDatabaseByCity($city);
            $dsn1        = "mysql:host=10.40.57.190:8066;dbname=$dbname_city";
            $db1         = new PDO($dsn1, 'mr', 'mr');
            $sql_city    = "select count(*) from mreServerNeighIrat where datetime_id like '$dateTime%'";
            $rs          = $db1->query($sql_city, PDO::FETCH_ASSOC);
            if ($rs) {
                $occurs_city = $rs->fetchColumn();
            } else {
                $data['message'] = $dateTime."暂无数据";
                return json_encode($data);
            }

            if ($occurs_city == 0) {
                $data['message'] = $dateTime."暂无数据";
                return json_encode($data);
            }

            $seriesData[] = (floatval($occurs) / floatval($occurs_city) * 100);
        }//end foreach

        $data['category'] = $category;
        $data['series']   = array();
        $data['series'][] = [
                             'name' => '有效率',
                             'data' => $seriesData,
                            ];
        return json_encode($data);

    }//end getGSMNeighRationalityData()


    /**
     * 获得日期(天)列表
     *
     * @return array 日期(天)列表
     */
    public function getfdfdi()
    {
        $dbname = 'MR_CZ';
        $dbc    = new DataBaseConnection();
        $db     = $dbc->getDB('MR', $dbname);
        $table  = 'mreServerNeighIrat';
        $result = array();
        $sql    = "select distinct datetime_id from $table";
        $rs     = $db->query($sql, PDO::FETCH_ASSOC);

        $test = [];
        if ($rs) {
            $rows = $rs->fetchall();
            if (count($rows) > 0) {
                foreach ($rows as $row) {
                    $arr = explode(' ', $row['datetime_id']);
                    if ($arr[0] == '0000-00-00') {
                        continue;
                    }

                    array_push($test, $arr[0]);
                }

                return $test;
            } else {
                $result['error'] = 'error';
                return $result;
            }
        } else {
            $result['error'] = 'error';
            return $result;
        }//end if

    }//end getfdfdi()


}//end class
