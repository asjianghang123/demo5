<?php

/**
 * LocalizationUtil.php
 *
 * @category Utils
 * @package  App\Http\Controllers\Utils
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
namespace App\Http\Controllers\Utils;

/**
 * 文件工具类
 * Class FileUtil
 *
 * @category Utils
 * @package  App\Http\Controllers\Utils
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
class LocalizationUtil
{


     /**
     * 城市名称本地化
     *
     * @return array
     */
    function localization($result)
    {
        $newResult = array();
        foreach ($result as $item) {
            $item->city = trans('message.city.'.$item->city);
            array_push($newResult, $item);
        }
        return $newResult;
    }//end localization()

}//end class
