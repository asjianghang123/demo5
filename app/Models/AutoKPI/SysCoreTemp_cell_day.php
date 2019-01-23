<?php

namespace App\Models\AutoKPI;

use Illuminate\Database\Eloquent\Model;

class SysCoreTemp_cell_day extends Model
{
    protected $connection = 'autokpi';
    protected $table = 'SysCoreTemp_cell_day';
    public $timestamps = false;

    public function siteLte()
    {
        return $this->hasOne('App\Models\Mongs\SiteLte','cellName','cell');
    }
}