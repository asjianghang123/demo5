<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2018/7/4
 * Time: 17:04
 */

namespace App\Http\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class SubnetWorkFacades
 * @package App\Http\Facades
 */
class SubnetWorkFacades extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'SubnetWork';
    }
}