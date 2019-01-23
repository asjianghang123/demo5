<?php

namespace App\Models\AutoKPI;

use Illuminate\Database\Eloquent\Model;

class HighLostCell extends Model
{
    protected $connection = 'autokpi';
    protected $table = 'highLostCell';
    public $timestamps = false;
}