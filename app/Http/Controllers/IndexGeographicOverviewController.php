<?php

/**
 * IndexGeographicOverviewController.php
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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use PDO;
use App\Models\AutoKPI\SysCoreTemp_cell_day;
use App\Models\Mongs\SiteLte;

/**
 * RRC用户数和空口下行业务量地理化呈现
 * Class IndexGeographicOverviewController
 *
 * @category Controllers
 * @package  App\Http\Controllers
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class IndexGeographicOverviewController extends Controller
{


    /**
     * 获得地理化数据
     *
     * @return void
     */
    public function getData()
    {
        $cityStr  = input::get("city");
        if ($cityStr == '') {
            $dbc     = new DataBaseConnection();
            $cityStr = $dbc->getCityStr();
        }

        $citys = explode(",", $cityStr);
        $date   = input::get("date");
        $row = SysCoreTemp_cell_day::selectRaw('最大RRC连接用户数,空口下行业务量GB, cell, longitudeBD as longitude, latitudeBD as latitude,dir,band')
            ->where('day_id', $date)
            ->whereIn('SysCoreTemp_cell_day.city', $citys)
            ->leftJoin('mongs.siteLte', 'cellName', '=', 'cell')
            ->get()
            ->toArray();
        echo json_encode($row);

    }//end getData()

    /**
     * 获得小区信息
     *
     * @return void
     */
    public function getCell()
    {
        $cityStr  = input::get("city");
        $citys    = explode(",", $cityStr);

        $date   = input::get("date");
        $cell   = input::get("cell");

        $row = SysCoreTemp_cell_day::selectRaw('最大RRC连接用户数,空口下行业务量GB, cell, longitudeBD as longitude, latitudeBD as latitude,dir,band')
            ->where('day_id', $date)
            ->whereIn('SysCoreTemp_cell_day.city', $citys)
            ->where('cell', $cell)
            ->leftJoin('mongs.siteLte', 'cellName', '=', 'cell')
            ->first()
            ->toArray();

        if ($row) {
            echo json_encode($row);
            return;
        } else {
            echo "false";
        }
    }//end getCell()

}//end class
