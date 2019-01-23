<?php

/**
 * ActiveUserController.php
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
use App\Models\Mongs\AccessDetail;

/**
 * 访问流量统计
 * Class ActiveUserController
 *
 * @category SystemManage
 * @package  App\Http\Controllers\SystemManage
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class ActiveUserController extends Controller
{


    /**
     * 获得日活用户
     *
     * @return void
     */
    public function getAccessData()
    {
        $startDate = input::get("startDate");
        $endDate   = input::get("endDate");
        $row = AccessDetail::query()->selectRaw('date_id,count(DISTINCT user) as num')
                ->where('date_id', '>=', $startDate)
                ->where('date_id', '<=', $endDate)
                ->groupBy('date_id')
                ->orderBy('date_id', 'asc')
                ->get()
                ->toArray();
        $dates = array();
        $nums  = array();
        foreach ($row as $qr) {
            array_push($dates, $qr['date_id']);
            array_push($nums, intval($qr['num']));
        }

        $result          = array();
        $result['dates'] = $dates;
        $result['nums']  = $nums;
        echo json_encode($result);

    }//end getAccessData()

}//end class
