<?php

/**
 * DateUtil.php
 *
 * @category Utils
 * @package  App\Http\Controllers\Utils
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
namespace App\Http\Controllers\Utils;
use Illuminate\Support\Facades\Redis;
use PDO;

/**
 * 日期工具类
 * Class DateUtil
 *
 * @category Utils
 * @package  App\Http\Controllers\Utils
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class DateUtil
{


    /**
     * 获得有数据的日期列表(天)
     *
     * @return array 日期列表(天)
     */
    function getDateListWithData($db,$key,$sql)
    {
        //print_r(Redis::ttl($key));
        if (Redis::exists($key)) {
            return json_decode(Redis::get($key));
        } else {
            //print_r($key.' 不存在');
            $result = array();
            //$sql    = "select distinct datetime_id from $table";
            $rs     = $db->query($sql);
            $test   = [];
            if ($rs) {
                $rows = $rs->fetchall();
                if (count($rows) > 0) {
                    foreach ($rows as $row) {
                        $arr = explode(' ', $row[0]);
                        if ($arr[0] == '0000-00-00') {
                            continue;
                        }

                        array_push($test, $arr[0]);
                    }
                    Redis::set($key, json_encode($test));
                    Redis::expire($key, 36000);//设置改key的过期时间（单位s）
                    return $test;
                } else {
                    $result['error'] = 'error';
                    return $result;
                }
            } else {
                $result['error'] = 'error';
                return $result;
            }//end if
        }
        

    }//end getDateListWithData()


}//end class
