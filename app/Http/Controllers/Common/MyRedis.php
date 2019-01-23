<?php

/**
 * MyRedis.php
 *
 * @category Common
 * @package  App\Http\Controllers\Common
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
namespace App\Http\Controllers\Common;
use Illuminate\Support\Facades\Redis;
use App\Http\Controllers\Controller;


/**
 * Class MyRedis
 *
 * @category QueryAnalysis
 * @package  App\Http\Controllers\Common
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
abstract class MyRedis extends Controller
{


    protected $type;
   
    protected function getValue($db, $sql)
    {
        return  Redis::exists($this->type) ? json_decode(Redis::get($this->type)) : $this->updateValue($db, $sql);

       }//end getValue()
       
       protected function updateValue($db, $sql){
        $result = array();
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
                Redis::set($this->type, json_encode($test));
                Redis::expire($this->type, 36000);//设置改key的过期时间（单位s）
                return $test;
            } else {
                $result['error'] = 'error';
                return $result;
            }
        } else {
            $result['error'] = 'error';
            return $result;
        }//end if
       }//end updateValue()


}//end class
