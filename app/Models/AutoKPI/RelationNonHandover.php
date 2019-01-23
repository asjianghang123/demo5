<?php

namespace App\Models\AutoKPI;

use Illuminate\Database\Eloquent\Model;

class RelationNonHandover extends Model
{
    protected $connection = 'autokpi';
    protected $table = 'RelationNonHandover';
    public $timestamps = false;
    protected $visible = ['day_from','day_to','city','subNetwork','cell','EutranCellRelation','切换成功率','同频切换成功率','异频切换成功率','同频准备切换尝试数','同频准备切换成功数','同频执行切换尝试数','同频执行切换成功数','异频准备切换尝试数','异频准备切换成功数','异频执行切换尝试数','异频执行切换成功数','准备切换成功率','执行切换成功率','准备切换尝试数','准备切换成功数','准备切换失败数','执行切换尝试数','执行切换成功数','执行切换失败数','mlongitude','mlatitude','mdir','mband','slongitude','slatitude','sdir','sband','scell','distince'];
}