<?php

/**
 * LTENeighRationalityAnalysisController.php
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
 * Class LTENeighRationalityAnalysisController
 * 4G邻区合理性分析
 *
 * @category NetworkOptimization
 * @package  App\Http\Controllers\NetworkOptimization
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class LTENeighRationalityAnalysisController extends Controller
{


    /**
     * 获取4G邻区合理性分析结果
     *
     * @return string 4G邻区合理性分析检查结果(JSON)
     */
    public function getLTENeighRationalityData()
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

            $sql = "select count(*) count from TempEUtranCellRelation where city='$city'";
            $rs  = $db->query($sql, PDO::FETCH_ASSOC);
            if ($rs) {
                $occurs = $rs->fetchColumn();
            } else {
                $occurs = 0;
            }

            $dbname_city = $dbc->getMRDatabase($city);
            $db          = $dbc->getDB('MR', $dbname_city);
            $sql_city    = "select count(DISTINCT a.ecgi,a.ecgiNeigh_direct) count_city from (select ecgi,ecgiNeigh_direct from mreServeNeigh where datetime_id like '$dateTime%' union  select ecgi,ecgiNeigh_direct from mroServeNeigh where datetime_id like '$dateTime%')a ";
            $rs          = $db1->query($sql_city, PDO::FETCH_ASSOC);
            if ($rs) {
                $occurs_city = $rs->fetchColumn();
            } else {
                $data['message'] = "数据不存在，请重新选择！";
                return json_encode($data);
            }

            if ($occurs_city == 0) {
                $data['message'] = "数据不存在，请重新选择！";
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

    }//end getLTENeighRationalityData()


    /**
     * 获得数据库名
     *
     * @param string $city 城市名
     *
     * @return string 数据库名
     */
    public function getMRDatabase($city)
    {
        $dbc = new DataBaseConnection();
        return $dbc->getMRDatabase($city);

    }//end getMRDatabase()


    /**
     * 获得日期(天)列表
     *
     * @return array 日期(天)列表
     */
    public function getfdfdg()
    {
        $sql = "SELECT cityChinese FROM databaseconn limit 1;";
        $dbc = new DataBaseConnection();
        $db = $dbc->getDB('mongs', 'mongs');
        $city = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC)[0]['cityChinese'];
        $dbname = $dbc->getMRDatabase($city);
        // $dbname = 'MR_CZ';
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

    }//end getfdfdg()


}//end class
