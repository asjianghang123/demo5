<?php

/**
 * LocationAndRangingController.php
 *
 * @category SystemManage
 * @package  App\Http\Controllers\SystemManage
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
namespace App\Http\Controllers\SystemManage;

use App\Http\Controllers\Common\DataBaseConnection;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use PDO;
use App\Models\Mongs\SiteLte;

/**
 * 定位测距
 * Class LocationAndRangingController
 *
 * @category SystemManage
 * @package  App\Http\Controllers\SystemManage
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class LocationAndRangingController extends Controller
{


    /**
     * 通过小区名称获取经纬度坐标
     *
     * @return mixed
     */
    public function getCoordinateByCell()
    {
        $cell  = input::get("cell");
        $row = SiteLte::query()->selectRaw('cellName,siteName,tac,longitudeBD AS lng,latitudeBD AS lat,dir,band,10 as count')->where('cellName', $cell)->first()->toArray();
        return json_encode($row);

    }//end getCoordinateByCell()

}//end class
