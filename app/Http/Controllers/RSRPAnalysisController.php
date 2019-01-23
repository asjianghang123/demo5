<?php

/**
 * RSRPAnalysisController.php
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
use App\Models\Mongs\Databaseconns;
use App\Models\MR\MroWeakCoverage_day;
use App\Models\Mongs\SiteLte;

/**
 * RSRP分析
 * Class RSRPAnalysisController
 *
 * @category Controllers
 * @package  App\Http\Controllers
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class RSRPAnalysisController extends MyRedis
{

    /**
     * 获得RSRP数据
     *
     * @return array
     */
    public function getRSRPdate()
    {
        $dbc = new DataBaseConnection();
        $city = Databaseconns::first()->cityChinese;
        $dbname = $dbc->getMRDatabase($city);
        
        $db     = $dbc->getDB('MR', $dbname);
        $table  = 'mroWeakCoverage_day';
        $result = array();
        $sql    = "select distinct dateId from $table";
        $this->type = $dbname.':RSRPAnalysis';
        return $this->getValue($db, $sql);

    }//end getRSRPdate()


    /**
     * 获得RSRP分析数据
     *
     * @return mixed
     */
    public function getRSRPAnalysisData()
    {
        $date      = Input::get('date');
        $cell      = Input::get('cell');
        $dbc       = new DataBaseConnection();

        $site = SiteLte::where('cellName', $cell);
        if (!$site->exists()) {
            return [];
        }
        $site = $site->first();
        $dataBase = $dbc->getMRDatabaseByCity($site->city);
        $conn = new MroWeakCoverage_day;
        $ecgi =  substr($site->ecgi, 5);//去掉前面的4600-
        $conn = $conn->setConnection($dataBase)->where('dateId', $date)->where('ecgi', $ecgi)->first();
        if (!$conn) {
            return [];
        }
        $row = $conn->toArray();
        $series         = array();
        $series['name'] = "A".$row['ecgi']."A";
        $series['data'] = array(
                           $row['numLess80'],
                           $row['numLess80_90'],
                           $row['numLess90_100'],
                           $row['numLess100_110'],
                           $row['numLess110'],
                          );
        $items = array();
        array_push($items, $series);
        return response()->json($items);
    }//end getRSRPAnalysisData()


}//end class
