<?php

/**
 * GetTreeData.php
 *
 * @category QueryAnalysis
 * @package  App\Http\Controllers\QueryAnalysis
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
namespace App\Http\Controllers\QueryAnalysis;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Common\MyRedis;

/**
 * Class GetTreeData
 *
 * @category QueryAnalysis
 * @package  App\Http\Controllers\QueryAnalysis
 * @author   ericsson <genius@ericsson.com>
 * @license  MIT License
 * @link     https://laravel.com/docs/5.4/controllers
 */
abstract class GetTreeData extends MyRedis
{


    /**
     * 获得树形列表
     *
     * @return mixed
     */
    abstract protected function getTreeData();


}//end class
