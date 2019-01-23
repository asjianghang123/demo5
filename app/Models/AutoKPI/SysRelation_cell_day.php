<?php

namespace App\Models\AutoKPI;

use Illuminate\Database\Eloquent\Model;

class SysRelation_cell_day extends Model
{
    protected $connection = 'autokpi';
    protected $table = 'SysRelation_cell_day';
    public $timestamps = false;
}